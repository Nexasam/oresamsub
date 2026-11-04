<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsappTransactionAlert;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WhatsappNotificationDelivery;
use App\Models\WhatsappNotificationPreference;
use App\Services\Whatsapp\MegaWhatsappService;
use App\Services\Whatsapp\WhatsappPhoneNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class WhatsappMessageController extends Controller
{
    public function index(Request $request): View
    {
        $windowStart = now()->subHours(
            max(1, (int) config('whatsapp_notifications.transaction_alerts.service_window_hours', 24))
        );

        $eligiblePreferences = WhatsappNotificationPreference::query()
            ->with('user:id,first_name,last_name,username,email,phone_number,whatsapp_number')
            ->where('last_inbound_at', '>=', $windowStart)
            ->whereHas('user', fn ($query) => $query->whereNotNull('whatsapp_number')->where('whatsapp_number', '!=', ''))
            ->latest('last_inbound_at')
            ->limit(500)
            ->get();

        $deliveries = WhatsappNotificationDelivery::query()
            ->with([
                'user:id,first_name,last_name,username,email,whatsapp_number',
                'sender:id,first_name,last_name,email',
                'transaction:id,txn_reference,status',
            ])
            ->latest()
            ->paginate(25);

        return view('admin.whatsapp_messages.index', [
            'eligiblePreferences' => $eligiblePreferences,
            'deliveries' => $deliveries,
            'serviceWindowHours' => max(1, (int) config('whatsapp_notifications.transaction_alerts.service_window_hours', 24)),
        ]);
    }

    public function store(Request $request, MegaWhatsappService $whatsapp, WhatsappPhoneNumber $phoneNumbers): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'uuid', 'exists:users,id'],
            'message' => ['required', 'string', 'min:2', 'max:1000'],
        ]);

        $user = User::findOrFail($validated['user_id']);
        $preference = WhatsappNotificationPreference::where('user_id', $user->id)->first();
        $serviceWindowHours = max(1, (int) config('whatsapp_notifications.transaction_alerts.service_window_hours', 24));

        if (! $preference?->last_inbound_at?->gte(now()->subHours($serviceWindowHours))) {
            return back()->withErrors([
                'user_id' => "This customer is outside Meta's {$serviceWindowHours}-hour service window. Ask the customer to message OresamSub first, or use an approved template.",
            ])->withInput();
        }

        $phone = $phoneNumbers->toMeta($user->whatsapp_number);
        if (! $phone) {
            return back()->withErrors(['user_id' => 'This customer has no valid linked WhatsApp number.'])->withInput();
        }

        $delivery = WhatsappNotificationDelivery::create([
            'user_id' => $user->id,
            'sent_by' => $request->user()->id,
            'event_key' => 'admin-message:'.Str::uuid(),
            'notification_type' => 'admin_manual',
            'payload_type' => 'text',
            'recipient_last_four' => substr($phone, -4),
            'status' => 'processing',
            'message_excerpt' => Str::limit(preg_replace('/\s+/', ' ', strip_tags($validated['message'])), 500, '…'),
            'attempted_at' => now(),
        ]);

        try {
            $response = $whatsapp->sendText($phone, $validated['message']);
        } catch (Throwable $exception) {
            $delivery->update([
                'status' => 'indeterminate',
                'failure_code' => 'transport_error',
                'failure_message' => Str::limit($exception->getMessage(), 500, '…'),
            ]);
            report($exception);

            return back()->withErrors([
                'message' => 'The send result is uncertain due to a connection error. Do not resend immediately; check the delivery history first.',
            ]);
        }

        $messageId = data_get($response, 'messages.0.id');
        if (! $messageId) {
            $delivery->update([
                'status' => 'failed',
                'failure_code' => (string) (data_get($response, 'error.code') ?: 'meta_rejected'),
                'failure_message' => Str::limit((string) (data_get($response, 'error.message') ?: 'Meta rejected the message.'), 500, '…'),
            ]);

            return back()->withErrors(['message' => 'Meta rejected the WhatsApp message. Check the delivery history for details.'])->withInput();
        }

        $delivery->update([
            'status' => 'sent',
            'meta_message_id' => (string) $messageId,
        ]);

        return redirect()->route('admin.whatsapp_messages.index')
            ->with('success', 'WhatsApp message accepted by Meta for delivery to the selected customer.');
    }

    public function sendTransactionAlert(Request $request, Transaction $transaction): RedirectResponse
    {
        $notificationType = match ((string) $transaction->status) {
            '-1' => 'failed',
            '2' => 'refunded',
            default => null,
        };

        if (! $notificationType) {
            return back()->with('failure', 'WhatsApp transaction alerts can only be sent for failed or refunded transactions.');
        }

        $user = $transaction->user;
        if (! $user?->whatsapp_number) {
            return back()->with('failure', 'This customer has no linked WhatsApp number.');
        }

        $preference = WhatsappNotificationPreference::where('user_id', $user->id)->first();
        if (! ($preference?->transactional_enabled ?? false)) {
            return back()->with('failure', 'This customer has not opted in to WhatsApp transaction alerts. Ask them to send NOTIFICATIONS ON to the bot.');
        }

        $delivery = WhatsappNotificationDelivery::where(
            'event_key',
            "transaction:{$transaction->id}:{$notificationType}"
        )->first();

        if ($delivery && in_array($delivery->status, ['processing', 'sent', 'delivered', 'read', 'indeterminate'], true)) {
            return back()->with('failure', 'This WhatsApp alert has already been sent or its delivery result is still uncertain. It was not sent twice.');
        }

        SendWhatsappTransactionAlert::dispatch($transaction->id, $notificationType);

        return back()->with('success', 'The WhatsApp transaction alert has been queued.');
    }
}
