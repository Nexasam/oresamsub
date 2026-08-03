@extends('layouts.app')

@section('content')
<div class="main-content" x-data="{ createOpen: false, tab: 'campaigns' }">
    <div class="block justify-between page-header md:flex">
        <div>
            <h3 class="text-gray-700 text-2xl font-semibold">Bonus campaigns</h3>
            <p class="text-sm text-gray-500 mt-1">Configure welcome rewards and monitor marketing spend.</p>
        </div>
        <button type="button" class="ti-btn ti-btn-success mt-3 md:mt-0" @click="createOpen = true">
            <i class="ri ri-add-line"></i> New campaign
        </button>
    </div>

    @if (session('success'))
        <div class="bg-success/10 border border-success/20 alert text-success">{{ session('success') }}</div>
    @endif
    @if (session('failure'))
        <div class="bg-danger/10 border border-danger/20 alert text-danger">{{ session('failure') }}</div>
    @endif
    @if ($errors->any())
        <div class="bg-danger/10 border border-danger/20 alert text-danger">
            <strong>Please correct the campaign:</strong> {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-12 gap-4 mb-5">
        @foreach ([
            ['Wallet awarded', $stats['wallet_awarded'], 'ri-gift-line', 'text-primary'],
            ['Moved to main wallet', $stats['wallet_converted'], 'ri-exchange-funds-line', 'text-success'],
            ['Funding incentives', $stats['funding_rewards'], 'ri-bank-card-line', 'text-warning'],
            ['Expired unused', $stats['expired'], 'ri-timer-line', 'text-danger'],
        ] as [$label, $value, $icon, $tone])
            <div class="col-span-12 sm:col-span-6 xl:col-span-3">
                <div class="box mb-0">
                    <div class="box-body flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $label }}</p>
                            <p class="text-xl font-bold mt-2">₦{{ number_format($value, 2) }}</p>
                        </div>
                        <i class="{{ $icon }} {{ $tone }} text-3xl"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="box">
        <div class="box-header flex gap-2">
            <button type="button" class="ti-btn" :class="tab === 'campaigns' ? 'ti-btn-primary' : 'ti-btn-light'" @click="tab = 'campaigns'">Campaigns</button>
            <button type="button" class="ti-btn" :class="tab === 'logs' ? 'ti-btn-primary' : 'ti-btn-light'" @click="tab = 'logs'">Audit & bonus logs</button>
        </div>

        <div class="box-body" x-show="tab === 'campaigns'">
            <div class="overflow-auto">
                <table class="ti-custom-table ti-custom-table-head">
                    <thead>
                        <tr>
                            <th>Campaign</th>
                            <th>Audience</th>
                            <th>Rewards</th>
                            <th>Window</th>
                            <th>Customers</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bonuses as $bonus)
                            <tr class="{{ $bonus->trashed() ? 'opacity-60' : '' }}">
                                <td>
                                    <div class="font-semibold">{{ $bonus->title }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ \Illuminate\Support\Str::limit($bonus->description, 70) }}</div>
                                    @if ($bonus->isTargeted())
                                        <div class="text-xs text-primary mt-1">Targeted: {{ implode(', ', data_get($bonus->conditions, 'targeted_customers', [])) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="px-2 py-1 rounded bg-primary/10 text-primary text-xs">
                                        {{ str($bonus->group)->replace('_', ' ')->title() }}
                                    </span>
                                    @if ($bonus->group === 'dormant_customer')
                                        <div class="text-xs text-gray-500 mt-2">
                                            @if (data_get($bonus->conditions, 'last_transaction_before'))
                                                Last purchase before {{ data_get($bonus->conditions, 'last_transaction_before') }}
                                            @else
                                                {{ data_get($bonus->conditions, 'dormant_days', 15) }} inactive days
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($bonus->enjoyment ?? [] as $reward)
                                            <span class="px-2 py-1 rounded bg-gray-100 text-gray-700 text-xs">{{ str($reward)->replace('_', ' ')->title() }}</span>
                                        @endforeach
                                    </div>
                                    @if ($bonus->bonus_wallet_amount > 0)
                                        <div class="text-xs mt-2">Wallet: ₦{{ number_format((float) $bonus->bonus_wallet_amount, 2) }}</div>
                                    @endif
                                    @if ($bonus->funding_value > 0)
                                        <div class="text-xs mt-1">
                                            Funding: {{ $bonus->funding_type === 'percent' ? rtrim(rtrim(number_format((float) $bonus->funding_value, 4), '0'), '.') . '%' : '₦' . number_format((float) $bonus->funding_value, 2) }}
                                            @if ($bonus->funding_cap) · cap ₦{{ number_format((float) $bonus->funding_cap, 2) }} @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="text-xs">
                                    <div>{{ $bonus->starts_at?->format('d M Y H:i') ?? 'Immediately' }}</div>
                                    <div class="text-gray-500 mt-1">to {{ $bonus->ends_at?->format('d M Y H:i') }}</div>
                                </td>
                                <td>{{ number_format($bonus->entitlements_count) }}</td>
                                <td>
                                    @if ($bonus->trashed())
                                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">Archived</span>
                                    @elseif ($bonus->status)
                                        <span class="bg-success/10 text-success px-2 py-1 rounded text-xs">Active</span>
                                    @else
                                        <span class="bg-warning/10 text-warning px-2 py-1 rounded text-xs">Paused</span>
                                    @endif
                                </td>
                                <td>
                                    @unless ($bonus->trashed())
                                        <div class="flex flex-wrap gap-2">
                                            <form method="POST" action="{{ route('admin.bonuses.toggle', $bonus) }}">
                                                @csrf @method('PATCH')
                                                <button class="ti-btn ti-btn-sm {{ $bonus->status ? 'ti-btn-warning' : 'ti-btn-success' }}" type="submit">
                                                    {{ $bonus->status ? 'Pause' : 'Resume' }}
                                                </button>
                                            </form>
                                            <details class="relative">
                                                <summary class="ti-btn ti-btn-sm ti-btn-primary cursor-pointer list-none">Edit</summary>
                                                <div class="fixed inset-0 z-[70] bg-black/50 flex items-center justify-center p-4">
                                                    <div class="bg-white rounded-xl w-full max-w-3xl max-h-[90vh] overflow-auto p-5" @click.outside="$el.parentElement.removeAttribute('open')">
                                                        <div class="flex items-center justify-between mb-4">
                                                            <h4 class="font-semibold text-lg">Edit {{ $bonus->title }}</h4>
                                                            <button type="button" class="text-2xl" onclick="this.closest('details').removeAttribute('open')">&times;</button>
                                                        </div>
                                                        @include('admin.bonuses.partials.form', ['campaign' => $bonus, 'formAction' => route('admin.bonuses.update', $bonus), 'formMethod' => 'PUT'])
                                                    </div>
                                                </div>
                                            </details>
                                            <form method="POST" action="{{ route('admin.bonuses.destroy', $bonus) }}" onsubmit="return confirm('Archive this campaign? Existing audit records will remain.')">
                                                @csrf @method('DELETE')
                                                <button class="ti-btn ti-btn-sm ti-btn-danger" type="submit">Archive</button>
                                            </form>
                                        </div>
                                    @endunless
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-gray-500 py-8">No bonus campaigns yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box-body" x-show="tab === 'logs'" x-cloak>
            <div class="overflow-auto">
                <table class="ti-custom-table ti-custom-table-head">
                    <thead><tr><th>Date</th><th>Customer</th><th>Campaign</th><th>Event</th><th>Amount</th><th>Provider/reference</th><th>Context</th><th></th></tr></thead>
                    <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="text-xs whitespace-nowrap">{{ $log->created_at?->format('d M Y H:i') }}</td>
                            <td><div class="font-medium">{{ $log->user?->username }}</div><div class="text-xs text-gray-500">{{ $log->user?->email }}</div></td>
                            <td>{{ $log->bonus?->title ?? 'Campaign removed' }}</td>
                            <td><span class="px-2 py-1 bg-gray-100 rounded text-xs">{{ str($log->event_type)->replace('_', ' ')->title() }}</span></td>
                            <td>₦{{ number_format((float) $log->amount, 2) }}</td>
                            <td class="text-xs"><div>{{ $log->funding_provider ?: '—' }}</div><div class="text-gray-500">{{ $log->funding_reference ?: '' }}</div></td>
                            <td class="text-xs">
                                <div>{{ $log->ip_address ?: 'No IP' }}</div>
                                @if (data_get($log->metadata, 'reason'))
                                    <div class="text-danger mt-1">{{ str(data_get($log->metadata, 'reason'))->replace('_', ' ') }}</div>
                                @endif
                            </td>
                            <td>
                                @if ($log->event_type === 'eligibility_rejected' && $log->bonus && ! $log->bonus->trashed())
                                    <form method="POST" action="{{ route('admin.bonus-logs.override', $log) }}">
                                        @csrf
                                        <button type="submit" class="ti-btn ti-btn-sm ti-btn-primary">Grant manually</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-gray-500 py-8">No bonus activity yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $logs->links() }}</div>
        </div>
    </div>

    <div class="fixed inset-0 z-[80] bg-black/50 flex items-center justify-center p-4" x-show="createOpen" x-cloak>
        <div class="bg-white rounded-xl w-full max-w-3xl max-h-[90vh] overflow-auto p-5" @click.outside="createOpen = false">
            <div class="flex items-center justify-between mb-4">
                <div><h4 class="font-semibold text-lg">Create bonus campaign</h4><p class="text-xs text-gray-500 mt-1">Rewards are enforced and audited by the backend.</p></div>
                <button type="button" class="text-2xl" @click="createOpen = false">&times;</button>
            </div>
            @include('admin.bonuses.partials.form', ['campaign' => null, 'formAction' => route('admin.bonuses.store'), 'formMethod' => 'POST'])
        </div>
    </div>
</div>
@endsection
