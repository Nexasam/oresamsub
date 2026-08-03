<?php

namespace App\Services;

use App\Models\Bonus;
use App\Models\BonusEntitlement;
use App\Models\BonusLog;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BonusService
{
    public function captureRegistrationContext(User $user, Request $request): void
    {
        $user->forceFill([
            'registration_ip' => $this->ip($request),
            'registration_device_hash' => $this->deviceHash($request),
        ])->save();
    }

    public function captureLoginContext(User $user, Request $request): void
    {
        $user->forceFill([
            'last_login_ip' => $this->ip($request),
            'last_login_device_hash' => $this->deviceHash($request),
        ])->save();
    }

    /**
     * Evaluate currently active campaigns. Calling this repeatedly is safe.
     *
     * @return array<int, BonusEntitlement>
     */
    public function evaluate(User $user, ?Request $request = null): array
    {
        if (! $user->hasVerifiedEmail() || (bool) $user->is_deactivated) {
            return [];
        }

        $this->expireCredits($user);
        $granted = [];

        Bonus::query()
            ->available()
            ->orderByDesc('priority')
            ->orderByDesc('created_at')
            ->get()
            ->each(function (Bonus $bonus) use ($user, $request, &$granted) {
                if (BonusEntitlement::query()->where('bonus_id', $bonus->id)->where('user_id', $user->id)->exists()) {
                    return;
                }

                if (! $this->isEligible($bonus, $user)) {
                    return;
                }

                $ip = $user->registration_ip ?: ($request ? $this->ip($request) : null);
                $deviceHash = $user->registration_device_hash ?: ($request ? $this->deviceHash($request) : null);
                $rejection = $this->abuseRejection($bonus, $ip, $deviceHash);

                if ($rejection) {
                    $this->logRejection($bonus, $user, $rejection, $ip, $deviceHash);

                    return;
                }

                $entitlement = $this->grant($bonus, $user, $ip, $deviceHash);
                if ($entitlement) {
                    $granted[] = $entitlement;
                }
            });

        return $granted;
    }

    public function summary(User $user, ?Request $request = null): array
    {
        $this->evaluate($user, $request);
        $user->refresh();

        $rewards = BonusEntitlement::query()
            ->with('bonus')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest('awarded_at')
            ->get()
            ->map(function (BonusEntitlement $entitlement) {
                $bonus = $entitlement->bonus;

                return [
                    'id' => $entitlement->id,
                    'title' => $bonus?->title,
                    'group' => $bonus?->group,
                    'enjoyment' => $bonus?->enjoyment ?? [],
                    'bonus_wallet_remaining' => round((float) $entitlement->bonus_wallet_remaining, 2),
                    'funding_uses_remaining' => max(
                        0,
                        (int) ($bonus?->frequency_per_user ?? 0) - (int) $entitlement->funding_uses
                    ),
                    'expires_at' => $entitlement->expires_at?->toIso8601String(),
                ];
            })
            ->values()
            ->all();

        return [
            'balance' => round((float) $user->bonus_wallet, 2),
            'convertible' => (float) $user->bonus_wallet > 0,
            'active_rewards' => $rewards,
        ];
    }

    public function convertToMainWallet(User $user, ?Request $request = null): array
    {
        $this->expireCredits($user);

        return DB::transaction(function () use ($user, $request) {
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $entitlements = BonusEntitlement::query()
                ->with('bonus')
                ->where('user_id', $lockedUser->id)
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->where('bonus_wallet_remaining', '>', 0)
                ->lockForUpdate()
                ->get();

            $amount = round((float) $lockedUser->bonus_wallet, 2);
            if ($amount <= 0) {
                return [
                    'converted_amount' => 0.0,
                    'main_wallet_balance' => round((float) $lockedUser->main_wallet, 2),
                    'bonus_wallet_balance' => round((float) $lockedUser->bonus_wallet, 2),
                ];
            }

            $mainBefore = round((float) $lockedUser->main_wallet, 2);
            $mainAfter = round($mainBefore + $amount, 2);
            $ip = $request ? $this->ip($request) : null;
            $deviceHash = $request ? $this->deviceHash($request) : null;
            $campaignAmount = 0.0;
            $unallocatedAmount = $amount;

            foreach ($entitlements as $entitlement) {
                $remaining = round((float) $entitlement->bonus_wallet_remaining, 2);
                $converted = round(min($remaining, $unallocatedAmount), 2);
                if ($converted <= 0) {
                    break;
                }

                $campaignAmount += $converted;
                $unallocatedAmount = round($unallocatedAmount - $converted, 2);
                $entitlementBalance = round($remaining - $converted, 2);
                $hasFundingLeft = $this->hasFundingBenefit($entitlement->bonus)
                    && (int) $entitlement->funding_uses < (int) $entitlement->bonus->frequency_per_user;

                $entitlement->update([
                    'bonus_wallet_remaining' => $entitlementBalance,
                    'status' => $hasFundingLeft || $entitlementBalance > 0 ? 'active' : 'exhausted',
                ]);

                BonusLog::create([
                    'bonus_id' => $entitlement->bonus_id,
                    'bonus_entitlement_id' => $entitlement->id,
                    'user_id' => $lockedUser->id,
                    'event_type' => 'wallet_converted',
                    'amount' => $converted,
                    'balance_before' => $mainBefore,
                    'balance_after' => $mainAfter,
                    'ip_address' => $ip,
                    'device_hash' => $deviceHash,
                    'event_key' => $this->eventKey('wallet-converted', $entitlement->id),
                    'metadata' => ['destination' => 'main_wallet'],
                ]);
            }

            $externalBonusAmount = round(max(0, $amount - $campaignAmount), 2);
            if ($externalBonusAmount > 0) {
                BonusLog::create([
                    'bonus_id' => null,
                    'bonus_entitlement_id' => null,
                    'user_id' => $lockedUser->id,
                    'event_type' => 'external_bonus_converted',
                    'amount' => $externalBonusAmount,
                    'balance_before' => $mainBefore,
                    'balance_after' => $mainAfter,
                    'ip_address' => $ip,
                    'device_hash' => $deviceHash,
                    'event_key' => $this->eventKey(
                        'external-wallet-converted',
                        $lockedUser->id,
                        (string) Str::uuid()
                    ),
                    'metadata' => ['destination' => 'main_wallet', 'source' => 'upline_or_external_bonus'],
                ]);
            }

            $lockedUser->update([
                'main_wallet' => $mainAfter,
                'bonus_wallet' => 0,
            ]);

            return [
                'converted_amount' => $amount,
                'main_wallet_balance' => $mainAfter,
                'bonus_wallet_balance' => round((float) $lockedUser->bonus_wallet, 2),
            ];
        });
    }

    public function manuallyGrant(Bonus $bonus, User $user): ?BonusEntitlement
    {
        if (BonusEntitlement::query()->where('bonus_id', $bonus->id)->where('user_id', $user->id)->exists()) {
            return null;
        }

        $entitlement = $this->grant(
            $bonus,
            $user,
            $user->registration_ip,
            $user->registration_device_hash
        );

        if ($entitlement) {
            BonusLog::create([
                'bonus_id' => $bonus->id,
                'bonus_entitlement_id' => $entitlement->id,
                'user_id' => $user->id,
                'event_type' => 'manual_override',
                'amount' => (float) $entitlement->bonus_wallet_awarded,
                'event_key' => $this->eventKey('manual-override', $bonus->id, $user->id),
                'metadata' => ['reason' => 'admin_override'],
            ]);
        }

        return $entitlement;
    }

    /**
     * Apply at most one campaign incentive to one verified, successful funding.
     */
    public function applyFundingReward(
        User $user,
        string $provider,
        float $fundedAmount,
        string $fundingReference,
        float $gatewayCharge,
        float $baseAmountToCredit
    ): array {
        $provider = mb_strtolower(trim($provider));
        $eventKey = $this->eventKey('funding', $provider, $fundingReference);

        if (! $user->hasVerifiedEmail() || (bool) $user->is_deactivated) {
            return ['reward' => 0.0, 'funding_bonus' => 0.0, 'fee_waiver' => 0.0, 'duplicate' => false];
        }

        // Webhooks can arrive before the customer's next login/dashboard request.
        // Ensure any newly available targeted or general campaign is granted first.
        $this->evaluate($user);

        if (BonusLog::query()->where('event_key', $eventKey)->exists()) {
            return ['reward' => 0.0, 'funding_bonus' => 0.0, 'fee_waiver' => 0.0, 'duplicate' => true];
        }

        $candidate = BonusEntitlement::query()
            ->with('bonus')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->whereHas('bonus', fn ($query) => $query->where('status', true))
            ->get()
            ->filter(function (BonusEntitlement $entitlement) use ($provider) {
                $bonus = $entitlement->bonus;
                $whitelist = array_map('mb_strtolower', $bonus->funding_whitelist ?? []);

                return $this->hasFundingBenefit($bonus)
                    && (int) $entitlement->funding_uses < (int) $bonus->frequency_per_user
                    && ($whitelist === [] || in_array($provider, $whitelist, true));
            })
            ->sortByDesc(fn (BonusEntitlement $entitlement) => [
                (int) $entitlement->bonus->targets($user),
                (int) $entitlement->bonus->priority,
                $entitlement->awarded_at?->getTimestamp() ?? 0,
            ])
            ->first();

        if (! $candidate) {
            return ['reward' => 0.0, 'funding_bonus' => 0.0, 'fee_waiver' => 0.0, 'duplicate' => false];
        }

        return DB::transaction(function () use (
            $candidate,
            $user,
            $provider,
            $fundedAmount,
            $fundingReference,
            $gatewayCharge,
            $baseAmountToCredit,
            $eventKey
        ) {
            $entitlement = BonusEntitlement::query()->whereKey($candidate->id)->lockForUpdate()->firstOrFail();
            $bonus = Bonus::query()->whereKey($entitlement->bonus_id)->lockForUpdate()->firstOrFail();

            if (
                ! $bonus->status
                || $entitlement->expires_at->isPast()
                || (int) $entitlement->funding_uses >= (int) $bonus->frequency_per_user
                || BonusLog::query()->where('event_key', $eventKey)->exists()
            ) {
                return ['reward' => 0.0, 'funding_bonus' => 0.0, 'fee_waiver' => 0.0, 'duplicate' => true];
            }

            $fundingBonus = 0.0;
            if ($bonus->includes(Bonus::ENJOYMENT_FUNDING)) {
                $fundingBonus = $bonus->funding_type === 'percent'
                    ? round($fundedAmount * ((float) $bonus->funding_value / 100), 2)
                    : round((float) $bonus->funding_value, 2);

                if ($bonus->funding_type === 'percent' && $bonus->funding_cap !== null) {
                    $fundingBonus = min($fundingBonus, round((float) $bonus->funding_cap, 2));
                }
            }

            $feeWaiver = 0.0;
            if ($bonus->includes(Bonus::ENJOYMENT_FEE_WAIVER)) {
                $uncreditedFunding = max(0, round($fundedAmount - $baseAmountToCredit, 2));
                $feeWaiver = round((float) min(round(max(0, $gatewayCharge), 2), $uncreditedFunding), 2);
            }

            $reward = round($fundingBonus + $feeWaiver, 2);
            $newUses = (int) $entitlement->funding_uses + 1;
            $entitlement->update([
                'funding_uses' => $newUses,
                'status' => $newUses >= (int) $bonus->frequency_per_user
                    && (float) $entitlement->bonus_wallet_remaining <= 0
                        ? 'exhausted'
                        : 'active',
            ]);

            BonusLog::create([
                'bonus_id' => $bonus->id,
                'bonus_entitlement_id' => $entitlement->id,
                'user_id' => $user->id,
                'event_type' => 'funding_reward',
                'amount' => $reward,
                'funding_provider' => $provider,
                'funding_reference' => $fundingReference,
                'event_key' => $eventKey,
                'metadata' => [
                    'funded_amount' => round($fundedAmount, 2),
                    'base_amount_to_credit' => round($baseAmountToCredit, 2),
                    'funding_bonus' => $fundingBonus,
                    'fee_waiver' => $feeWaiver,
                    'use_number' => $newUses,
                ],
            ]);

            return [
                'reward' => $reward,
                'funding_bonus' => $fundingBonus,
                'fee_waiver' => $feeWaiver,
                'duplicate' => false,
                'bonus_id' => $bonus->id,
            ];
        });
    }

    public function expireCredits(User $user): float
    {
        return DB::transaction(function () use ($user) {
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $expired = BonusEntitlement::query()
                ->where('user_id', $lockedUser->id)
                ->where('status', 'active')
                ->where('expires_at', '<=', now())
                ->lockForUpdate()
                ->get();
            $expiredAmount = 0.0;

            foreach ($expired as $entitlement) {
                $amount = round((float) $entitlement->bonus_wallet_remaining, 2);
                $expiredAmount += $amount;
                $entitlement->update(['bonus_wallet_remaining' => 0, 'status' => 'expired']);

                BonusLog::firstOrCreate(
                    ['event_key' => $this->eventKey('expired', $entitlement->id)],
                    [
                        'bonus_id' => $entitlement->bonus_id,
                        'bonus_entitlement_id' => $entitlement->id,
                        'user_id' => $lockedUser->id,
                        'event_type' => 'expired',
                        'amount' => $amount,
                        'metadata' => ['expired_at' => $entitlement->expires_at?->toIso8601String()],
                    ]
                );
            }

            if ($expired->isNotEmpty()) {
                $lockedUser->update([
                    'bonus_wallet' => max(
                        0,
                        round((float) $lockedUser->bonus_wallet - $expiredAmount, 2)
                    ),
                ]);
            }

            return round($expiredAmount, 2);
        });
    }

    private function isEligible(Bonus $bonus, User $user): bool
    {
        if ($bonus->isTargeted()) {
            return $bonus->targets($user);
        }

        if ($bonus->group === Bonus::GROUP_NEW_REGISTRATION) {
            $registrationStart = $bonus->starts_at ?? $bonus->created_at;
            $withinCampaign = $user->created_at->greaterThanOrEqualTo($registrationStart)
                && $user->created_at->lessThanOrEqualTo($bonus->ends_at);
            $maximumAge = $bonus->conditions['registration_max_age_days'] ?? null;

            return $withinCampaign && (
                ! $maximumAge
                || $user->created_at->greaterThanOrEqualTo(now()->subDays((int) $maximumAge))
            );
        }

        if ($bonus->group === Bonus::GROUP_DORMANT_CUSTOMER) {
            $lastTransactionAt = Transaction::query()
                ->where('user_id', $user->id)
                ->where('status', '1')
                ->latest('created_at')
                ->value('created_at');

            if (! $lastTransactionAt) {
                return false;
            }

            $lastTransactionAt = Carbon::parse($lastTransactionAt);
            $beforeDate = $bonus->conditions['last_transaction_before'] ?? null;
            if ($beforeDate) {
                return $lastTransactionAt->lessThanOrEqualTo(Carbon::parse($beforeDate)->endOfDay());
            }

            $dormantDays = max(1, (int) ($bonus->conditions['dormant_days'] ?? 15));

            return $lastTransactionAt->lessThanOrEqualTo(now()->subDays($dormantDays));
        }

        return false;
    }

    private function abuseRejection(Bonus $bonus, ?string $ip, ?string $deviceHash): ?string
    {
        if ($bonus->isTargeted()) {
            return null;
        }

        if ($bonus->group !== Bonus::GROUP_NEW_REGISTRATION) {
            return null;
        }

        if ($ip && $bonus->max_rewards_per_ip !== null) {
            $count = BonusEntitlement::query()
                ->where('bonus_id', $bonus->id)
                ->where('registration_ip', $ip)
                ->distinct('user_id')
                ->count('user_id');
            if ($count >= (int) $bonus->max_rewards_per_ip) {
                return 'ip_reward_limit_reached';
            }
        }

        if ($deviceHash && $bonus->max_rewards_per_device !== null) {
            $count = BonusEntitlement::query()
                ->where('bonus_id', $bonus->id)
                ->where('device_hash', $deviceHash)
                ->distinct('user_id')
                ->count('user_id');
            if ($count >= (int) $bonus->max_rewards_per_device) {
                return 'device_reward_limit_reached';
            }
        }

        return null;
    }

    private function grant(
        Bonus $campaign,
        User $user,
        ?string $ip,
        ?string $deviceHash
    ): ?BonusEntitlement {
        return DB::transaction(function () use ($campaign, $user, $ip, $deviceHash) {
            $bonus = Bonus::query()->whereKey($campaign->id)->lockForUpdate()->firstOrFail();
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();

            if (
                ! $bonus->status
                || $bonus->ends_at->isPast()
                || BonusEntitlement::query()->where('bonus_id', $bonus->id)->where('user_id', $lockedUser->id)->exists()
            ) {
                return null;
            }

            $expiresAt = $bonus->reward_valid_days
                ? now()->addDays((int) $bonus->reward_valid_days)
                : $bonus->ends_at->copy();

            $walletAmount = $bonus->includes(Bonus::ENJOYMENT_WALLET)
                ? round((float) $bonus->bonus_wallet_amount, 2)
                : 0.0;
            $bonusBefore = round((float) $lockedUser->bonus_wallet, 2);
            $bonusAfter = round($bonusBefore + $walletAmount, 2);

            $entitlement = BonusEntitlement::create([
                'bonus_id' => $bonus->id,
                'user_id' => $lockedUser->id,
                'status' => 'active',
                'registration_ip' => $ip,
                'device_hash' => $deviceHash,
                'bonus_wallet_awarded' => $walletAmount,
                'bonus_wallet_remaining' => $walletAmount,
                'funding_uses' => 0,
                'awarded_at' => now(),
                'expires_at' => $expiresAt,
            ]);

            if ($walletAmount > 0) {
                $lockedUser->update(['bonus_wallet' => $bonusAfter]);
            }

            BonusLog::create([
                'bonus_id' => $bonus->id,
                'bonus_entitlement_id' => $entitlement->id,
                'user_id' => $lockedUser->id,
                'event_type' => 'entitlement_granted',
                'amount' => $walletAmount,
                'balance_before' => $bonusBefore,
                'balance_after' => $bonusAfter,
                'ip_address' => $ip,
                'device_hash' => $deviceHash,
                'event_key' => $this->eventKey('granted', $bonus->id, $lockedUser->id),
                'metadata' => [
                    'group' => $bonus->group,
                    'enjoyment' => $bonus->enjoyment,
                    'expires_at' => $expiresAt->toIso8601String(),
                ],
            ]);

            return $entitlement;
        });
    }

    private function logRejection(
        Bonus $bonus,
        User $user,
        string $reason,
        ?string $ip,
        ?string $deviceHash
    ): void {
        BonusLog::firstOrCreate(
            ['event_key' => $this->eventKey('rejected', $bonus->id, $user->id)],
            [
                'bonus_id' => $bonus->id,
                'user_id' => $user->id,
                'event_type' => 'eligibility_rejected',
                'amount' => 0,
                'ip_address' => $ip,
                'device_hash' => $deviceHash,
                'metadata' => ['reason' => $reason],
            ]
        );
    }

    private function hasFundingBenefit(?Bonus $bonus): bool
    {
        return $bonus && (
            $bonus->includes(Bonus::ENJOYMENT_FUNDING)
            || $bonus->includes(Bonus::ENJOYMENT_FEE_WAIVER)
        );
    }

    private function ip(Request $request): ?string
    {
        $ip = $request->ip();

        return $ip && filter_var($ip, FILTER_VALIDATE_IP) ? $ip : null;
    }

    private function deviceHash(Request $request): ?string
    {
        $deviceId = trim((string) $request->header('X-Device-ID', ''));

        return $deviceId !== '' && mb_strlen($deviceId) <= 200
            ? hash('sha256', $deviceId)
            : null;
    }

    private function eventKey(string ...$parts): string
    {
        $prefix = array_shift($parts);

        return $prefix.':'.hash('sha256', implode('|', $parts));
    }
}
