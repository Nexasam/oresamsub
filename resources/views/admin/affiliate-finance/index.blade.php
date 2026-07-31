@extends('layouts.app')

@section('content')
<div class="main-content" x-data="{ tab: 'monitoring', walletOpen: false, bonusOpen: false }">
    <div class="block justify-between page-header md:flex">
        <div>
            <h3 class="text-gray-700 text-2xl font-semibold">Affiliate finance</h3>
            <p class="text-sm text-gray-500 mt-1">Low-wallet monitoring and funding-linked upline rewards.</p>
        </div>
        <div class="flex gap-2 mt-3 md:mt-0">
            <button class="ti-btn ti-btn-primary" type="button" @click="walletOpen = true">Add wallet monitor</button>
            <button class="ti-btn ti-btn-success" type="button" @click="bonusOpen = true">Add upline bonus</button>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-success/10 border border-success/20 alert text-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="bg-danger/10 border border-danger/20 alert text-danger">{{ $errors->first() }}</div>
    @endif

    <form class="box box-body mb-5 flex gap-2" method="GET">
        <input class="ti-form-input flex-1" name="search" value="{{ request('search') }}" placeholder="Search customer by email, username or phone">
        <button class="ti-btn ti-btn-primary" type="submit">Search</button>
        @if (request('search')) <a class="ti-btn ti-btn-light" href="{{ route('admin.affiliate-finance.index') }}">Clear</a> @endif
    </form>

    <div class="box">
        <div class="box-header flex flex-wrap gap-2">
            <button type="button" class="ti-btn" :class="tab === 'monitoring' ? 'ti-btn-primary' : 'ti-btn-light'" @click="tab = 'monitoring'">Wallet monitoring</button>
            <button type="button" class="ti-btn" :class="tab === 'upline' ? 'ti-btn-primary' : 'ti-btn-light'" @click="tab = 'upline'">Upline bonuses</button>
            <button type="button" class="ti-btn" :class="tab === 'activity' ? 'ti-btn-primary' : 'ti-btn-light'" @click="tab = 'activity'">Activity logs</button>
        </div>

        <div class="box-body overflow-auto" x-show="tab === 'monitoring'">
            <table class="ti-custom-table ti-custom-table-head">
                <thead><tr><th>Affiliate</th><th>Balance / threshold</th><th>Notification</th><th>Funding account</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                @forelse ($walletSettings as $setting)
                    <tr>
                        <td><strong>{{ $setting->user?->username }}</strong><div class="text-xs text-gray-500">{{ $setting->user?->email }}</div></td>
                        <td><div>₦{{ number_format((float) $setting->user?->main_wallet, 2) }}</div><div class="text-xs text-gray-500">Alert below ₦{{ number_format((float) $setting->funding_threshold, 2) }}</div></td>
                        <td class="text-xs"><div>{{ $setting->notification_email ?: $setting->user?->email }}</div><div class="text-gray-500">CC: {{ $setting->admin_copy_email ?: 'Not set' }}</div><div class="mt-1">Last: {{ $setting->last_notified_on?->format('d M Y') ?? 'Never' }}</div></td>
                        <td class="text-xs">{{ $setting->funding_account_number ? 'Configured ••••'.substr($setting->funding_account_number, -4) : 'Not configured' }}<div class="text-gray-500">{{ $setting->automatic_transfer_enabled ? 'Transfer placeholder enabled' : 'Notification only' }}</div></td>
                        <td><span class="px-2 py-1 rounded text-xs {{ $setting->enabled ? 'bg-success/10 text-success' : 'bg-gray-100 text-gray-600' }}">{{ $setting->enabled ? 'Active' : 'Paused' }}</span></td>
                        <td>
                            <details>
                                <summary class="ti-btn ti-btn-sm ti-btn-primary cursor-pointer list-none">Edit</summary>
                                <div class="mt-3 min-w-[700px]">@include('admin.affiliate-finance.partials.wallet-form', ['setting' => $setting])</div>
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-8 text-gray-500">No affiliate wallet monitor configured.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="box-body overflow-auto" x-show="tab === 'upline'" x-cloak>
            <table class="ti-custom-table ti-custom-table-head">
                <thead><tr><th>Upline</th><th>Reward</th><th>Frequency</th><th>Providers</th><th>Rewards given</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                @forelse ($uplineSettings as $setting)
                    <tr>
                        <td><strong>{{ $setting->user?->username }}</strong><div class="text-xs text-gray-500">{{ $setting->user?->email }}</div></td>
                        <td>{{ $setting->reward_type === 'percent' ? rtrim(rtrim(number_format((float) $setting->reward_value, 4), '0'), '.').'%' : '₦'.number_format((float) $setting->reward_value, 2) }}@if($setting->reward_cap)<div class="text-xs text-gray-500">Cap ₦{{ number_format((float) $setting->reward_cap, 2) }}</div>@endif</td>
                        <td>{{ $setting->frequency_per_downline }} per downline</td>
                        <td class="text-xs">{{ implode(', ', $setting->funding_whitelist ?: ['All active']) }}</td>
                        <td>{{ number_format($setting->logs_count) }}</td>
                        <td><span class="px-2 py-1 rounded text-xs {{ $setting->enabled ? 'bg-success/10 text-success' : 'bg-gray-100 text-gray-600' }}">{{ $setting->enabled ? 'Active' : 'Paused' }}</span></td>
                        <td>
                            <details>
                                <summary class="ti-btn ti-btn-sm ti-btn-primary cursor-pointer list-none">Edit</summary>
                                <div class="mt-3 min-w-[700px]">@include('admin.affiliate-finance.partials.upline-form', ['setting' => $setting])</div>
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-8 text-gray-500">No upline funding bonus configured.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="box-body grid xl:grid-cols-2 gap-5" x-show="tab === 'activity'" x-cloak>
            <div>
                <h5 class="font-semibold mb-3">Low-balance notifications</h5>
                <div class="overflow-auto"><table class="ti-custom-table ti-custom-table-head">
                    <thead><tr><th>Date</th><th>Affiliate</th><th>Balance</th><th>Status</th></tr></thead>
                    <tbody>@forelse($attempts as $attempt)<tr><td class="text-xs">{{ $attempt->triggered_at?->format('d M Y H:i') }}</td><td>{{ $attempt->user?->username }}</td><td>₦{{ number_format((float) $attempt->wallet_balance, 2) }}</td><td>{{ str($attempt->status)->replace('_', ' ')->title() }}</td></tr>@empty<tr><td colspan="4">No activity yet.</td></tr>@endforelse</tbody>
                </table></div>
            </div>
            <div>
                <h5 class="font-semibold mb-3">Upline funding rewards</h5>
                <div class="overflow-auto"><table class="ti-custom-table ti-custom-table-head">
                    <thead><tr><th>Date</th><th>Upline / downline</th><th>Funding</th><th>Bonus</th></tr></thead>
                    <tbody>@forelse($bonusLogs as $log)<tr><td class="text-xs">{{ $log->created_at?->format('d M Y H:i') }}</td><td><strong>{{ $log->upline?->username }}</strong><div class="text-xs text-gray-500">from {{ $log->downline?->username }}</div></td><td>₦{{ number_format((float) $log->funded_amount, 2) }}<div class="text-xs text-gray-500">{{ $log->funding_provider }}</div></td><td class="text-success">+₦{{ number_format((float) $log->bonus_amount, 2) }}</td></tr>@empty<tr><td colspan="4">No rewards yet.</td></tr>@endforelse</tbody>
                </table></div>
            </div>
        </div>
    </div>

    <div class="fixed inset-0 z-[80] bg-black/50 flex items-center justify-center p-4" x-show="walletOpen" x-cloak>
        <div class="bg-white rounded-xl w-full max-w-4xl max-h-[90vh] overflow-auto p-5" @click.outside="walletOpen = false">
            <div class="flex justify-between mb-4"><h4 class="font-semibold text-lg">Configure affiliate wallet monitoring</h4><button type="button" @click="walletOpen = false">&times;</button></div>
            @include('admin.affiliate-finance.partials.wallet-form', ['setting' => null])
        </div>
    </div>
    <div class="fixed inset-0 z-[80] bg-black/50 flex items-center justify-center p-4" x-show="bonusOpen" x-cloak>
        <div class="bg-white rounded-xl w-full max-w-4xl max-h-[90vh] overflow-auto p-5" @click.outside="bonusOpen = false">
            <div class="flex justify-between mb-4"><h4 class="font-semibold text-lg">Configure upline funding bonus</h4><button type="button" @click="bonusOpen = false">&times;</button></div>
            @include('admin.affiliate-finance.partials.upline-form', ['setting' => null])
        </div>
    </div>
</div>
@endsection
