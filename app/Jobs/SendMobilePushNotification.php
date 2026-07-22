<?php

namespace App\Jobs;

use App\Models\MobileDeviceInstallation;
use App\Models\MobileNotificationPreference;
use App\Models\MobilePushDelivery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class SendMobilePushNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [30, 120, 600];

    public function __construct(public string $userId, public string $eventKey, public string $title, public string $body, public array $data = [], public bool $promotional = false) {}

    public function handle(): void
    {
        $preferences = MobileNotificationPreference::where('user_id', $this->userId)->first();
        if ($this->promotional ? ! ($preferences?->promotional_enabled ?? false) : ! ($preferences?->transactional_enabled ?? true)) {
            return;
        }

        MobileDeviceInstallation::where('user_id', $this->userId)->where('enabled', true)->whereNull('revoked_at')->get()->each(function ($device) {
            $delivery = MobilePushDelivery::firstOrCreate(
                ['mobile_device_installation_id' => $device->id, 'event_key' => $this->eventKey],
                ['category' => $this->promotional ? 'promotional' : 'transactional']
            );
            if (! $delivery->wasRecentlyCreated && in_array($delivery->status, ['queued', 'sent'], true)) {
                return;
            }

            $response = Http::timeout(10)->post('https://exp.host/--/api/v2/push/send', [
                'to' => $device->expo_push_token, 'title' => $this->title, 'body' => $this->body,
                'sound' => 'default', 'channelId' => 'transactions', 'data' => $this->data,
            ]);
            $ticket = $response->json('data');
            $errorCode = $ticket['details']['error'] ?? null;
            $delivery->update(['status' => $response->successful() && ($ticket['status'] ?? null) === 'ok' ? 'sent' : 'failed', 'expo_ticket_id' => $ticket['id'] ?? null, 'error_code' => $errorCode, 'attempted_at' => now()]);
            if ($errorCode === 'DeviceNotRegistered') {
                $device->update(['enabled' => false, 'revoked_at' => now()]);
            }
            if (! $response->successful()) {
                $response->throw();
            }
            if (($ticket['status'] ?? null) !== 'ok' && $errorCode !== 'DeviceNotRegistered') {
                throw new \RuntimeException('Expo rejected the push notification.');
            }
        });
    }
}
