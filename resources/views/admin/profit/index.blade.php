@extends('layouts.app')

@section('content')
@php($summary = $report['summary'])
<div class="main-content">
    <div class="block justify-between page-header md:flex">
        <div>
            <h3 class="text-gray-700 text-2xl font-semibold">Business profitability</h3>
            <p class="text-sm text-gray-500 mt-1">Realised margins, commissions, bonuses and current promotional liabilities.</p>
        </div>
        <a class="ti-btn ti-btn-light mt-3 md:mt-0" href="{{ request()->fullUrlWithQuery(['format' => 'json']) }}" target="_blank">Open JSON</a>
    </div>

    <form class="box box-body grid grid-cols-1 md:grid-cols-6 gap-3 mb-5" method="GET">
        <div><label class="ti-form-label">From</label><input class="ti-form-input" type="date" name="from" value="{{ $report['filters']['from'] }}"></div>
        <div><label class="ti-form-label">To</label><input class="ti-form-input" type="date" name="to" value="{{ $report['filters']['to'] }}"></div>
        <div><label class="ti-form-label">Service</label><select class="ti-form-select" name="category"><option value="">All services</option>@foreach($categories as $category)<option value="{{ $category }}" @selected($report['filters']['category'] === $category)>{{ str($category)->replace('_', ' ')->title() }}</option>@endforeach</select></div>
        <div><label class="ti-form-label">Automation</label><select class="ti-form-select" name="automation_id"><option value="">All automations</option>@foreach($automations as $automation)<option value="{{ $automation->id }}" @selected($report['filters']['automation_id'] === $automation->id)>{{ $automation->automation_name }}</option>@endforeach</select></div>
        <div><label class="ti-form-label">Funding provider</label><select class="ti-form-select" name="funding_provider"><option value="">All providers</option>@foreach($fundingProviders as $provider)<option value="{{ $provider->slug }}" @selected($report['filters']['funding_provider'] === $provider->slug)>{{ $provider->funding_option_name }}</option>@endforeach</select></div>
        <div class="flex items-end"><button class="ti-btn ti-btn-primary w-full" type="submit">Apply filters</button></div>
    </form>

    <div class="grid grid-cols-12 gap-4">
        @foreach([
            ['Estimated net profit', $summary['net_profit'], $summary['net_profit'] >= 0 ? 'text-success' : 'text-danger', 'ri-line-chart-line'],
            ['Transaction gross profit', $summary['transaction_gross_profit'], 'text-primary', 'ri-shopping-bag-3-line'],
            ['Funding net margin', $summary['funding_net_margin'], 'text-info', 'ri-bank-card-line'],
            ['Total reward expenses', $summary['commission_accrued'] + $summary['campaign_wallet_expense'] + $summary['upline_funding_bonus_expense'], 'text-warning', 'ri-gift-line'],
        ] as [$label, $amount, $tone, $icon])
            <div class="col-span-12 sm:col-span-6 xl:col-span-3"><div class="box mb-0"><div class="box-body flex justify-between"><div><p class="text-xs text-gray-500 uppercase">{{ $label }}</p><p class="text-xl font-bold mt-2 {{ $tone }}">₦{{ number_format($amount, 2) }}</p></div><i class="{{ $icon }} text-3xl {{ $tone }}"></i></div></div></div>
        @endforeach
    </div>

    <div class="grid xl:grid-cols-2 gap-5 mt-5">
        <div class="box">
            <div class="box-header"><h5 class="box-title">Transaction economics</h5></div>
            <div class="box-body space-y-3">
                @foreach([
                    'Customer transaction revenue' => $summary['transaction_revenue'],
                    'Provider/service cost' => -$summary['provider_cost'],
                    'Service charges included above' => $summary['service_charges'],
                    'Accrued referral commissions' => -$summary['commission_accrued'],
                    'Refunded transaction volume' => $summary['refunded_volume'],
                ] as $label => $value)
                    <div class="flex justify-between border-b pb-2"><span class="text-sm text-gray-600">{{ $label }}</span><strong class="{{ $value < 0 ? 'text-danger' : '' }}">₦{{ number_format($value, 2) }}</strong></div>
                @endforeach
            </div>
        </div>
        <div class="box">
            <div class="box-header"><h5 class="box-title">Marketing and funding</h5></div>
            <div class="box-body space-y-3">
                @foreach([
                    'Campaign wallet rewards converted' => $summary['campaign_wallet_expense'],
                    'Funding campaign rewards (already in margin)' => $summary['campaign_funding_rewards_in_funding_margin'],
                    'Upline funding bonuses' => $summary['upline_funding_bonus_expense'],
                    'Campaign liability movement in period' => $summary['campaign_liability_movement'],
                    'Current total bonus-wallet liability' => $summary['current_bonus_wallet_liability'],
                    'Expired bonus released' => $summary['expired_bonus_released'],
                ] as $label => $value)
                    <div class="flex justify-between border-b pb-2"><span class="text-sm text-gray-600">{{ $label }}</span><strong>₦{{ number_format($value, 2) }}</strong></div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="box mt-5">
        <div class="box-header"><h5 class="box-title">Service breakdown</h5></div>
        <div class="box-body overflow-auto">
            <table class="ti-custom-table ti-custom-table-head">
                <thead><tr><th>Service</th><th>Transactions</th><th>Revenue</th><th>Provider cost</th><th>Gross profit</th></tr></thead>
                <tbody>
                    @forelse($report['categories'] as $row)
                        <tr><td>{{ str($row['category'])->replace('_', ' ')->title() }}</td><td>{{ number_format($row['transactions']) }}</td><td>₦{{ number_format($row['revenue'], 2) }}</td><td>₦{{ number_format($row['provider_cost'], 2) }}</td><td class="{{ $row['gross_profit'] >= 0 ? 'text-success' : 'text-danger' }}">₦{{ number_format($row['gross_profit'], 2) }}</td></tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-8 text-gray-500">No successful transactions in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="box mt-5"><div class="box-body">
        <div class="grid md:grid-cols-4 gap-3 mb-4">
            <div><span class="text-xs text-gray-500">Successful</span><strong class="block">{{ number_format($report['counts']['successful_transactions']) }}</strong></div>
            <div><span class="text-xs text-gray-500">Failed</span><strong class="block">{{ number_format($report['counts']['failed_transactions']) }}</strong></div>
            <div><span class="text-xs text-gray-500">Fundings</span><strong class="block">{{ number_format($report['counts']['funding_transactions']) }}</strong></div>
            <div><span class="text-xs text-gray-500">Estimated-cost transactions</span><strong class="block">{{ number_format($report['counts']['estimated_provider_cost_transactions']) }}</strong></div>
        </div>
        @foreach($report['notes'] as $note)<p class="text-xs text-gray-500 mt-2">• {{ $note }}</p>@endforeach
    </div></div>
</div>
@endsection
