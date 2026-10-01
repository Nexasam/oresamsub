<?php

namespace App\Services\Automation;

use App\Mail\AutomationLowBalanceMail;
use App\Models\AutomationLowBalanceAlert;
use App\Models\AutomationWalletFunding;
use App\Services\AdminEmailNotificationRecipients;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AutomationLowBalanceNotificationService
{
    public function __construct(private readonly AdminEmailNotificationRecipients $recipients)
    {
    }

    public function run(int $burstPosition): array
    {
        if (! in_array($burstPosition, [1, 2, 3], true)) {
            throw new \InvalidArgumentException('Low-balance notification burst position must be 1, 2, or 3.');
        }

        $lagosNow = now('Africa/Lagos');
        $cycle = intdiv((int) $lagosNow->format('G'), 3);
        $slot = ($cycle * 3) + $burstPosition;

        $recipients = $this->recipients->emails('automation_low_balance');

        if ($recipients->isEmpty()) {
            return ['notified' => 0, 'failed' => 0];
        }

        $notified = 0;
        $failed = 0;

        AutomationWalletFunding::query()
            ->with('automation')
            ->where('active', 'yes')
            ->whereColumn('last_balance', '<=', 'threshold')
            ->orderBy('id')
            ->chunkById(100, function ($fundings) use ($lagosNow, $slot, $recipients, &$notified, &$failed): void {
                foreach ($fundings as $funding) {
                    $alert = AutomationLowBalanceAlert::query()->firstOrCreate([
                        'automation_wallet_funding_id' => $funding->id,
                        'alert_date' => $lagosNow->toDateString(),
                        'slot' => $slot,
                    ]);

                    if (! $alert->wasRecentlyCreated || $recipients->isEmpty()) {
                        continue;
                    }

                    try {
                        Mail::to($recipients->all())->send(new AutomationLowBalanceMail($funding));
                        $alert->update(['sent_at' => now()]);
                        $notified++;
                    } catch (Throwable $exception) {
                        report($exception);
                        $alert->delete();
                        $failed++;
                    }
                }
            });

        return compact('notified', 'failed');
    }
}
