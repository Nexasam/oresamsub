@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="page-header">
        <div>
            <h3 class="text-2xl font-semibold text-gray-800">WhatsApp Customer Messages</h3>
            <p class="text-sm text-gray-500">Send one support message to one customer and monitor transaction-alert deliveries.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert mb-4 border border-success/20 bg-success/10 text-success">{{ session('success') }}</div>
    @endif
    @if (session('failure'))
        <div class="alert mb-4 border border-danger/20 bg-danger/10 text-danger">{{ session('failure') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert mb-4 border border-danger/20 bg-danger/10 text-danger">
            <ul class="ml-5 list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-12">
        <div class="xl:col-span-7">
            <div class="box">
                <div class="box-header"><h5 class="box-title">Compose a customer message</h5></div>
                <div class="box-body">
                    <form method="POST" action="{{ route('admin.whatsapp_messages.store') }}" class="space-y-5" id="whatsapp-message-form">
                        @csrf
                        <div>
                            <label for="whatsapp-customer" class="ti-form-label">Customer</label>
                            <select id="whatsapp-customer" name="user_id" required class="ti-form-select">
                                <option value="">Select a customer</option>
                                @foreach ($eligiblePreferences as $preference)
                                    @if ($preference->user)
                                        <option value="{{ $preference->user_id }}" @selected(old('user_id') === $preference->user_id)>
                                            {{ trim($preference->user->first_name.' '.$preference->user->last_name) ?: $preference->user->username }}
                                            — {{ $preference->user->username }}
                                            — WhatsApp ***{{ substr((string) $preference->user->whatsapp_number, -4) }}
                                            — active {{ $preference->last_inbound_at->diffForHumans() }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Only customers who messaged OresamSub within the last {{ $serviceWindowHours }} hours are listed.</p>
                        </div>

                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <label for="whatsapp-message" class="ti-form-label">Message</label>
                                <span class="text-xs text-gray-500"><span id="whatsapp-message-count">0</span>/1000</span>
                            </div>
                            <textarea id="whatsapp-message" name="message" maxlength="1000" minlength="2" rows="8" required class="ti-form-input" placeholder="Hello, we are following up on your transaction...">{{ old('message') }}</textarea>
                        </div>

                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-900">
                            Meta permits this free-form message only inside the {{ $serviceWindowHours }}-hour customer-service window. Send to one customer at a time. Never include PINs, passwords, tokens, or full wallet details.
                        </div>

                        <button type="submit" id="whatsapp-message-submit" class="ti-btn ti-btn-primary w-full justify-center py-3">Send WhatsApp message</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="xl:col-span-5">
            <div class="box">
                <div class="box-header"><h5 class="box-title">How transaction alerts work</h5></div>
                <div class="box-body space-y-3 text-sm leading-6 text-gray-600">
                    <p><strong class="text-gray-900">Customer opt-in:</strong> the customer sends <code>NOTIFICATIONS ON</code> to the OresamSub bot.</p>
                    <p><strong class="text-gray-900">Automatic alerts:</strong> failed and refunded transactions trigger one protected alert.</p>
                    <p><strong class="text-gray-900">Outside {{ $serviceWindowHours }} hours:</strong> the approved Meta Utility template is used.</p>
                    <p><strong class="text-gray-900">Opt-out:</strong> the customer sends <code>STOP</code> or <code>NOTIFICATIONS OFF</code>.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="box mt-5">
        <div class="box-header"><h5 class="box-title">WhatsApp delivery history</h5></div>
        <div class="box-body overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead><tr class="text-left text-xs uppercase tracking-wide text-gray-500"><th class="px-3 py-3">Customer</th><th class="px-3 py-3">Message</th><th class="px-3 py-3">Type</th><th class="px-3 py-3">Status</th><th class="px-3 py-3">Sent by / Date</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($deliveries as $delivery)
                        <tr>
                            <td class="px-3 py-4 align-top"><p class="font-bold text-gray-900">{{ trim(($delivery->user?->first_name ?? '').' '.($delivery->user?->last_name ?? '')) ?: ($delivery->user?->username ?? 'Former customer') }}</p><p class="text-xs text-gray-500">***{{ $delivery->recipient_last_four ?: substr((string) $delivery->user?->whatsapp_number, -4) }}</p></td>
                            <td class="max-w-lg px-3 py-4 align-top"><p class="text-gray-700">{{ $delivery->message_excerpt ?: ($delivery->transaction?->txn_reference ? 'Transaction '.$delivery->transaction->txn_reference : 'Protected transaction alert') }}</p>@if($delivery->failure_message)<p class="mt-1 text-xs text-red-600">{{ $delivery->failure_message }}</p>@endif</td>
                            <td class="px-3 py-4 align-top"><p class="font-semibold text-gray-700">{{ str($delivery->notification_type)->headline() }}</p><p class="text-xs text-gray-500">{{ $delivery->payload_type ?: 'Pending' }}</p></td>
                            <td class="px-3 py-4 align-top"><span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-bold uppercase text-gray-700">{{ $delivery->status }}</span></td>
                            <td class="px-3 py-4 align-top"><p>{{ $delivery->sender?->email ?? ($delivery->notification_type === 'admin_manual' ? 'Former admin' : 'Automatic') }}</p><p class="mt-1 text-xs text-gray-500">{{ $delivery->created_at->diffForHumans() }}</p></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-3 py-10 text-center text-gray-500">No WhatsApp notification has been recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $deliveries->links() }}</div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('whatsapp-message-form');
    const message = document.getElementById('whatsapp-message');
    const counter = document.getElementById('whatsapp-message-count');
    const submit = document.getElementById('whatsapp-message-submit');

    const updateCount = () => counter.textContent = message.value.length;
    message.addEventListener('input', updateCount);
    updateCount();

    form.addEventListener('submit', function (event) {
        if (!window.confirm('Send this WhatsApp message to the selected customer?')) {
            event.preventDefault();
            return;
        }
        submit.disabled = true;
        submit.textContent = 'Sending…';
    });
});
</script>
@endsection
