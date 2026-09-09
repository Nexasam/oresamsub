@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="block justify-between page-header md:flex">
        <div>
            <h3 class="text-gray-700 text-2xl font-semibold">Standalone websites</h3>
            <p class="mt-1 text-sm text-gray-500">Manage API access, Kolomoni accounts and master wallets.</p>
        </div>
        <a class="ti-btn ti-btn-primary mt-3 md:mt-0" href="{{ route('admin.standalones.create') }}">Add standalone website</a>
    </div>

    @if (session('success'))
        <div class="bg-success/10 border border-success/20 alert text-success">{{ session('success') }}</div>
    @endif

    <div class="box">
        <div class="box-header"><h5 class="box-title">Registered standalones</h5></div>
        <div class="box-body overflow-auto">
            <table class="ti-custom-table ti-custom-table-head ti-striped-table ti-custom-table-hover">
                <thead><tr><th>Business</th><th>API access</th><th>Master wallet</th><th>Kolomoni account</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                @forelse ($sites as $site)
                    <tr>
                        <td><strong>{{ $site->business_name }}</strong><div class="text-xs text-gray-500">{{ $site->email }}</div></td>
                        <td><span class="text-xs">{{ $site->api_token_must_rotate ? 'Rotation required' : 'Operational' }}</span><div class="text-xs text-gray-500">{{ $site->api_token_prefix }}••••</div></td>
                        <td class="font-semibold">₦{{ number_format((float) $site->master_wallet, 2) }}</td>
                        <td>{{ $site->virtualAccount?->account_number ?? 'Not generated' }}<div class="text-xs text-gray-500">{{ $site->virtualAccount?->bank_name }}</div></td>
                        <td><span class="px-2 py-1 rounded text-xs {{ $site->status === 'active' ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger' }}">{{ ucfirst($site->status) }}</span></td>
                        <td><a class="ti-btn ti-btn-sm ti-btn-primary" href="{{ route('admin.standalones.show', $site) }}">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-10 text-center text-gray-500">No standalone websites have been added.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if ($sites->hasPages())<div class="box-footer">{{ $sites->links() }}</div>@endif
    </div>
</div>
@endsection
