<?php

namespace App\Http\Controllers\Api\v1\VendorUsersApi;

use App\Http\Controllers\Controller;
use App\Services\Whatsapp\MegaWhatsappConversationService;
use App\Services\Whatsapp\MegaWhatsappService;
use App\Services\Whatsapp\WhatsappNotificationWebhookHandler;
use Illuminate\Http\Request;
use Throwable;

class MegaWhatsappWebhookController extends Controller
{
    public function webhook(
        Request $request,
        MegaWhatsappConversationService $conversationService,
        MegaWhatsappService $whatsapp,
        WhatsappNotificationWebhookHandler $notificationHandler,
    ) {
        try {

            $payload = $request->all();

            $notificationHandler->recordDeliveryStatuses($payload);

            $phone = $this->extractPhone($payload);

            $message = $this->extractMessage($payload);

            logger()->info(
                'Mega WhatsApp Incoming',
                [
                    'phone_last_four' => substr((string) $phone, -4),
                    'has_message' => filled($message),
                ]
            );

            /*
             * Ignore delivery/read/status webhooks
             */
            if (
                empty($phone) ||
                empty($message)
            ) {
                return response()->json([
                    'success' => true,
                ]);
            }

            $preference = $notificationHandler->handleInboundPreference($phone, $message);
            if ($preference['handled']) {
                $whatsapp->sendText($phone, $preference['reply']);

                return response()->json(['success' => true]);
            }

            $conversationService->handle([
                'phone' => $phone,
                'message' => $message,
            ]);

            return response()->json([
                'success' => true,
            ]);

        } catch (Throwable $exception) {

            logger()->error(
                'Mega WhatsApp Webhook Error',
                [
                    'message' => $exception->getMessage(),
                    'event_kind' => data_get($request->all(), 'entry.0.changes.0.field'),
                ]
            );

            return response()->json([
                'success' => false,
            ], 500);
        }
    }

    private function extractPhone(array $payload): ?string
    {
        return data_get(
            $payload,
            'entry.0.changes.0.value.messages.0.from'
        );
    }

    private function extractMessage(array $payload): ?string
    {
        /*
         * Normal text message
         */
        $text = data_get(
            $payload,
            'entry.0.changes.0.value.messages.0.text.body'
        );

        if (! empty($text)) {
            return strtolower(
                trim($text)
            );
        }

        /*
         * Interactive button reply
         */
        $buttonId = data_get(
            $payload,
            'entry.0.changes.0.value.messages.0.interactive.button_reply.id'
        );

        if (! empty($buttonId)) {
            return strtolower(
                trim($buttonId)
            );
        }

        /*
         * Interactive list reply
         */
        $listId = data_get(
            $payload,
            'entry.0.changes.0.value.messages.0.interactive.list_reply.id'
        );

        if (! empty($listId)) {
            return strtolower(
                trim($listId)
            );
        }

        /*
         * Shared contact
         */
        $contactPhone = data_get(
            $payload,
            'entry.0.changes.0.value.messages.0.contacts.0.phones.0.phone'
        );

        if (! empty($contactPhone)) {
            return preg_replace(
                '/\D/',
                '',
                $contactPhone
            );
        }

        return null;
    }
}
