<?php

namespace App\Services;

use App\Models\UplineFundingBonusLog;
use App\Models\UplineFundingBonusSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UplineFundingBonusService
{
    public function apply(
        User $downline,
        string $provider,
        float $fundedAmount,
        string $fundingReference
    ): array {
        $provider = mb_strtolower(trim($provider));
        $uplineId = $downline->upline_id;

        if (! $uplineId || $uplineId === $downline->id || (bool) $downline->is_deactivated) {
            return $this->emptyResult();
        }

        $setting = UplineFundingBonusSetting::query()
            ->available()
            ->where('user_id', $uplineId)
            ->first();

        if (! $setting) {
            return $this->emptyResult();
        }

        $whitelist = array_map('mb_strtolower', $setting->funding_whitelist ?? []);
        if ($whitelist !== [] && ! in_array($provider, $whitelist, true)) {
            return $this->emptyResult();
        }

        $eventKey = 'upline-funding:'.hash('sha256', implode('|', [
            $setting->id,
            $provider,
            $fundingReference,
        ]));

        if (UplineFundingBonusLog::where('event_key', $eventKey)->exists()) {
            return $this->emptyResult(duplicate: true);
        }

        return DB::transaction(function () use (
            $setting,
            $downline,
            $provider,
            $fundedAmount,
            $fundingReference,
            $eventKey
        ) {
            $lockedSetting = UplineFundingBonusSetting::query()
                ->whereKey($setting->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                ! $lockedSetting->enabled
                || ($lockedSetting->starts_at && $lockedSetting->starts_at->isFuture())
                || ($lockedSetting->ends_at && $lockedSetting->ends_at->isPast())
                || UplineFundingBonusLog::where('event_key', $eventKey)->exists()
            ) {
                return $this->emptyResult(duplicate: true);
            }

            $uses = UplineFundingBonusLog::query()
                ->where('upline_funding_bonus_setting_id', $lockedSetting->id)
                ->where('downline_id', $downline->id)
                ->count();

            if ($uses >= (int) $lockedSetting->frequency_per_downline) {
                return $this->emptyResult();
            }

            $upline = User::query()->whereKey($lockedSetting->user_id)->lockForUpdate()->first();
            if (! $upline || (bool) $upline->is_deactivated) {
                return $this->emptyResult();
            }

            $bonus = $lockedSetting->reward_type === 'percent'
                ? round($fundedAmount * ((float) $lockedSetting->reward_value / 100), 2)
                : round((float) $lockedSetting->reward_value, 2);

            if ($lockedSetting->reward_type === 'percent' && $lockedSetting->reward_cap !== null) {
                $bonus = min($bonus, round((float) $lockedSetting->reward_cap, 2));
            }

            if ($bonus <= 0) {
                return $this->emptyResult();
            }

            $before = round((float) $upline->bonus_wallet, 2);
            $after = round($before + $bonus, 2);
            $sequence = $uses + 1;
            $upline->update(['bonus_wallet' => $after]);

            UplineFundingBonusLog::create([
                'upline_funding_bonus_setting_id' => $lockedSetting->id,
                'upline_id' => $upline->id,
                'downline_id' => $downline->id,
                'funding_provider' => $provider,
                'funding_reference' => $fundingReference,
                'funded_amount' => round($fundedAmount, 2),
                'bonus_amount' => $bonus,
                'bonus_balance_before' => $before,
                'bonus_balance_after' => $after,
                'sequence' => $sequence,
                'event_key' => $eventKey,
                'metadata' => [
                    'reward_type' => $lockedSetting->reward_type,
                    'reward_value' => (float) $lockedSetting->reward_value,
                    'reward_cap' => $lockedSetting->reward_cap !== null
                        ? (float) $lockedSetting->reward_cap
                        : null,
                ],
            ]);

            return [
                'reward' => $bonus,
                'upline_id' => $upline->id,
                'sequence' => $sequence,
                'duplicate' => false,
            ];
        });
    }

    private function emptyResult(bool $duplicate = false): array
    {
        return [
            'reward' => 0.0,
            'upline_id' => null,
            'sequence' => null,
            'duplicate' => $duplicate,
        ];
    }
}
