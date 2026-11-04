<?php

namespace App\Services\Whatsapp;

use App\Models\User;
use App\Models\WhatsappNotificationDelivery;
use App\Models\WhatsappNotificationPreference;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class WhatsappNotificationWebhookHandler
{
    public function __construct(private WhatsappPhoneNumber $phoneNumbers) {}

    public function recordDeliveryStatuses(array $payload): void
    {
        // WhatsApp bot traffic must keep working while a deployment is between
        // the code-release and migration steps. Delivery tracking is optional.
        if (! Schema::hasTable('whatsapp_notification_deliveries')) {
            return;
        }

        foreach ((array) data_get($payload, 'entry', []) as $entry) {
            foreach ((array) data_get($entry, 'changes', []) as $change) {
                foreach ((array) data_get($change, 'value.statuses', []) as $statusUpdate) {
                    $messageId = data_get($statusUpdate, 'id');
                    $metaStatus = data_get($statusUpdate, 'status');

                    if (! $messageId || ! in_array($metaStatus, ['sent', 'delivered', 'read', 'failed'], true)) {
                        continue;
                    }

                    $delivery = WhatsappNotificationDelivery::where('meta_message_id', $messageId)->first();
                    if (! $delivery) {
                        continue;
                    }

                    $rank = ['queued' => 0, 'processing' => 1, 'sent' => 2, 'delivered' => 3, 'read' => 4];
                    if ($metaStatus !== 'failed' && ($rank[$metaStatus] ?? 0) < ($rank[$delivery->status] ?? 0)) {
                        continue;
                    }

                    $updates = ['status' => $metaStatus];
                    if ($metaStatus === 'delivered') {
                        $updates['delivered_at'] = now();
                    } elseif ($metaStatus === 'read') {
                        $updates['delivered_at'] = $delivery->delivered_at ?: now();
                        $updates['read_at'] = now();
                    } elseif ($metaStatus === 'failed') {
                        $updates['failure_code'] = (string) (data_get($statusUpdate, 'errors.0.code') ?: 'delivery_failed');
                        $updates['failure_message'] = Str::limit(strip_tags((string) (
                            data_get($statusUpdate, 'errors.0.error_data.details')
                            ?: data_get($statusUpdate, 'errors.0.title')
                            ?: 'Meta reported that the WhatsApp notification failed.'
                        )), 500, '…');
                    }

                    $delivery->update($updates);
                }
            }
        }
    }

    public function handleInboundPreference(?string $phone, ?string $message): array
    {
        if (blank($phone) || blank($message)) {
            return ['handled' => false, 'reply' => null];
        }

        // Do not allow the optional notification-preference feature to take the
        // main bot offline if its migration has not run yet. The message will
        // continue through the normal WhatsApp conversation flow.
        if (! Schema::hasTable('whatsapp_notification_preferences')) {
            return ['handled' => false, 'reply' => null];
        }

        $user = $this->resolveUser($phone);
        if ($user) {
            WhatsappNotificationPreference::updateOrCreate(
                ['user_id' => $user->id],
                ['last_inbound_at' => now()]
            );
        }

        $command = $this->preferenceCommand($message);
        if ($command === null) {
            return ['handled' => false, 'reply' => null];
        }

        if (! $user) {
            return [
                'handled' => true,
                'reply' => 'Please link this WhatsApp number to your OresamSub account before enabling transaction alerts.',
            ];
        }

        WhatsappNotificationPreference::updateOrCreate(
            ['user_id' => $user->id],
            $command
                ? [
                    'transactional_enabled' => true,
                    'consented_at' => now(),
                    'consent_source' => 'whatsapp_command',
                    'opted_out_at' => null,
                    'last_inbound_at' => now(),
                ]
                : [
                    'transactional_enabled' => false,
                    'opted_out_at' => now(),
                    'last_inbound_at' => now(),
                ]
        );

        return [
            'handled' => true,
            'reply' => $command
                ? '✅ WhatsApp alerts are now ON. We will notify you when an OresamSub transaction fails or is refunded. Reply STOP anytime to turn them off.'
                : 'WhatsApp transaction alerts are now OFF. Reply NOTIFICATIONS ON anytime to enable them again.',
        ];
    }

    private function resolveUser(string $phone): ?User
    {
        $variants = $this->phoneNumbers->lookupVariants($phone);
        if ($variants === []) {
            return null;
        }

        return User::whereIn('whatsapp_number', $variants)->first()
            ?? User::whereIn('phone_number', $variants)->first();
    }

    private function preferenceCommand(string $message): ?bool
    {
        $command = Str::lower(trim($message));

        if (in_array($command, [
            'notifications on',
            'notification on',
            'alerts on',
            'transaction alerts on',
        ], true)) {
            return true;
        }

        if (in_array($command, [
            'stop',
            'unsubscribe',
            'notifications off',
            'notification off',
            'alerts off',
            'transaction alerts off',
        ], true)) {
            return false;
        }

        return null;
    }
}
