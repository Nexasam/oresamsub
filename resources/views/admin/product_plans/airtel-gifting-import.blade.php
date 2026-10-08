@extends('layouts.app')

@section('content')
@php
    $summary = $result['summary'] ?? null;
    $hasBlockingIssues = $result && (! empty($result['errors']) || ! empty($result['parse_errors']) || (($summary['invalid'] ?? 0) > 0));
@endphp

<div class="main-content">
    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12">
            @if ($errors->any())
                <div class="bg-danger/10 border border-danger/10 alert text-danger" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="col-span-12">
            <div class="box">
                <div class="box-header flex items-start justify-between gap-3">
                    <div>
                        <h5 class="box-title">Airtel Gifting Plan Import</h5>
                        <p class="text-xs text-gray-500 mt-1">
                            Paste Airtel gifting plans, preview the calculated selling prices/profit, edit rows if needed, then apply.
                        </p>
                    </div>
                    <a href="{{ route('admin.product_plans.index2') }}" class="ti-btn ti-btn-light ti-btn-sm">Back to plans</a>
                </div>

                <div class="box-body">
                    <form method="POST" action="{{ route('admin.product_plans.airtel_gifting_import.preview') }}" class="space-y-4">
                        @csrf

                        <div class="rounded-lg border border-blue-100 bg-blue-50/60 p-3 text-xs text-blue-800">
                            <div class="font-semibold">DB target</div>
                            <div class="mt-1">
                                Choose the Network, Product, Product Plan Category, and Automation/source that will receive the imported rows.
                                If you choose an existing category, its product/network relationship is used for the foreign key.
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-gray-600">Network</label>
                                <select name="network_id" class="ti-form-select text-xs">
                                    <option value="">Auto: Airtel</option>
                                    @foreach($networks as $network)
                                        <option value="{{ $network->id }}" {{ (old('network_id', $options['network_id'] ?? '') == $network->id) ? 'selected' : '' }}>
                                            {{ $network->network_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600">Product</label>
                                <select name="product_id" class="ti-form-select text-xs">
                                    <option value="">Auto: Data</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ (old('product_id', $options['product_id'] ?? '') == $product->id) ? 'selected' : '' }}>
                                            {{ $product->product_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600">Product plan category</label>
                                <select name="category_id" class="ti-form-select text-xs">
                                    <option value="">Create/find by category name below</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ (old('category_id', $options['category_id'] ?? '') == $category->id) ? 'selected' : '' }}>
                                            {{ $category->product_plan_category_name }}
                                            — {{ $category->network->network_name ?? 'No network' }}
                                            / {{ $category->product->product_name ?? 'No product' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600">Automation/source</label>
                                <select name="automation_id" class="ti-form-select text-xs">
                                    <option value="">Auto: Oresamplug</option>
                                    @foreach($automations as $automation)
                                        <option value="{{ $automation->id }}" {{ (old('automation_id', $options['automation_id'] ?? '') == $automation->id) ? 'selected' : '' }}>
                                            {{ $automation->automation_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <input type="hidden" name="category" value="{{ old('category', $options['category'] ?? 'Airtel Gifting') }}">
                        <input type="hidden" name="source_slug" value="{{ old('source_slug', $options['source_slug'] ?? 'oresamplug') }}">
                        <input type="hidden" name="source_name" value="{{ old('source_name', $options['source_name'] ?? 'ORESAMPLUG AUTOMATION') }}">

                        <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-5 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-gray-600">Airtime cost per ₦100</label>
                                <input type="number" step="0.01" min="0" max="100" name="airtime_purchase_rate_per_100" value="{{ old('airtime_purchase_rate_per_100', $options['airtime_purchase_rate_per_100'] ?? 94) }}" class="ti-form-input text-xs">
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-gray-600">Discount per customer level</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2 mt-1">
                                @foreach(range(1, 7) as $level)
                                    <div>
                                        <label class="text-[10px] text-gray-500">Level {{ $level }} %</label>
                                        <input type="number"
                                               step="0.01"
                                               min="0"
                                               max="100"
                                               name="level_discount_percent_{{ $level }}"
                                               value="{{ old('level_discount_percent_'.$level, $options['level_discount_percent_'.$level] ?? 0.5) }}"
                                               class="ti-form-input text-xs">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <label class="flex items-center gap-2 text-xs text-gray-600">
                            <input type="checkbox" name="update_existing" value="1" {{ old('update_existing', $options['update_existing'] ?? false) ? 'checked' : '' }}>
                            Update matching existing plans instead of skipping them.
                        </label>

                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <label class="text-xs font-semibold text-gray-600">Plans text</label>
                                <span class="text-[11px] text-gray-500">Format: Public ID | Plan Name | Airtel API Code | Data Size | Validity | Original Price</span>
                            </div>
                            <textarea name="plans_text" rows="14" class="ti-form-input mt-1 font-mono text-xs">{{ $plansText }}</textarea>
                        </div>

                        <button type="submit" class="ti-btn ti-btn-primary">
                            Preview Import
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.product_plans.airtel_gifting_import.stale_preview') }}" class="mt-5 rounded-xl border border-orange-200 bg-orange-50 p-4 space-y-3">
                        @csrf
                        <input type="hidden" name="network_id" value="{{ $options['network_id'] ?? '' }}">
                        <input type="hidden" name="product_id" value="{{ $options['product_id'] ?? '' }}">
                        <input type="hidden" name="category_id" value="{{ $options['category_id'] ?? '' }}">
                        <input type="hidden" name="automation_id" value="{{ $options['automation_id'] ?? '' }}">
                        <input type="hidden" name="category" value="{{ $options['category'] ?? 'Airtel Gifting' }}">
                        <input type="hidden" name="source_slug" value="{{ $options['source_slug'] ?? 'oresamplug' }}">
                        <input type="hidden" name="source_name" value="{{ $options['source_name'] ?? 'ORESAMPLUG AUTOMATION' }}">
                        <input type="hidden" name="airtime_purchase_rate_per_100" value="{{ $options['airtime_purchase_rate_per_100'] ?? 94 }}">
                        @foreach(range(1, 7) as $level)
                            <input type="hidden" name="level_discount_percent_{{ $level }}" value="{{ $options['level_discount_percent_'.$level] ?? 0.5 }}">
                        @endforeach

                        <div>
                            <div class="font-semibold text-orange-800">Stale Airtel plan cleanup</div>
                            <p class="mt-1 text-xs text-orange-700">
                                Preview Airtel/Data plans with no successful purchase in the last 3 months. New Airtel gifting public IDs are excluded from this cleanup.
                            </p>
                        </div>
                        <button type="submit" class="ti-btn ti-btn-warning ti-btn-sm">
                            Preview stale Airtel plans
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @if(isset($deleteResult))
            <div class="col-span-12">
                <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-700">
                    Deleted {{ $deleteResult['deleted'] ?? 0 }} stale Airtel plan(s).
                </div>
            </div>
        @endif

        @if(isset($staleResult))
            <div class="col-span-12">
                <div class="box">
                    <div class="box-header">
                        <div>
                            <h5 class="box-title">Stale Airtel plans preview</h5>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $staleResult['summary']['total'] ?? 0 }} eligible plan(s). Cutoff:
                                {{ $staleResult['summary']['cutoff'] ?? '-' }}.
                            </p>
                        </div>
                    </div>
                    <div class="box-body space-y-4">
                        @foreach(($staleResult['errors'] ?? []) as $error)
                            <div class="rounded-lg bg-red-50 border border-red-200 text-red-700 p-2 text-xs">{{ $error }}</div>
                        @endforeach

                        <form method="POST" action="{{ route('admin.product_plans.airtel_gifting_import.stale_delete') }}" class="space-y-4">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="network_id" value="{{ $options['network_id'] ?? '' }}">
                            <input type="hidden" name="product_id" value="{{ $options['product_id'] ?? '' }}">
                            <input type="hidden" name="category_id" value="{{ $options['category_id'] ?? '' }}">
                            <input type="hidden" name="automation_id" value="{{ $options['automation_id'] ?? '' }}">
                            <input type="hidden" name="category" value="{{ $options['category'] ?? 'Airtel Gifting' }}">
                            <input type="hidden" name="source_slug" value="{{ $options['source_slug'] ?? 'oresamplug' }}">
                            <input type="hidden" name="source_name" value="{{ $options['source_name'] ?? 'ORESAMPLUG AUTOMATION' }}">
                            <input type="hidden" name="airtime_purchase_rate_per_100" value="{{ $options['airtime_purchase_rate_per_100'] ?? 94 }}">

                            <div class="overflow-x-auto border rounded-lg">
                                <table class="ti-custom-table ti-striped-table ti-custom-table-hover w-full text-xs">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Plan</th>
                                            <th>Category</th>
                                            <th>Plan ID</th>
                                            <th>Size</th>
                                            <th>Validity</th>
                                            <th>Successful buys</th>
                                            <th>Last success</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse(($staleResult['rows'] ?? []) as $row)
                                            <tr>
                                                <td><input type="checkbox" name="stale_plan_ids[]" value="{{ $row['id'] }}"></td>
                                                <td>{{ $row['name'] }}</td>
                                                <td>{{ $row['category'] }}</td>
                                                <td><code>{{ $row['automation_product_plan_id'] }}</code></td>
                                                <td>{{ $row['size_mb'] }}</td>
                                                <td>{{ $row['validity_days'] }}</td>
                                                <td>{{ $row['successful_purchase_count'] }}</td>
                                                <td>{{ $row['last_successful_purchase_at'] ?? 'Never' }}</td>
                                                <td>{{ $row['visibility'] == 1 ? 'Visible' : 'Hidden' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="py-5 text-center text-gray-500">No stale Airtel plans found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if(($staleResult['summary']['total'] ?? 0) > 0)
                                <label class="flex items-start gap-2 text-xs text-red-700">
                                    <input type="checkbox" name="confirm_delete" value="1" class="mt-1">
                                    <span>I understand this will delete only the selected plans that are still stale at delete time.</span>
                                </label>
                                <button type="submit" class="ti-btn ti-btn-danger">
                                    Delete selected stale plans
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @if ($result)
            <div class="col-span-12">
                <div class="box">
                    <div class="box-header">
                        <div>
                            <h5 class="box-title">{{ $mode === 'apply' ? 'Apply Result' : 'Preview Result' }}</h5>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $summary['total'] ?? 0 }} rows ·
                                {{ $summary['new'] ?? 0 }} new ·
                                {{ $summary['existing'] ?? 0 }} existing ·
                                {{ $summary['created'] ?? 0 }} created ·
                                {{ $summary['updated'] ?? 0 }} updated ·
                                {{ $summary['invalid'] ?? 0 }} invalid
                            </p>
                        </div>
                    </div>
                    <div class="box-body space-y-4">
                        @foreach (($result['errors'] ?? []) as $error)
                            <div class="rounded-lg bg-red-50 border border-red-200 text-red-700 p-2 text-xs">{{ $error }}</div>
                        @endforeach
                        @foreach (($result['warnings'] ?? []) as $warning)
                            <div class="rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-700 p-2 text-xs">{{ $warning }}</div>
                        @endforeach
                        @foreach (($result['parse_errors'] ?? []) as $error)
                            <div class="rounded-lg bg-red-50 border border-red-200 text-red-700 p-2 text-xs">{{ $error }}</div>
                        @endforeach

                        <form method="POST" action="{{ route('admin.product_plans.airtel_gifting_import.apply') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="network_id" value="{{ $options['network_id'] ?? '' }}">
                            <input type="hidden" name="product_id" value="{{ $options['product_id'] ?? '' }}">
                            <input type="hidden" name="category_id" value="{{ $options['category_id'] ?? '' }}">
                            <input type="hidden" name="automation_id" value="{{ $options['automation_id'] ?? '' }}">
                            <input type="hidden" name="category" value="{{ $options['category'] ?? 'Airtel Gifting' }}">
                            <input type="hidden" name="source_slug" value="{{ $options['source_slug'] ?? 'oresamplug' }}">
                            <input type="hidden" name="source_name" value="{{ $options['source_name'] ?? 'ORESAMPLUG AUTOMATION' }}">
                            @foreach(range(1, 7) as $level)
                                <input type="hidden" name="level_discount_percent_{{ $level }}" value="{{ $options['level_discount_percent_'.$level] ?? 0.5 }}">
                            @endforeach
                            <input type="hidden" name="airtime_purchase_rate_per_100" value="{{ $options['airtime_purchase_rate_per_100'] ?? 94 }}">
                            @if($options['update_existing'] ?? false)
                                <input type="hidden" name="update_existing" value="1">
                            @endif

                            <div class="overflow-x-auto border rounded-lg">
                                <table class="ti-custom-table ti-striped-table ti-custom-table-hover w-full text-xs">
                                    <thead>
                                        <tr>
                                            <th>Public ID</th>
                                            <th>Plan</th>
                                            <th>Airtel API Code</th>
                                            <th>Size (MB)</th>
                                            <th>Validity</th>
                                            <th>Original</th>
                                            <th>Cost @ ₦{{ number_format((float) ($options['airtime_purchase_rate_per_100'] ?? 94), 2) }}/₦100</th>
                                            @foreach(range(1, 7) as $level)
                                                <th>L{{ $level }}</th>
                                            @endforeach
                                            <th>Profit range</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse(($result['rows'] ?? []) as $index => $row)
                                            @php
                                                $profits = collect($row['profit_by_level'] ?? [])->map(fn ($profit) => (float) $profit);
                                                $lowestProfit = $profits->min() ?? 0;
                                                $highestProfit = $profits->max() ?? 0;
                                                $rowIsEditable = $row['is_editable'] ?? true;
                                                $readOnlyAttrs = $rowIsEditable ? '' : 'readonly';
                                                $readOnlyClass = $rowIsEditable ? '' : 'bg-gray-100 text-gray-500';
                                            @endphp
                                            <tr>
                                                <td>
                                                    <input name="rows[{{ $index }}][public_id]" value="{{ $row['public_id'] }}" class="ti-form-input py-1 text-xs min-w-[70px] {{ $readOnlyClass }}" {!! $readOnlyAttrs !!}>
                                                    @if(! $rowIsEditable)
                                                        <div class="mt-1 text-[10px] text-orange-600">Locked</div>
                                                    @endif
                                                </td>
                                                <td><input name="rows[{{ $index }}][name]" value="{{ $row['name'] }}" class="ti-form-input py-1 text-xs min-w-[170px] {{ $readOnlyClass }}" {!! $readOnlyAttrs !!}></td>
                                                <td><input name="rows[{{ $index }}][code]" value="{{ $row['code'] }}" class="ti-form-input py-1 text-xs min-w-[150px] {{ $readOnlyClass }}" {!! $readOnlyAttrs !!}></td>
                                                <td><input name="rows[{{ $index }}][size]" value="{{ $row['size'] }}" class="ti-form-input py-1 text-xs min-w-[80px] {{ $readOnlyClass }}" {!! $readOnlyAttrs !!}></td>
                                                <td><input name="rows[{{ $index }}][validity]" value="{{ $row['validity'] }}" class="ti-form-input py-1 text-xs min-w-[70px] {{ $readOnlyClass }}" {!! $readOnlyAttrs !!}></td>
                                                <td><input name="rows[{{ $index }}][price]" value="{{ $row['price'] }}" class="ti-form-input py-1 text-xs min-w-[80px] {{ $readOnlyClass }}" {!! $readOnlyAttrs !!}></td>
                                                <td>₦{{ number_format((float) ($row['effective_airtime_cost'] ?? 0), 2) }}</td>
                                                @foreach(range(1, 7) as $level)
                                                    <td>
                                                        <input type="number"
                                                               step="0.01"
                                                               min="0"
                                                               name="rows[{{ $index }}][level_price_{{ $level }}]"
                                                               value="{{ number_format((float) ($row['level_prices'][$level] ?? 0), 2, '.', '') }}"
                                                               class="ti-form-input py-1 text-xs min-w-[82px] {{ $readOnlyClass }}"
                                                               {!! $readOnlyAttrs !!}>
                                                        <div class="text-[10px] {{ (($row['profit_by_level'][$level] ?? 0) < 0) ? 'text-red-600' : 'text-green-600' }}">
                                                            ₦{{ number_format((float) ($row['profit_by_level'][$level] ?? 0), 2) }}
                                                        </div>
                                                    </td>
                                                @endforeach
                                                <td>
                                                    <span class="{{ $lowestProfit < 0 ? 'text-red-600' : 'text-green-600' }}">
                                                        ₦{{ number_format($lowestProfit, 2) }} - ₦{{ number_format($highestProfit, 2) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="rounded px-2 py-1 text-[10px] font-semibold {{ in_array($row['status'], ['new', 'created', 'updated_existing'], true) ? 'bg-green-100 text-green-700' : ($row['status'] === 'invalid' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                                        {{ $row['status'] }}
                                                    </span>
                                                    @if(! empty($row['reason']))
                                                        <div class="text-[10px] text-gray-500 mt-1">{{ $row['reason'] }}</div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="17" class="text-center text-gray-500 py-5">No rows found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if ($mode !== 'apply')
                                <div class="rounded-lg bg-blue-50 border border-blue-200 text-blue-700 p-3 text-xs">
                                    You can edit any row in this preview table before saving. If you edit pricing inputs here, click <b>Preview Import</b> again only if you want recalculated profit before applying.
                                </div>

                                <button type="submit" class="ti-btn ti-btn-success" {{ $hasBlockingIssues ? 'disabled' : '' }}>
                                    Apply Import
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
