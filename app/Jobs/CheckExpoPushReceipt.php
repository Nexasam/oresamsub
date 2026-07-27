<?php

namespace App\Jobs;

use App\Models\MobilePushDelivery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CheckExpoPushReceipt implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public array $backoff = [60, 180, 600, 1800];

    public function __construct(public string $deliveryId) {}

    public function handle(): void
    {
        $delivery = MobilePushDelivery::query()->with('device')->find($this->deliveryId);

        if (! $delivery || ! $delivery->expo_ticket_id || in_array($delivery->status, ['delivered', 'failed'], true)) {
            return;
        }

        $response = Http::timeout(10)->post('https://exp.host/--/api/v2/push/getReceipts', [
            'ids' => [$delivery->expo_ticket_id],
        ])->throw();
        $receipt = $response->json('data.'.$delivery->expo_ticket_id);

        if (! is_array($receipt)) {
            throw new RuntimeException('The Expo push receipt is not ready yet.');
        }

        $errorCode = $receipt['details']['error'] ?? null;
        $successful = ($receipt['status'] ?? null) === 'ok';
        $delivery->update([
            'status' => $successful ? 'delivered' : 'failed',
            'error_code' => $errorCode,
            'receipt_checked_at' => now(),
            'delivered_at' => $successful ? now() : null,
        ]);

        if ($errorCode === 'DeviceNotRegistered' && $delivery->device) {
            $delivery->device->update(['enabled' => false, 'revoked_at' => now()]);
        }
    }
}
