<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\WhatsappNotificationDelivery;
use App\Models\WhatsappNotificationPreference;
use App\Services\Whatsapp\MegaWhatsappService;
use App\Services\Whatsapp\TransactionWhatsappMessage;
use App\Services\Whatsapp\WhatsappPhoneNumber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;
use Throwable;

class SendWhatsappTransactionAlert implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(
        public string $transactionId,
        public string $notificationType,
    ) {}

    public function handle(
        MegaWhatsappService $whatsapp,
        WhatsappPhoneNumber $phoneNumbers,
        TransactionWhatsappMessage $messageBuilder,
    ): void {
        if (! in_array($this->notificationType, ['failed', 'refunded'], true)) {
            return;
        }

        $transaction = Transaction::with(['user', 'product_plan'])->find($this->transactionId);
        if (! $transaction?->user) {
            return;
        }

        $eventKey = "transaction:{$transaction->id}:{$this->notificationType}";
        $delivery = WhatsappNotificationDelivery::firstOrCreate(
            ['event_key' => $eventKey],
            [
                'user_id' => $transaction->user_id,
                'transaction_id' => $transaction->id,
                'notification_type' => $this->notificationType,
                'status' => 'queued',
            ]
        );

        if (in_array($delivery->status, ['processing', 'sent', 'delivered', 'read'], true)) {
            return;
        }

        $claimed = WhatsappNotificationDelivery::whereKey($delivery->id)
            ->whereIn('status', ['queued', 'failed', 'skipped'])
            ->update([
                'status' => 'processing',
                'attempted_at' => now(),
                'failure_code' => null,
                'failure_message' => null,
            ]);

        if ($claimed !== 1) {
            return;
        }

        $expectedStatus = $this->notificationType === 'refunded' ? '2' : '-1';
        if ((string) $transaction->status !== $expectedStatus) {
            $this->skip($delivery, 'stale_status', 'Transaction status changed before notification dispatch.');

            return;
        }

        $preference = WhatsappNotificationPreference::where('user_id', $transaction->user_id)->first();
        if (! ($preference?->transactional_enabled ?? false)) {
            $this->skip($delivery, 'not_opted_in', 'Customer has not enabled WhatsApp transaction alerts.');

            return;
        }

        $phone = $phoneNumbers->toMeta($transaction->user->whatsapp_number);
        if (! $phone) {
            $this->skip($delivery, 'missing_whatsapp_number', 'No valid linked WhatsApp number is available.');

            return;
        }

        $message = $messageBuilder->build($transaction, $this->notificationType);
        $serviceWindowHours = max(1, (int) config('whatsapp_notifications.transaction_alerts.service_window_hours', 24));
        $insideServiceWindow = $preference->last_inbound_at?->gte(now()->subHours($serviceWindowHours)) ?? false;
        $templateName = $this->notificationType === 'refunded'
            ? (string) config('whatsapp_notifications.transaction_alerts.refunded_template')
            : (string) config('whatsapp_notifications.transaction_alerts.failed_template');

        $delivery->update([
            'payload_type' => $insideServiceWindow ? 'text' : 'template',
            'template_name' => $insideServiceWindow ? null : $templateName,
            'recipient_last_four' => substr($phone, -4),
        ]);

        try {
            $response = $insideServiceWindow
                ? $whatsapp->sendText($phone, $message['text'])
                : $whatsapp->sendTemplate(
                    $phone,
                    $templateName,
                    $message['parameters'],
                    (string) config('whatsapp_notifications.transaction_alerts.language', 'en')
                );
        } catch (Throwable $exception) {
            // A connection failure after dispatch is ambiguous. Do not retry and
            // risk sending the customer the same notification more than once.
            $delivery->update([
                'status' => 'indeterminate',
                'failure_code' => 'transport_error',
                'failure_message' => Str::limit($exception->getMessage(), 500, '…'),
            ]);

            report($exception);

            return;
        }

        $messageId = data_get($response, 'messages.0.id');
        if (filled($messageId)) {
            $delivery->update([
                'status' => 'sent',
                'meta_message_id' => (string) $messageId,
            ]);

            return;
        }

        $delivery->update([
            'status' => 'failed',
            'failure_code' => (string) (data_get($response, 'error.code') ?: 'meta_rejected'),
            'failure_message' => Str::limit(
                (string) (data_get($response, 'error.message') ?: 'Meta did not accept the WhatsApp notification.'),
                500,
                '…'
            ),
        ]);
    }

    private function skip(WhatsappNotificationDelivery $delivery, string $code, string $message): void
    {
        $delivery->update([
            'status' => 'skipped',
            'failure_code' => $code,
            'failure_message' => $message,
        ]);
    }
}
