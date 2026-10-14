@extends('layouts.app')

@section('content')
@php
    $summary = $report['summary'];
    $selectedPlanIds = $filters['plan_ids'] ?? [];
    $currency = fn ($value) => '₦'.number_format((float) $value, 2);
    $number = fn ($value, $decimals = 0) => number_format((float) $value, $decimals);
    $dateValue = fn ($date) => $date instanceof \Carbon\CarbonInterface ? $date->format('Y-m-d') : $date;
@endphp

<style>
    .sales-report-page {
        --sales-border: #e8edf3;
        --sales-muted: #64748b;
    }

    .sales-report-page .sales-page-heading {
        min-width: 0;
    }

    .sales-report-page .sales-filter-card .card-body {
        padding: 1.25rem;
    }

    .sales-report-page .sales-filter-card .form-label {
        margin-bottom: .4rem;
        font-size: .78rem;
        font-weight: 700;
        letter-spacing: .025em;
        color: #475569;
    }

    .sales-report-page .sales-filter-card .form-control,
    .sales-report-page .sales-filter-card .form-select,
    .sales-report-page .select2-container .select2-selection--single {
        min-height: 42px;
    }

    .sales-report-page .plan-picker {
        position: relative;
    }

    .sales-report-page .plan-picker-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        min-height: 42px;
        gap: .75rem;
        padding: .5rem .75rem;
        overflow: hidden;
        text-align: left;
        background: var(--bs-body-bg, #fff);
        border: 1px solid #d9dde3;
        border-radius: .375rem;
        color: var(--bs-body-color, #212529);
    }

    .sales-report-page .plan-picker-toggle:hover,
    .sales-report-page .plan-picker-toggle:focus {
        border-color: #86b7fe;
    }

    .sales-report-page .plan-picker-label {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sales-report-page .plan-picker-menu {
        position: absolute;
        z-index: 1055;
        top: calc(100% + .35rem);
        right: 0;
        left: 0;
        padding: .75rem;
        background: var(--bs-body-bg, #fff);
        border: 1px solid var(--sales-border);
        border-radius: .5rem;
        box-shadow: 0 .75rem 2rem rgba(15, 23, 42, .16);
    }

    .sales-report-page .plan-picker-list {
        max-height: 280px;
        margin-top: .65rem;
        overflow-y: auto;
        overscroll-behavior: contain;
    }

    .sales-report-page .plan-picker-option {
        display: flex;
        align-items: flex-start;
        gap: .6rem;
        margin: 0;
        padding: .55rem .35rem;
        border-bottom: 1px solid var(--sales-border);
        cursor: pointer;
        font-size: .85rem;
        line-height: 1.3;
    }

    .sales-report-page .plan-picker-option:last-child {
        border-bottom: 0;
    }

    .sales-report-page .plan-picker-option input {
        flex: 0 0 auto;
        margin-top: .15rem;
    }

    .sales-report-page .plan-picker-empty {
        padding: 1rem;
        color: var(--sales-muted);
        text-align: center;
    }

    .sales-report-page .sales-summary-card {
        border: 1px solid var(--sales-border);
        overflow: hidden;
    }

    .sales-report-page .sales-summary-label {
        display: block;
        min-height: 2.2em;
        color: var(--sales-muted);
        font-size: .78rem;
        font-weight: 700;
        line-height: 1.1;
        text-transform: uppercase;
        letter-spacing: .035em;
    }

    .sales-report-page .sales-summary-value {
        margin: .55rem 0 0;
        font-size: clamp(1.15rem, 1.7vw, 1.65rem);
        line-height: 1.15;
        overflow-wrap: anywhere;
    }

    .sales-report-page .sales-highlight-content {
        min-width: 0;
    }

    .sales-report-page .sales-highlight-content h4,
    .sales-report-page .sales-highlight-content p {
        overflow-wrap: anywhere;
    }

    .sales-report-page .sales-highlight-stat {
        flex: 0 0 auto;
        white-space: nowrap;
    }

    .sales-report-page .sales-table th {
        white-space: nowrap;
        font-size: .76rem;
        text-transform: uppercase;
        letter-spacing: .025em;
        color: #64748b;
    }

    .sales-report-page .sales-table td {
        vertical-align: middle;
    }

    .sales-report-page .plan-cell {
        min-width: 230px;
        max-width: 360px;
    }

    .sales-report-page .customer-cell {
        min-width: 200px;
    }

    @media (max-width: 767.98px) {
        .sales-report-page .sales-filter-card .card-body {
            padding: 1rem;
        }

        .sales-report-page .sales-page-actions,
        .sales-report-page .sales-page-actions .btn {
            width: 100%;
        }

        .sales-report-page .sales-highlight-row {
            align-items: flex-start !important;
            flex-direction: column;
        }

        .sales-report-page .sales-highlight-stat {
            text-align: left !important;
        }
    }
</style>

<div class="main-content app-content sales-report-page">
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div class="sales-page-heading">
                <h1 class="h3 mb-1">Sales Calculator</h1>
                <p class="text-muted mb-0">Track daily sales, plan count, averages, and your highest buying customers.</p>
            </div>
            <div class="sales-page-actions">
                <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-secondary">
                    View Transactions
                </a>
            </div>
        </div>

        <div class="card custom-card sales-filter-card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.sales.v2') }}" class="row g-3 align-items-end">
                    <div class="col-sm-6 col-xl-2">
                        <label class="form-label">Period</label>
                        <select name="period" class="form-select">
                            @foreach($periods as $value => $label)
                                <option value="{{ $value }}" @selected($filters['period'] === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-6 col-xl-2">
                        <label class="form-label">From</label>
                        <input type="date" name="date_from" value="{{ old('date_from', $dateValue($filters['date_from'])) }}" class="form-control">
                    </div>

                    <div class="col-sm-6 col-xl-2">
                        <label class="form-label">To</label>
                        <input type="date" name="date_to" value="{{ old('date_to', $dateValue($filters['date_to'])) }}" class="form-control">
                    </div>

                    <div class="col-sm-6 col-xl-3">
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

                    <div class="col-sm-6 col-xl-3">
                        <label class="form-label">Product type</label>
                        <select name="product_type" class="form-select">
                            <option value="">All product types</option>
                            @foreach($productTypes as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['product_type'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-6 col-xl-3">
                        <label class="form-label">Network</label>
                        <select name="network_id" class="form-select">
                            <option value="">All networks</option>
                            @foreach($networks as $network)
                                <option value="{{ $network->id }}" @selected(($filters['network_id'] ?? '') === $network->id)>{{ $network->network_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-6 col-xl-3">
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

                    <div class="col-xl-6">
                        <label class="form-label">Specific plans</label>
                        <div class="plan-picker" id="sales-plan-picker">
                            <button type="button" class="plan-picker-toggle" aria-expanded="false" aria-controls="sales-plan-menu">
                                <span class="plan-picker-label">{{ count($selectedPlanIds) ? count($selectedPlanIds).' plan(s) selected' : 'All plans' }}</span>
                                <span aria-hidden="true">&#9662;</span>
                            </button>

                            <div class="plan-picker-menu" id="sales-plan-menu" hidden>
                                <div class="d-flex gap-2">
                                    <input type="search" class="form-control form-control-sm plan-picker-search" placeholder="Search plan name or API ID..." autocomplete="off">
                                    <button type="button" class="btn btn-sm btn-outline-secondary plan-picker-clear">Clear</button>
                                </div>

                                <div class="plan-picker-list">
                                    @foreach($plans as $plan)
                                        @php($planLabel = $plan->product_plan_name.($plan->api_id ? ' · API '.$plan->api_id : ''))
                                        <label class="plan-picker-option" data-search="{{ Str::lower($planLabel) }}">
                                            <input
                                                type="checkbox"
                                                class="form-check-input plan-picker-checkbox"
                                                name="plan_ids[]"
                                                value="{{ $plan->id }}"
                                                @checked(in_array($plan->id, $selectedPlanIds, true))
                                            >
                                            <span>{{ $planLabel }}</span>
                                        </label>
                                    @endforeach
                                    <div class="plan-picker-empty" hidden>No matching plans found.</div>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Search and select one or more plans, or leave empty for all plans.</small>
                    </div>

                    <div class="col-sm-6 col-xl-3 d-grid">
                        <button class="btn btn-primary">Calculate</button>
                    </div>

                    <div class="col-sm-6 col-xl-3 d-grid">
                        <a href="{{ route('admin.reports.sales.v2') }}" class="btn btn-light">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-4 col-xl-2">
                <div class="card custom-card sales-summary-card h-100">
                    <div class="card-body">
                        <span class="sales-summary-label">Sales Value</span>
                        <h3 class="sales-summary-value">{{ $currency($summary['total_sales']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 col-xl-2">
                <div class="card custom-card sales-summary-card h-100">
                    <div class="card-body">
                        <span class="sales-summary-label">Transactions</span>
                        <h3 class="sales-summary-value">{{ $number($summary['quantity_sold']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 col-xl-2">
                <div class="card custom-card sales-summary-card h-100">
                    <div class="card-body">
                        <span class="sales-summary-label">Data Volume</span>
                        <h3 class="sales-summary-value">{{ $number($summary['total_volume_gb'], 2) }}GB</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 col-xl-2">
                <div class="card custom-card sales-summary-card h-100">
                    <div class="card-body">
                        <span class="sales-summary-label">Avg Count/Day</span>
                        <h3 class="sales-summary-value">{{ $number($summary['average_count_per_day'], 1) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 col-xl-2">
                <div class="card custom-card sales-summary-card h-100">
                    <div class="card-body">
                        <span class="sales-summary-label">Customers</span>
                        <h3 class="sales-summary-value">{{ $number($summary['customer_count']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 col-xl-2">
                <div class="card custom-card sales-summary-card h-100">
                    <div class="card-body">
                        <span class="sales-summary-label">Plans Sold</span>
                        <h3 class="sales-summary-value">{{ $number($summary['product_count']) }}</h3>
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
                            <div class="d-flex justify-content-between gap-3 sales-highlight-row">
                                <div class="sales-highlight-content">
                                    <h4 class="mb-1">{{ $summary['top_customer']['username'] }}</h4>
                                    <p class="text-muted mb-0">{{ $summary['top_customer']['name'] ?: $summary['top_customer']['email'] }}</p>
                                </div>
                                <div class="text-end sales-highlight-stat">
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
                            <div class="d-flex justify-content-between gap-3 sales-highlight-row">
                                <div class="sales-highlight-content">
                                    <h4 class="mb-1">{{ $summary['top_product']['plan_name'] }}</h4>
                                    <p class="text-muted mb-0">
                                        {{ $summary['top_product']['network_name'] ?: 'No network' }}
                                        @if($summary['top_product']['category_name'])
                                            · {{ $summary['top_product']['category_name'] }}
                                        @endif
                                    </p>
                                </div>
                                <div class="text-end sales-highlight-stat">
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
                    <table class="table table-hover align-middle mb-0 sales-table">
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
                                    <td class="plan-cell">
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
                            <table class="table table-hover align-middle mb-0 sales-table">
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
                                            <td class="customer-cell">
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
                            <table class="table table-sm align-middle mb-0 sales-table">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const picker = document.getElementById('sales-plan-picker');

        if (!picker) {
            return;
        }

        const toggle = picker.querySelector('.plan-picker-toggle');
        const menu = picker.querySelector('.plan-picker-menu');
        const search = picker.querySelector('.plan-picker-search');
        const clear = picker.querySelector('.plan-picker-clear');
        const label = picker.querySelector('.plan-picker-label');
        const checkboxes = Array.from(picker.querySelectorAll('.plan-picker-checkbox'));
        const options = Array.from(picker.querySelectorAll('.plan-picker-option'));
        const empty = picker.querySelector('.plan-picker-empty');

        const updateLabel = () => {
            const selected = checkboxes.filter((checkbox) => checkbox.checked);
            label.textContent = selected.length ? `${selected.length} plan(s) selected` : 'All plans';
        };

        const closeMenu = () => {
            menu.hidden = true;
            toggle.setAttribute('aria-expanded', 'false');
        };

        toggle.addEventListener('click', function () {
            const willOpen = menu.hidden;
            menu.hidden = !willOpen;
            toggle.setAttribute('aria-expanded', String(willOpen));

            if (willOpen) {
                search.focus();
            }
        });

        search.addEventListener('input', function () {
            const term = search.value.trim().toLowerCase();
            let visibleCount = 0;

            options.forEach((option) => {
                const visible = option.dataset.search.includes(term);
                option.hidden = !visible;
                visibleCount += visible ? 1 : 0;
            });

            empty.hidden = visibleCount !== 0;
        });

        clear.addEventListener('click', function () {
            checkboxes.forEach((checkbox) => {
                checkbox.checked = false;
            });
            updateLabel();
        });

        checkboxes.forEach((checkbox) => checkbox.addEventListener('change', updateLabel));

        document.addEventListener('click', function (event) {
            if (!picker.contains(event.target)) {
                closeMenu();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeMenu();
                toggle.focus();
            }
        });

        updateLabel();
    });
</script>
@endpush
