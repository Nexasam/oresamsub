@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="page-header">
        <div>
            <h3 class="text-2xl font-semibold text-gray-800">Mobile Push Campaigns</h3>
            <p class="text-sm text-gray-500">Send a notification to app customers and open the right page when they tap it.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert mb-4 border border-success/20 bg-success/10 text-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert mb-4 border border-danger/20 bg-danger/10 text-danger">
            <ul class="ml-5 list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="mb-5 grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="box"><div class="box-body"><p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Reachable customers</p><p class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($stats['customers']) }}</p></div></div>
        <div class="box"><div class="box-body"><p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Active app devices</p><p class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($stats['devices']) }}</p></div></div>
        <div class="box"><div class="box-body"><p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Campaigns created</p><p class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($stats['campaigns']) }}</p></div></div>
    </div>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-12">
        <div class="xl:col-span-7">
            <div class="box">
                <div class="box-header"><h5 class="box-title">Create campaign</h5></div>
                <div class="box-body">
                    <form id="push-campaign-form" method="POST" action="{{ route('admin.mobile_push_campaigns.store') }}" class="space-y-5">
                        @csrf
                        <input type="hidden" name="request_token" value="{{ old('request_token', $requestToken) }}">

                        <div>
                            <label for="push-title" class="ti-form-label">Notification title</label>
                            <input id="push-title" name="title" maxlength="80" required value="{{ old('title') }}" class="ti-form-input" placeholder="Get free ₦200 today">
                            <p class="mt-1 text-xs text-gray-500"><span id="title-count">0</span>/80 characters</p>
                        </div>

                        <div>
                            <label for="push-message" class="ti-form-label">Message</label>
                            <textarea id="push-message" name="message" maxlength="180" rows="4" required class="ti-form-input" placeholder="Open OresamSub and complete the offer before it ends.">{{ old('message') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500"><span id="message-count">0</span>/180 characters</p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label for="push-destination" class="ti-form-label">Open this mobile page</label>
                                <select id="push-destination" name="destination" required class="ti-form-select">
                                    @foreach ($destinations as $value => $label)
                                        <option value="{{ $value }}" @selected(old('destination', 'dashboard') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="push-audience" class="ti-form-label">Audience</label>
                                <select id="push-audience" name="audience_type" required class="ti-form-select">
                                    @foreach ($audiences as $value => $label)
                                        <option value="{{ $value }}" @selected(old('audience_type', 'all') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="plan-audience-field" class="hidden">
                            <label for="push-plan" class="ti-form-label">Customer plan</label>
                            <select id="push-plan" name="audience_value" class="ti-form-select">
                                <option value="">Select a customer plan</option>
                                @foreach ($userPlans as $plan)
                                    <option value="{{ $plan->id }}" @selected(old('audience_value') === $plan->id)>{{ $plan->user_plan_name }} (Level {{ $plan->plan_level }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                            This action creates one campaign and queues it once. Delivery uses only enabled, non-revoked app installations.
                        </div>

                        <button id="campaign-submit" type="submit" class="ti-btn ti-btn-primary w-full justify-center py-3">Queue push campaign</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="xl:col-span-5">
            <div class="box">
                <div class="box-header"><h5 class="box-title">Customer preview</h5></div>
                <div class="box-body">
                    <div class="rounded-[28px] bg-slate-900 p-4 shadow-xl">
                        <div class="rounded-2xl bg-white p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-600 font-black text-white">O</div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2"><p class="font-bold text-gray-900">OresamSub</p><span class="text-xs text-gray-400">now</span></div>
                                    <p id="preview-title" class="mt-1 break-words text-sm font-bold text-gray-900">Your notification title</p>
                                    <p id="preview-message" class="mt-1 break-words text-sm leading-5 text-gray-600">Your notification message will appear here.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 rounded-xl bg-gray-50 p-4 text-sm text-gray-600">
                        Tapping it opens: <strong id="preview-destination" class="text-gray-900">Dashboard</strong><br>
                        Audience: <strong id="preview-audience" class="text-gray-900">All app customers</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box mt-5">
        <div class="box-header"><h5 class="box-title">Campaign history</h5></div>
        <div class="box-body overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead><tr class="text-left text-xs uppercase tracking-wide text-gray-500"><th class="px-3 py-3">Campaign</th><th class="px-3 py-3">Audience</th><th class="px-3 py-3">Destination</th><th class="px-3 py-3">Reach</th><th class="px-3 py-3">Delivery</th><th class="px-3 py-3">Status / Date</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($campaigns as $campaign)
                        @php($delivery = $campaign->delivery_stats ?? [])
                        <tr>
                            <td class="max-w-md px-3 py-4 align-top"><p class="font-bold text-gray-900">{{ $campaign->title }}</p><p class="mt-1 text-xs leading-5 text-gray-500">{{ $campaign->message }}</p><p class="mt-1 text-[11px] text-gray-400">By {{ $campaign->sender?->first_name ?? $campaign->sender?->email ?? 'Former admin' }}</p></td>
                            <td class="px-3 py-4 align-top font-semibold text-gray-700">{{ $campaign->audienceLabel() }}</td>
                            <td class="px-3 py-4 align-top">{{ $destinations[$campaign->destination] ?? $campaign->destination }}</td>
                            <td class="px-3 py-4 align-top"><p>{{ number_format($campaign->recipient_users) }} customers</p><p class="text-xs text-gray-500">{{ number_format($campaign->recipient_devices) }} devices</p></td>
                            <td class="px-3 py-4 align-top text-xs"><p class="text-emerald-700">Delivered: {{ number_format($delivery['delivered'] ?? 0) }}</p><p class="text-blue-700">Accepted: {{ number_format($delivery['sent'] ?? 0) }}</p><p class="text-red-700">Failed: {{ number_format($delivery['failed'] ?? 0) }}</p><p class="text-gray-500">Queued: {{ number_format($delivery['queued'] ?? 0) }}</p></td>
                            <td class="px-3 py-4 align-top"><span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-bold uppercase text-gray-700">{{ $campaign->status }}</span><p class="mt-2 text-xs text-gray-500">{{ $campaign->created_at->diffForHumans() }}</p>@if($campaign->failure_message)<p class="mt-1 max-w-xs text-xs text-red-600">{{ $campaign->failure_message }}</p>@endif</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-3 py-10 text-center text-gray-500">No push campaign has been created yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $campaigns->links() }}</div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const title = document.getElementById('push-title');
    const message = document.getElementById('push-message');
    const destination = document.getElementById('push-destination');
    const audience = document.getElementById('push-audience');
    const plan = document.getElementById('push-plan');
    const planField = document.getElementById('plan-audience-field');
    const form = document.getElementById('push-campaign-form');
    const submit = document.getElementById('campaign-submit');

    function updatePreview() {
        document.getElementById('preview-title').textContent = title.value.trim() || 'Your notification title';
        document.getElementById('preview-message').textContent = message.value.trim() || 'Your notification message will appear here.';
        document.getElementById('title-count').textContent = title.value.length;
        document.getElementById('message-count').textContent = message.value.length;
        document.getElementById('preview-destination').textContent = destination.options[destination.selectedIndex]?.text || 'Dashboard';
        const planAudience = audience.value === 'user_plan';
        planField.classList.toggle('hidden', !planAudience);
        plan.required = planAudience;
        document.getElementById('preview-audience').textContent = planAudience
            ? (plan.options[plan.selectedIndex]?.text || 'Selected customer plan')
            : (audience.options[audience.selectedIndex]?.text || 'All app customers');
    }

    [title, message, destination, audience, plan].forEach(function (element) {
        element.addEventListener('input', updatePreview);
        element.addEventListener('change', updatePreview);
    });
    form.addEventListener('submit', function (event) {
        if (!window.confirm('Queue this push notification for the selected audience?')) {
            event.preventDefault();
            return;
        }
        submit.disabled = true;
        submit.textContent = 'Queueing campaign…';
    });
    updatePreview();
});
</script>
@endsection
