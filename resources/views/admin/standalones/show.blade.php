@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="block justify-between page-header md:flex">
        <div><h3 class="text-gray-700 text-2xl font-semibold">{{ $site->business_name }}</h3><p class="mt-1 text-sm text-gray-500">Standalone wallet and integration management.</p></div>
        <a class="ti-btn ti-btn-light mt-3 md:mt-0" href="{{ route('admin.standalones.index') }}">Back to standalones</a>
    </div>
    @if(session('success'))<div class="bg-success/10 border border-success/20 alert text-success">{{ session('success') }}</div>@endif

    <div class="grid grid-cols-12 gap-6 mb-6">
        <div class="col-span-12 md:col-span-4 box box-body"><p class="text-sm text-gray-500">Master wallet</p><p class="mt-2 text-2xl font-semibold">₦{{ number_format((float) $site->master_wallet, 2) }}</p></div>
        <div class="col-span-12 md:col-span-4 box box-body"><p class="text-sm text-gray-500">API token</p><p class="mt-2 font-semibold">{{ $site->api_token_must_rotate ? 'Rotation required' : 'Operational' }}</p><p class="text-xs text-gray-500">{{ $site->api_token_prefix }}•••• @if($site->api_token_expires_at) · expires {{ $site->api_token_expires_at->diffForHumans() }} @endif</p></div>
        <div class="col-span-12 md:col-span-4 box box-body"><p class="text-sm text-gray-500">Kolomoni account</p><p class="mt-2 font-semibold">{{ $site->virtualAccount?->account_number ?? 'Not generated' }}</p><p class="text-xs text-gray-500">{{ $site->virtualAccount?->bank_name }}</p></div>
    </div>

    <div class="box"><div class="box-header"><h5 class="box-title">Feature price level</h5></div><div class="box-body"><form method="POST" action="{{ route('admin.standalones.price-level', $site) }}" class="flex flex-wrap items-end gap-3">@csrf @method('PUT')<div><label class="ti-form-label">Global level</label><select class="ti-form-select" name="price_level"><option value="">Default prices</option>@foreach([1,2,3,4] as $level)<option value="{{ $level }}" @selected($site->price_level===$level)>Level {{ $level }}</option>@endforeach</select></div><button class="ti-btn ti-btn-primary">Save level</button></form><p class="mt-2 text-xs text-gray-500">Changes apply only to future purchases and renewals.</p></div></div>

    <div class="box"><div class="box-header"><h5 class="box-title">Available features</h5></div><div class="box-body overflow-auto"><table class="ti-custom-table ti-custom-table-head"><thead><tr><th>Feature</th><th>Mode</th><th>Billing</th><th>Setup / initial</th><th>Monthly</th><th>Status / slots</th></tr></thead><tbody>@foreach($features as $feature) @php($featureSubscriptions=$subscriptions->get($feature->id,collect()))<tr><td><strong>{{ $feature->name }}</strong></td><td>{{ $feature->purchase_mode === 'named_slots' ? 'Named slots' : 'Single' }}</td><td>{{ str_replace('_',' ',ucfirst($feature->billing_type)) }}</td><td>₦{{ number_format((float)$feature->purchasePriceFor($site->price_level),2) }}</td><td>{{ $feature->isRecurring() ? '₦'.number_format((float)$feature->monthlyPriceFor($site->price_level),2) : '—' }}</td><td>@if($feature->purchase_mode==='named_slots') @forelse($featureSubscriptions as $slot)<div>{{ $slot->slot_name }} — {{ ucfirst($slot->status) }}</div>@empty Available @endforelse @else {{ ucfirst($featureSubscriptions->first()?->status ?? ($feature->is_active ? 'Available' : 'Unavailable')) }} @endif</td></tr>@endforeach</tbody></table></div></div>

    <div class="box"><div class="box-header"><h5 class="box-title">Feature purchase history</h5></div><div class="box-body overflow-auto"><table class="ti-custom-table ti-custom-table-head"><thead><tr><th>Date</th><th>Feature / slot</th><th>Event</th><th>Price level</th><th>Amount</th><th>Reference</th></tr></thead><tbody>@forelse($featurePurchases as $purchase)<tr><td>{{ $purchase->created_at?->format('d M Y H:i') }}</td><td>{{ $purchase->feature->name }}@if($purchase->slot_name)<div class="text-xs text-gray-500">{{ $purchase->slot_name }}</div>@endif</td><td>{{ ucfirst($purchase->billing_event) }}</td><td>{{ str_replace('_',' ',ucfirst($purchase->applied_price_level)) }}</td><td>₦{{ number_format((float)$purchase->amount,2) }}</td><td>{{ $purchase->client_reference }}</td></tr>@empty<tr><td colspan="6" class="py-8 text-center text-gray-500">No feature purchases yet.</td></tr>@endforelse</tbody></table></div>@if($featurePurchases->hasPages())<div class="box-footer">{{ $featurePurchases->links() }}</div>@endif</div>

    <div class="box"><div class="box-header"><h5 class="box-title">Business details</h5></div><div class="box-body grid md:grid-cols-2 gap-4 text-sm">
        <div><span class="text-gray-500">Contact</span><p class="font-medium">{{ $site->contact_first_name }} {{ $site->contact_last_name }}</p></div>
        <div><span class="text-gray-500">Email / phone</span><p class="font-medium">{{ $site->email }} · {{ $site->phone }}</p></div>
        <div><span class="text-gray-500">Website</span><p><a class="text-primary" href="{{ $site->website_url }}" target="_blank" rel="noopener">{{ $site->website_url }}</a></p></div>
        <div><span class="text-gray-500">Status</span><p class="font-medium">{{ ucfirst($site->status) }}</p></div>
    </div><div class="box-footer flex flex-wrap gap-2">
        <form method="POST" action="{{ route('admin.standalones.status', $site) }}" onsubmit="return confirm('Change this standalone status?')">@csrf @method('PUT')<input type="hidden" name="status" value="{{ $site->status === 'active' ? 'suspended' : 'active' }}"><button class="ti-btn {{ $site->status === 'active' ? 'ti-btn-danger' : 'ti-btn-success' }}">{{ $site->status === 'active' ? 'Suspend' : 'Reactivate' }}</button></form>
        <form method="POST" action="{{ route('admin.standalones.rotate-api-token', $site) }}" onsubmit="return confirm('Generate a new 20-minute bootstrap token? The current token will stop working immediately.')">@csrf<button class="ti-btn ti-btn-warning">Generate replacement bootstrap token</button></form>
    </div></div>

    <div class="box"><div class="box-header"><h5 class="box-title">Wallet ledger</h5></div><div class="box-body overflow-auto"><table class="ti-custom-table ti-custom-table-head"><thead><tr><th>Date</th><th>Transaction</th><th>Purpose</th><th>Type</th><th>Amount</th><th>Balance after</th></tr></thead><tbody>
        @forelse($walletEntries as $entry)<tr><td>{{ $entry->created_at?->format('d M Y H:i') }}</td><td><strong>{{ $entry->client_reference ?? $entry->transaction_id }}</strong></td><td>{{ $entry->purpose }}</td><td>{{ ucfirst($entry->type) }}</td><td class="{{ $entry->type === 'credit' ? 'text-success' : 'text-danger' }}">{{ $entry->type === 'credit' ? '+' : '-' }}₦{{ number_format((float)$entry->amount,2) }}</td><td>₦{{ number_format((float)$entry->balance_after,2) }}</td></tr>
        @empty<tr><td colspan="6" class="py-8 text-center text-gray-500">No wallet activity yet.</td></tr>@endforelse
    </tbody></table></div>@if($walletEntries->hasPages())<div class="box-footer">{{ $walletEntries->links() }}</div>@endif</div>

    <div class="box"><div class="box-header"><h5 class="box-title">SecureWave funding history</h5></div><div class="box-body overflow-auto"><table class="ti-custom-table ti-custom-table-head"><thead><tr><th>Date</th><th>Provider reference</th><th>Gross</th><th>Fees</th><th>Settled</th></tr></thead><tbody>
        @forelse($events as $event)<tr><td>{{ $event->paid_at?->format('d M Y H:i') }}</td><td>{{ $event->provider_reference }}</td><td>₦{{ number_format((float)$event->amount_gross,2) }}</td><td>₦{{ number_format((float)$event->fees,2) }}</td><td class="text-success">₦{{ number_format((float)$event->amount_settled,2) }}</td></tr>
        @empty<tr><td colspan="5" class="py-8 text-center text-gray-500">No funding received yet.</td></tr>@endforelse
    </tbody></table></div>@if($events->hasPages())<div class="box-footer">{{ $events->links() }}</div>@endif</div>
</div>
@endsection
