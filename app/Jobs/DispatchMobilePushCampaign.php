<?php

namespace App\Jobs;

use App\Models\MobileDeviceInstallation;
use App\Models\MobilePushCampaign;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class DispatchMobilePushCampaign implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [60, 300];

    public function __construct(public string $campaignId) {}

    public function handle(): void
    {
        $campaign = MobilePushCampaign::find($this->campaignId);

        if (! $campaign || $campaign->status === 'dispatched') {
            return;
        }

        $eligibleDevices = MobileDeviceInstallation::query()
            ->where('enabled', true)
            ->whereNull('revoked_at');
        $this->applyDeviceAudience($eligibleDevices, $campaign);

        $campaign->update([
            'status' => 'sending',
            'recipient_users' => (clone $eligibleDevices)->distinct()->count('user_id'),
            'recipient_devices' => (clone $eligibleDevices)->count(),
            'failure_message' => null,
        ]);

        $users = User::query()
            ->select('id')
            ->whereHas('mobileDeviceInstallations', fn ($query) => $query
                ->where('enabled', true)
                ->whereNull('revoked_at'));
        $this->applyUserAudience($users, $campaign);

        $users
            ->orderBy('id')
            ->chunkById(200, function ($users) use ($campaign): void {
                foreach ($users as $user) {
                    SendMobilePushNotification::dispatch(
                        (string) $user->id,
                        $campaign->eventKey(),
                        $campaign->title,
                        $campaign->message,
                        [
                            'screen' => $campaign->destination,
                            'campaign_id' => (string) $campaign->id,
                        ],
                        false,
                        'broadcast',
                    );
                }
            });

        $campaign->update([
            'status' => 'dispatched',
            'dispatched_at' => now(),
        ]);
    }

    private function applyDeviceAudience($query, MobilePushCampaign $campaign): void
    {
        if ($campaign->audience_type === 'user_plan') {
            $query->whereHas('user', fn ($user) => $user->where('user_plan_id', $campaign->audience_value));
        }

        if ($campaign->audience_type === 'salespersons') {
            $query->whereHas('user', fn ($user) => $user->where('is_salesperson', true));
        }
    }

    private function applyUserAudience($query, MobilePushCampaign $campaign): void
    {
        if ($campaign->audience_type === 'user_plan') {
            $query->where('user_plan_id', $campaign->audience_value);
        }

        if ($campaign->audience_type === 'salespersons') {
            $query->where('is_salesperson', true);
        }
    }

    public function failed(Throwable $exception): void
    {
        MobilePushCampaign::whereKey($this->campaignId)->update([
            'status' => 'failed',
            'failure_message' => mb_substr($exception->getMessage(), 0, 1000),
        ]);
    }
}
