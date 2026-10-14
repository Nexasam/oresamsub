@extends('layouts.app')

@section('content')
@php
    $summary = $report['summary'];
    $selectedPlanIds = $filters['plan_ids'] ?? [];
    $currency = fn ($value) => '₦'.number_format((float) $value, 2);
    $number = fn ($value, $decimals = 0) => number_format((float) $value, $decimals);
    $dateValue = fn ($date) => $date instanceof \Carbon\CarbonInterface ? $date->format('Y-m-d') : $date;
@endphp

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">Sales Calculator</h1>
                <p class="text-muted mb-0">Track daily sales, plan count, averages, and your highest buying customers.</p>
            </div>
            <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-secondary">
                View Transactions
            </a>
        </div>

        <div class="card custom-card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.sales.v2') }}" class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">Period</label>
                        <select name="period" class="form-select">
                            @foreach($periods as $value => $label)
                                <option value="{{ $value }}" @selected($filters['period'] === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">From</label>
                        <input type="date" name="date_from" value="{{ old('date_from', $dateValue($filters['date_from'])) }}" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">To</label>
                        <input type="date" name="date_to" value="{{ old('date_to', $dateValue($filters['date_to'])) }}" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Customer</label>
                        <select name="user_id" class="form-select">
                            <option value="">All customers</option>
                            @foreach($users as $user)
                                @php($fullName = trim($user->first_name.' '.$user->last_name))
                                <option value="{{ $user->id }}" @selected(($filters['user_id'] ?? '') === $user->id)>
                                    {{ $user->username }}{{ $fullName ? ' — '.$fullName : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Product type</label>
                        <select name="product_type" class="form-select">
                            <option value="">All product types</option>
                            @foreach($productTypes as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['product_type'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Network</label>
                        <select name="network_id" class="form-select">
                            <option value="">All networks</option>
                            @foreach($networks as $network)
                                <option value="{{ $network->id }}" @selected(($filters['network_id'] ?? '') === $network->id)>{{ $network->network_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">All categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(($filters['category_id'] ?? '') === $category->id)>
                                    {{ $category->product_plan_category_name }}
                                    @if($category->network)
                                        — {{ $category->network->network_name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Specific plans</label>
                        <select name="plan_ids[]" class="form-select" multiple size="4">
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}" @selected(in_array($plan->id, $selectedPlanIds, true))>
                                    {{ $plan->product_plan_name }}{{ $plan->api_id ? ' · API '.$plan->api_id : '' }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to choose multiple plans.</small>
                    </div>

                    <div class="col-md-2 d-grid">
                        <button class="btn btn-primary">Calculate</button>
                    </div>

                    <div class="col-md-2 d-grid">
                        <a href="{{ route('admin.reports.sales.v2') }}" class="btn btn-light">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4 col-xl-2">
                <div class="card custom-card h-100">
                    <div class="card-body">
                        <span class="text-muted">Sales Value</span>
                        <h3 class="mt-2 mb-0">{{ $currency($summary['total_sales']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xl-2">
                <div class="card custom-card h-100">
                    <div class="card-body">
                        <span class="text-muted">Transactions</span>
                        <h3 class="mt-2 mb-0">{{ $number($summary['quantity_sold']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xl-2">
                <div class="card custom-card h-100">
                    <div class="card-body">
                        <span class="text-muted">Data Volume</span>
                        <h3 class="mt-2 mb-0">{{ $number($summary['total_volume_gb'], 2) }}GB</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xl-2">
                <div class="card custom-card h-100">
                    <div class="card-body">
                        <span class="text-muted">Avg Count/Day</span>
                        <h3 class="mt-2 mb-0">{{ $number($summary['average_count_per_day'], 1) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xl-2">
                <div class="card custom-card h-100">
                    <div class="card-body">
                        <span class="text-muted">Customers</span>
                        <h3 class="mt-2 mb-0">{{ $number($summary['customer_count']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xl-2">
                <div class="card custom-card h-100">
                    <div class="card-body">
                        <span class="text-muted">Plans Sold</span>
                        <h3 class="mt-2 mb-0">{{ $number($summary['product_count']) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="card custom-card h-100">
                    <div class="card-body">
                        <h5 class="mb-3">Top Buyer</h5>
                        @if($summary['top_customer'])
                            <div class="d-flex justify-content-between gap-3">
                                <div>
                                    <h4 class="mb-1">{{ $summary['top_customer']['username'] }}</h4>
                                    <p class="text-muted mb-0">{{ $summary['top_customer']['name'] ?: $summary['top_customer']['email'] }}</p>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">{{ $number($summary['top_customer']['quantity_sold']) }} txns</div>
                                    <div>{{ $currency($summary['top_customer']['total_sales']) }}</div>
                                </div>
                            </div>
                        @else
                            <p class="text-muted mb-0">No successful sales for this filter.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card custom-card h-100">
                    <div class="card-body">
                        <h5 class="mb-3">Best Selling Plan</h5>
                        @if($summary['top_product'])
                            <div class="d-flex justify-content-between gap-3">
                                <div>
                                    <h4 class="mb-1">{{ $summary['top_product']['plan_name'] }}</h4>
                                    <p class="text-muted mb-0">
                                        {{ $summary['top_product']['network_name'] ?: 'No network' }}
                                        @if($summary['top_product']['category_name'])
                                            · {{ $summary['top_product']['category_name'] }}
                                        @endif
                                    </p>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">{{ $number($summary['top_product']['quantity_sold']) }} sold</div>
                                    <div>{{ $currency($summary['top_product']['total_sales']) }}</div>
                                </div>
                            </div>
                        @else
                            <p class="text-muted mb-0">No successful sales for this filter.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card custom-card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Plan Performance</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Plan</th>
                                <th>Network / Category</th>
                                <th class="text-end">Sold</th>
                                <th class="text-end">Avg/Day</th>
                                <th class="text-end">Volume</th>
                                <th class="text-end">Sales</th>
                                <th>Top Buyer</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report['products'] as $product)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $product['plan_name'] }}</div>
                                        <small class="text-muted">
                                            {{ ucfirst(str_replace('_', ' ', $product['product_type'])) }}
                                            @if($product['volume_mb'])
                                                · {{ $product['volume_mb'] >= 1000 ? $number($product['volume_mb'] / 1000, 2).'GB' : $number($product['volume_mb']).'MB' }}
                                            @endif
                                            @if($product['validity_days'])
                                                · {{ $product['validity_days'] }} day(s)
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <div>{{ $product['network_name'] ?: '—' }}</div>
                                        <small class="text-muted">{{ $product['category_name'] ?: 'No category' }}</small>
                                    </td>
                                    <td class="text-end fw-semibold">{{ $number($product['quantity_sold']) }}</td>
                                    <td class="text-end">{{ $number($product['quantity_sold'] / max(1, $summary['days']), 1) }}</td>
                                    <td class="text-end">{{ $number($product['total_volume_gb'], 2) }}GB</td>
                                    <td class="text-end">{{ $currency($product['total_sales']) }}</td>
                                    <td>
                                        @if($product['top_buyer'])
                                            <div>{{ $product['top_buyer']['username'] }}</div>
                                            <small class="text-muted">{{ $number($product['top_buyer']['quantity_sold']) }} buys</small>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No plan sales found for this filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-7">
                <div class="card custom-card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Top Customers</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th class="text-end">Transactions</th>
                                        <th class="text-end">Plans</th>
                                        <th class="text-end">Sales</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($report['customers'] as $customer)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $customer['username'] }}</div>
                                                <small class="text-muted">
                                                    {{ $customer['name'] ?: $customer['email'] }}
                                                    @if($customer['phone_number'])
                                                        · {{ $customer['phone_number'] }}
                                                    @endif
                                                </small>
                                            </td>
                                            <td class="text-end">{{ $number($customer['quantity_sold']) }}</td>
                                            <td class="text-end">{{ $number($customer['distinct_plans']) }}</td>
                                            <td class="text-end">{{ $currency($customer['total_sales']) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">No customer sales found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="card custom-card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Daily Trend</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Day</th>
                                        <th class="text-end">Txns</th>
                                        <th class="text-end">Volume</th>
                                        <th class="text-end">Sales</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($report['daily_trend'] as $day)
                                        <tr>
                                            <td>
                                                <div>{{ $day['label'] }}</div>
                                                <small class="text-muted">{{ $day['date'] }}</small>
                                            </td>
                                            <td class="text-end">{{ $number($day['quantity_sold']) }}</td>
                                            <td class="text-end">{{ $number($day['total_volume_gb'], 2) }}GB</td>
                                            <td class="text-end">{{ $currency($day['total_sales']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
