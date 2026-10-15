@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="page-header md:flex md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Salesperson Pricing</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-white/60">
                Give a salesperson an exact selling price for a specific plan. An active price overrides their normal customer-level price on plan lists and at checkout.
            </p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-md border border-success/20 bg-success/10 px-4 py-3 text-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session('failure'))
        <div class="mb-4 rounded-md border border-danger/20 bg-danger/10 px-4 py-3 text-danger" role="alert">
            {{ session('failure') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-md border border-danger/20 bg-danger/10 px-4 py-3 text-danger" role="alert">
            <p class="font-semibold">The pricing could not be saved.</p>
            <ul class="mt-1 list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 xl:col-span-4">
            <div class="box">
                <div class="box-header">
                    <div>
                        <h2 class="box-title">Set a salesperson price</h2>
                        <p class="mt-1 text-xs text-gray-500 dark:text-white/60">Saving the same salesperson and plan again updates the existing price.</p>
                    </div>
                </div>
                <div class="box-body">
                    <form method="POST" action="{{ route('admin.product_plan_custom_pricing.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="username" class="ti-form-label">Salesperson username</label>
                            <input id="username" name="username" type="text" autocomplete="off"
                                value="{{ old('username') }}" class="ti-form-input"
                                placeholder="e.g. salesagent01">
                            <p class="mt-1 text-xs text-gray-500">Use one exact username, or select several marked salespeople below.</p>
                        </div>

                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <label class="ti-form-label mb-0">Bulk select marked salespeople</label>
                                <div class="flex gap-2 text-xs">
                                    <button type="button" id="select-all-salespeople" class="font-semibold text-primary">Select all</button>
                                    <button type="button" id="clear-salespeople" class="font-semibold text-gray-500">Clear</button>
                                </div>
                            </div>
                            <div id="bulk-salespeople" class="mt-2 max-h-48 space-y-1 overflow-y-auto rounded-lg border border-gray-200 p-2 dark:border-white/10">
                                @forelse ($salespeople as $salesperson)
                                    <label class="flex cursor-pointer items-start gap-2 rounded p-2 hover:bg-gray-50 dark:hover:bg-white/5">
                                        <input type="checkbox" name="usernames[]" value="{{ $salesperson->username }}"
                                            class="ti-form-checkbox mt-0.5" @checked(in_array($salesperson->username, old('usernames', []), true))>
                                        <span class="min-w-0 text-sm">
                                            <span class="block font-semibold">{{ $salesperson->username }}</span>
                                            <span class="block truncate text-xs text-gray-500">{{ trim($salesperson->first_name.' '.$salesperson->last_name) }}{{ $salesperson->phone_number ? ' · '.$salesperson->phone_number : '' }}</span>
                                        </span>
                                    </label>
                                @empty
                                    <p class="p-2 text-xs text-gray-500">No accounts are marked as salespeople yet. Mark them from Admin → Users → Manage User.</p>
                                @endforelse
                            </div>
                            <p class="mt-1 text-xs text-gray-500">The selected plan price will be created or updated for every checked account.</p>
                        </div>

                        <div>
                            <label for="network-filter" class="ti-form-label">Filter plans by network</label>
                            <select id="network-filter" class="ti-form-select">
                                <option value="">All networks</option>
                                @foreach ($networks as $network)
                                    <option value="{{ $network->id }}">{{ $network->network_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="product-plan" class="ti-form-label">Product plan</label>
                            <select id="product-plan" name="product_plan_id" required class="ti-form-select">
                                <option value="">Select a plan</option>
                                @foreach ($product_plans as $plan)
                                    @php($network = $plan->product_plan_category?->network)
                                    <option value="{{ $plan->id }}"
                                        data-network-id="{{ $network?->id }}"
                                        @selected(old('product_plan_id') === $plan->id)>
                                        {{ $network?->network_name ? $network->network_name.' — ' : '' }}{{ $plan->product_plan_name }}{{ filled($plan->api_id) ? ' (API ID '.$plan->api_id.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="price" class="ti-form-label">Exact selling price (₦)</label>
                            <input id="price" name="price" type="number" required min="0" max="99999999.99"
                                step="0.01" inputmode="decimal" value="{{ old('price') }}"
                                class="ti-form-input" placeholder="550.00">
                            <p class="mt-1 text-xs text-gray-500">
                                Data prices must satisfy the protected minimum margin of ₦{{ number_format(\App\Http\Services\DataPlansService::DEFAULT_MINIMUM_DATA_PROFIT, 2) }} above cost where applicable.
                            </p>
                        </div>

                        <div>
                            <label for="status" class="ti-form-label">Status</label>
                            <select id="status" name="status" required class="ti-form-select">
                                <option value="1" @selected(old('status', '1') === '1')>Active — apply this price</option>
                                <option value="0" @selected(old('status') === '0')>Inactive — use normal level price</option>
                            </select>
                        </div>

                        <button type="submit" class="ti-btn ti-btn-primary w-full">Save salesperson price</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-span-12 xl:col-span-8">
            <div class="box">
                <div class="box-header">
                    <div>
                        <h2 class="box-title">Configured prices</h2>
                        <p class="mt-1 text-xs text-gray-500 dark:text-white/60">{{ $product_plan_custom_pricings->total() }} salesperson-plan price(s)</p>
                    </div>
                </div>
                <div class="box-body">
                    <form method="GET" action="{{ route('admin.product_plan_custom_pricing.index') }}" class="mb-5 grid grid-cols-1 gap-3 md:grid-cols-[1fr_180px_auto]">
                        <input type="search" name="search" value="{{ $search }}" class="ti-form-input"
                            placeholder="Search username, name, phone, plan or API ID">
                        <select name="status" class="ti-form-select">
                            <option value="">All statuses</option>
                            <option value="1" @selected(request('status') === '1')>Active</option>
                            <option value="0" @selected(request('status') === '0')>Inactive</option>
                        </select>
                        <div class="flex gap-2">
                            <button type="submit" class="ti-btn ti-btn-primary">Filter</button>
                            @if ($search !== '' || request()->filled('status'))
                                <a href="{{ route('admin.product_plan_custom_pricing.index') }}" class="ti-btn ti-btn-light">Clear</a>
                            @endif
                        </div>
                    </form>

                    <div class="space-y-3">
                        @forelse ($product_plan_custom_pricings as $pricing)
                            @php
                                $plan = $pricing->product_plan;
                                $user = $pricing->user;
                                $network = $plan?->product_plan_category?->network;
                            @endphp
                            <div class="rounded-lg border border-gray-200 p-4 dark:border-white/10">
                                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="font-semibold text-gray-800 dark:text-white">{{ $user?->username ?? 'Deleted user' }}</p>
                                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ (int) $pricing->status === 1 ? 'bg-success/10 text-success' : 'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-white/60' }}">
                                                {{ (int) $pricing->status === 1 ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-white/70">
                                            {{ trim(($user?->first_name ?? '').' '.($user?->last_name ?? '')) ?: 'No full name' }}
                                            @if ($user?->phone_number) · {{ $user->phone_number }} @endif
                                        </p>
                                        <p class="mt-2 font-medium text-gray-800 dark:text-white">
                                            {{ $network?->network_name ? $network->network_name.' — ' : '' }}{{ $plan?->product_plan_name ?? 'Deleted plan' }}
                                        </p>
                                        <p class="mt-1 text-xs text-gray-500">
                                            API ID: {{ filled($plan?->api_id) ? $plan->api_id : '—' }} · Customer level: {{ $user?->user_plan?->plan_level ?? '—' }}
                                        </p>
                                    </div>
                                    <div class="md:text-right">
                                        <p class="text-xs uppercase tracking-wide text-gray-500">Exact price</p>
                                        <p class="text-2xl font-bold text-primary">₦{{ number_format((float) $pricing->price, 2) }}</p>
                                        <p class="mt-1 text-xs text-gray-500">Updated {{ $pricing->updated_at?->diffForHumans() }}</p>
                                    </div>
                                </div>

                                <details class="mt-4 border-t border-gray-100 pt-3 dark:border-white/10">
                                    <summary class="cursor-pointer select-none text-sm font-semibold text-primary">Edit this price</summary>
                                    <form method="POST" action="{{ route('admin.product_plan_custom_pricing.update', ['id' => $pricing->id]) }}"
                                        class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
                                        @csrf
                                        @method('PUT')

                                        <div>
                                            <label class="ti-form-label">Salesperson username</label>
                                            <input name="username" type="text" required class="ti-form-input" value="{{ $user?->username }}">
                                        </div>
                                        <div>
                                            <label class="ti-form-label">Product plan</label>
                                            <select name="product_plan_id" required class="ti-form-select">
                                                @foreach ($product_plans as $editablePlan)
                                                    @php($editableNetwork = $editablePlan->product_plan_category?->network)
                                                    <option value="{{ $editablePlan->id }}" @selected($pricing->product_plan_id === $editablePlan->id)>
                                                        {{ $editableNetwork?->network_name ? $editableNetwork->network_name.' — ' : '' }}{{ $editablePlan->product_plan_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="ti-form-label">Exact selling price (₦)</label>
                                            <input name="price" type="number" required min="0" max="99999999.99" step="0.01"
                                                class="ti-form-input" value="{{ number_format((float) $pricing->price, 2, '.', '') }}">
                                        </div>
                                        <div>
                                            <label class="ti-form-label">Status</label>
                                            <select name="status" required class="ti-form-select">
                                                <option value="1" @selected((int) $pricing->status === 1)>Active — apply this price</option>
                                                <option value="0" @selected((int) $pricing->status === 0)>Inactive — use normal level price</option>
                                            </select>
                                        </div>
                                        <div class="md:col-span-2">
                                            <button type="submit" class="ti-btn ti-btn-primary">Update salesperson price</button>
                                        </div>
                                    </form>
                                </details>
                            </div>
                        @empty
                            <div class="rounded-lg border border-dashed border-gray-300 px-6 py-10 text-center text-gray-500 dark:border-white/10">
                                No salesperson pricing matches this filter.
                            </div>
                        @endforelse
                    </div>

                    @if ($product_plan_custom_pricings->hasPages())
                        <div class="mt-5">{{ $product_plan_custom_pricings->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const networkFilter = document.getElementById('network-filter');
        const planSelect = document.getElementById('product-plan');
        const salespersonCheckboxes = Array.from(document.querySelectorAll('#bulk-salespeople input[type="checkbox"]'));
        const selectAllSalespeople = document.getElementById('select-all-salespeople');
        const clearSalespeople = document.getElementById('clear-salespeople');

        selectAllSalespeople?.addEventListener('click', function () {
            salespersonCheckboxes.forEach(function (checkbox) { checkbox.checked = true; });
        });

        clearSalespeople?.addEventListener('click', function () {
            salespersonCheckboxes.forEach(function (checkbox) { checkbox.checked = false; });
        });

        if (!networkFilter || !planSelect) return;

        networkFilter.addEventListener('change', function () {
            const networkId = this.value;
            let selectedStillVisible = false;

            Array.from(planSelect.options).forEach(function (option, index) {
                if (index === 0) return;
                const visible = !networkId || option.dataset.networkId === networkId;
                option.hidden = !visible;
                option.disabled = !visible;
                if (visible && option.selected) selectedStillVisible = true;
            });

            if (!selectedStillVisible) planSelect.value = '';
        });
    });
</script>
@endsection
