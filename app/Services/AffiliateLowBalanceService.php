<?php

namespace App\Services;

use App\Mail\AffiliateLowBalanceMail;
use App\Models\AffiliateFundingAttempt;
use App\Models\AffiliateWalletSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AffiliateLowBalanceService
{
    public function __construct(
        private readonly AffiliateFundingTransferService $transfers
    ) {}

    public function run(): array
    {
        $checked = 0;
        $notified = 0;
        $failed = 0;

        AffiliateWalletSetting::query()
            ->with('user')
            ->where('enabled', true)
            ->orderBy('id')
            ->chunk(200, function ($settings) use (&$checked, &$notified, &$failed) {
                foreach ($settings as $setting) {
                    $checked++;
                    $result = $this->process($setting);
                    $notified += $result === 'notified' ? 1 : 0;
                    $failed += $result === 'failed' ? 1 : 0;
                }
            });

        return compact('checked', 'notified', 'failed');
    }

    public function process(AffiliateWalletSetting $setting): string
    {
        $user = $setting->user;
        if (! $setting->enabled || ! $user || (bool) $user->is_deactivated) {
            return 'skipped';
        }

        $balance = round((float) $user->main_wallet, 2);
        $threshold = round((float) $setting->funding_threshold, 2);
        $setting->forceFill(['last_checked_at' => now()])->save();

        if ($balance >= $threshold || $setting->last_notified_on?->isToday()) {
            return 'skipped';
        }

        $attempt = DB::transaction(function () use ($setting, $user, $balance, $threshold) {
            $locked = AffiliateWalletSetting::query()->whereKey($setting->id)->lockForUpdate()->firstOrFail();
            if ($locked->last_notified_on?->isToday()) {
                return null;
            }

            $locked->update(['last_notified_on' => today()]);

            return AffiliateFundingAttempt::create([
                'affiliate_wallet_setting_id' => $locked->id,
                'user_id' => $user->id,
                'status' => $locked->automatic_transfer_enabled
                    ? 'awaiting_transfer_integration'
                    : 'notification_pending',
                'wallet_balance' => $balance,
                'funding_threshold' => $threshold,
                'requested_amount' => $locked->funding_amount,
                'provider' => $locked->transfer_provider,
                'metadata' => [
                    'automatic_transfer_requested' => (bool) $locked->automatic_transfer_enabled,
                    'account_configured' => filled($locked->funding_account_number),
                ],
                'triggered_at' => now(),
            ]);
        });

        if (! $attempt) {
            return 'skipped';
        }

        if ($setting->automatic_transfer_enabled) {
            $transfer = $this->transfers->initiate($attempt);
            $attempt->update([
                'status' => $transfer['status'],
                'provider_reference' => $transfer['provider_reference'],
                'failure_reason' => $transfer['message'],
            ]);
        }

        try {
            $recipient = $setting->notification_email ?: $user->email;
            $mail = Mail::to($recipient);
            $adminCopy = $setting->admin_copy_email ?: config('mail.from.address');
            if ($adminCopy && strcasecmp($adminCopy, $recipient) !== 0) {
                $mail->cc($adminCopy);
            }
            $mail->send(new AffiliateLowBalanceMail($setting->loadMissing('user'), $balance));

            if (! $setting->automatic_transfer_enabled) {
                $attempt->update(['status' => 'notification_sent']);
            }

            return 'notified';
        } catch (Throwable $exception) {
            report($exception);
            $attempt->update([
                'status' => 'notification_failed',
                'failure_reason' => mb_substr($exception->getMessage(), 0, 1000),
            ]);

            return 'failed';
        }
    }
}
