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
