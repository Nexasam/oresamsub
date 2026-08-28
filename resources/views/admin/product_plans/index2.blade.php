@extends('layouts.app')
@section('content')

<div
    class="main-content"
    x-data="productPlansManager()"
>

    <div class="grid grid-cols-12 gap-1">

        {{-- ALERTS --}}
        <div class="col-span-12">
            @if (Session::has('success'))
                <div class="bg-success/10 border border-success/10 text-success p-2 text-sm">
                    {{ Session::get('success') }}
                </div>
            @endif

            @if (Session::has('failure'))
                <div class="bg-danger/10 border border-danger/10 text-danger p-2 text-sm">
                    {{ Session::get('failure') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-danger/10 border border-danger/10 text-danger p-2 text-sm">
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

                <div class="box-header py-2 flex justify-between items-center">
                    <h5 class="box-title text-sm font-semibold">Product Plans</h5>
                </div>

                <div class="box-body p-2">

                    {{-- FILTERS --}}
                    <form method="GET" class="mb-3">

                        <div class="grid grid-cols-2 md:grid-cols-6 gap-2">

                            <input type="text"
                                   name="product_plan_name"
                                   value="{{ request('product_plan_name') }}"
                                   class="ti-form-input py-1 text-xs"
                                   placeholder="Search...">

                            <select name="automation_id" class="ti-form-select py-1 text-xs">
                                <option value="">Automation</option>
                                @foreach($automations as $auto)
                                    <option value="{{ $auto->id }}" {{ request('automation_id') == $auto->id ? 'selected' : '' }}>
                                        {{ $auto->automation_name }}
                                    </option>
                                @endforeach
                            </select>

                            <select name="product_id" class="ti-form-select py-1 text-xs">
                                <option value="">Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->product_name }}
                                    </option>
                                @endforeach
                            </select>

                            <select name="network_id" class="ti-form-select py-1 text-xs">
                                <option value="">Network</option>
                                @foreach($networks as $network)
                                    <option value="{{ $network->id }}" {{ request('network_id') == $network->id ? 'selected' : '' }}>
                                        {{ $network->network_name }}
                                    </option>
                                @endforeach
                            </select>

                            <input type="date" name="from_date"
                                   value="{{ request('from_date') }}"
                                   class="ti-form-input py-1 text-xs">

                            <input type="date" name="to_date"
                                   value="{{ request('to_date') }}"
                                   class="ti-form-input py-1 text-xs">


                            
                            <input type="text" name="validity_in_days"
                            value="{{ request('validity_in_days') }}" placeholder="Validity"
                            class="ti-form-input py-1 text-xs">

                            <input type="text" name="data_size_in_mb"
                            value="{{ request('data_size_in_mb') }}"  placeholder="data size"
                            class="ti-form-input py-1 text-xs">

                            <select name="visibility" class="ti-form-select py-1 text-xs">
                                <option value="">All statuses</option>
                                <option value="1" {{ request('visibility') === '1' ? 'selected' : '' }}>ON</option>
                                <option value="0" {{ request('visibility') === '0' ? 'selected' : '' }}>OFF</option>
                            </select>

                            <select name="tracking" class="ti-form-select py-1 text-xs">
                                <option value="">All tracking</option>
                                <option value="tracked" {{ request('tracking') === 'tracked' ? 'selected' : '' }}>Tracked (30 days)</option>
                                <option value="untracked" {{ request('tracking') === 'untracked' ? 'selected' : '' }}>Not tracked (30 days)</option>
                            </select>

                            <select name="per_page" class="ti-form-select py-1 text-xs">
                                <option value="50" {{ request('per_page', 500) == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page', 500) == 100 ? 'selected' : '' }}>100</option>
                                <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                                <option value="500" {{ ! in_array((int) request('per_page', 500), [50, 100, 200, 500], true) || (int) request('per_page', 500) === 500 ? 'selected' : '' }}>500</option>
                            </select>

                            <div class="flex gap-2 items-center">
                                <button class="ti-btn ti-btn-primary ti-btn-sm">Filter</button>
                                <a href="{{ route('admin.product_plans.index2') }}"
                                   class="ti-btn ti-btn-light ti-btn-sm">Reset</a>
                            </div>

                        </div>
                    </form>

                    {{-- TABLE --}}
                    <div class="overflow-x-auto border rounded-sm">

                        <table class="ti-custom-table ti-striped-table ti-custom-table-hover w-full text-xs">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Plan</th>
                                    <th>API ID</th>
                                    <th>Providers</th>
                                    <th>Best provider · 30 days</th>
                                    <th>Network</th>
                                    <th>Validity</th>
                                    <th>Cost</th>
                                    <th>Selling</th>
                                    <th></th>
                                </tr>
                            </thead>
                        
                            <tbody>
                                @forelse($data as $plan)
                                    <tr>
                        
                                        <td>
                                            {{ $data->firstItem() + $loop->index }}
                                        </td>
                        
                                        <td>
                                            <div class="font-semibold">
                                                {{ $plan->product_plan_name }} <br>
                                                {{ $plan->provider_mappings->count() .' providers'}} <br>
                                                {{ 'Type: '.$plan->product_plan_category->product_plan_category_name }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $plan->product_plan_category->product->product_name ?? '-' }} <br>
                                            
                                                <span class="{{ $plan->visibility == 1
                                                    ? 'text-green-600 bg-green-100'
                                                    : 'text-red-600 bg-red-100' }} px-2 py-[1px] rounded text-[10px] font-medium">
                                                    {{ $plan->visibility == 1 ? 'ON' : 'OFF' }}
                                                </span>
                                            </div>
                                        </td>

                                        <td>
                                            <code class="rounded bg-gray-100 px-2 py-1 text-[11px] font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                                {{ $plan->api_id ?? '-' }}
                                            </code>
                                        </td>

                                        <td class="min-w-[170px] max-w-[220px]">
                                            <div class="space-y-1">
                                                @forelse($plan->provider_mappings as $provider)
                                                    <div class="border-b border-gray-100 pb-1 last:border-0 last:pb-0 dark:border-gray-700">
                                                        <div class="flex items-center gap-1 text-[10px]">
                                                            <span class="min-w-0 flex-1 truncate font-semibold text-gray-800 dark:text-gray-100" title="{{ $provider['automation_name'] }}">{{ $provider['automation_name'] }}</span>
                                                            <span class="rounded px-1 py-px text-[8px] font-semibold {{ $provider['source'] === 'Default' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                                                {{ $provider['source'] === 'Default' ? 'D' : 'C' }}
                                                            </span>
                                                            <span class="h-1.5 w-1.5 rounded-full {{ $provider['is_active'] ? 'bg-green-500' : 'bg-red-500' }}" title="{{ $provider['is_active'] ? 'Active' : 'Inactive' }}"></span>
                                                        </div>
                                                        <div class="truncate font-mono text-[9px] text-gray-500" title="{{ $provider['provider_plan_id'] ?: 'No provider plan ID' }}">
                                                            {{ $provider['provider_plan_id'] ?: 'No provider plan ID' }}
                                                        </div>
                                                    </div>
                                                @empty
                                                    <span class="text-[10px] text-gray-400">No providers configured</span>
                                                @endforelse
                                            </div>
                                        </td>

                                        <td class="min-w-[125px] max-w-[155px]">
                                            @if($plan->best_provider_performance)
                                                <div class="truncate text-[10px] font-semibold text-gray-800 dark:text-gray-100" title="{{ $plan->best_provider_performance['automation_name'] }}">
                                                    {{ $plan->best_provider_performance['automation_name'] }}
                                                </div>
                                                <div class="whitespace-nowrap text-[11px] font-bold text-green-600">
                                                    {{ number_format($plan->best_provider_performance['success_rate'], 1) }}% · {{ $plan->best_provider_performance['successful_count'] }}/{{ $plan->best_provider_performance['total_count'] }}
                                                </div>
                                            @else
                                                <span class="text-[10px] text-gray-400">No tracked transactions</span>
                                            @endif
                                        </td>

                                        <td>
                                            {{ $plan->product_plan_category->network->network_name ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $plan->validity_in_days }} days
                                        </td>

                                        <td>
                                            ₦{{ number_format($plan->cost_price, 2) }}
                                        </td>
                        
                                        <td>
                                            ₦{{ number_format($plan->user_level_1_selling_price, 2) }}
                                        </td>

                                        <td>
                                            <a href="{{ route('admin.product_plans.manage', $plan->id) }}"
                                               data-manage-plan
                                               data-manage-url="{{ route('admin.product_plans.manage', ['id' => $plan->id, 'modal' => 1]) }}"
                                               class="ti-btn ti-btn-primary ti-btn-sm">
                                                Manage
                                            </a>

                                            <button
                                            type="button"
                                            class="ti-btn ti-btn-warning ti-btn-sm"
                                            data-hs-overlay="#duplicate-plan-modal-{{ $plan->id }}">
                                            Duplicate 
                                        </button>

                                                <div id="duplicate-plan-modal-{{ $plan->id }}" class="hs-overlay ti-modal hidden">
                                                    <div class="ti-modal-box">
                                                        <div class="ti-modal-content">
                                                
                                                            <div class="ti-modal-header">
                                                                <h3 class="ti-modal-title">
                                                                    Duplicate Plan
                                                                </h3>
                                                
                                                                <button
                                                                    type="button"
                                                                    class="hs-dropdown-toggle ti-modal-clode-btn"
                                                                    data-hs-overlay="#duplicate-plan-modal-{{ $plan->id }}">
                                                                    ✕
                                                                </button>
                                                            </div>
                                                
                                                            <div class="ti-modal-body">
                                                
                                                                <form
                                                                    method="POST"
                                                                    action="{{ route('admin.product_plans.duplicate', $plan->id) }}">
                                                
                                                                    @csrf
                                                
                                                                    <div class="mb-4">
                                                                        <label class="ti-form-label">
                                                                            Existing Plan
                                                                        </label>
                                                
                                                                        <input
                                                                            type="text"
                                                                            readonly
                                                                            value="{{ $plan->product_plan_name }}"
                                                                            class="ti-form-input">
                                                                    </div>
                                                
                                                                    <div class="mb-4">
                                                                        <label class="ti-form-label">
                                                                            New Plan Name
                                                                        </label>
                                                
                                                                        <input
                                                                            type="text"
                                                                            name="product_plan_name"
                                                                            required
                                                                            class="ti-form-input"
                                                                            placeholder="e.g MTN 10GB SME">
                                                                    </div>

                                                                    <div class="mb-4">
                                                                        <label class="ti-form-label">
                                                                            Product Plan Category
                                                                        </label>
                                                                    
                                                                        <select
                                                                            name="product_plan_category_id"
                                                                            required
                                                                            class="ti-form-select">
                                                                    
                                                                            <option value="">Select Category</option>
                                                                    
                                                                            @foreach($product_plan_categories as $category)
                                                                                <option
                                                                                    value="{{ $category->id }}"
                                                                                    {{ $plan->product_plan_category_id == $category->id ? 'selected' : '' }}>
                                                                                    {{ $category->product_plan_category_name }}
                                                                                </option>
                                                                            @endforeach
                                                                    
                                                                        </select>
                                                                    </div>
                                                
                                                                    <button
                                                                        type="submit"
                                                                        class="ti-btn ti-btn-primary w-full">
                                                                        Duplicate Plan
                                                                    </button>
                                                
                                                                </form>
                                                
                                                            </div>
                                                
                                                        </div>
                                                    </div>
                                                </div>
                                        </td>
                        
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-xs">
                                            No product plans found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        
                        </table>

                    </div>

                    {{-- PAGINATION --}}
                    <div class="mt-2 flex justify-between items-center text-xs">

                        <div class="text-gray-500">
                            {{ $data->firstItem() }} - {{ $data->lastItem() }} of {{ $data->total() }}
                        </div>

                        <div>
                            {{ $data->onEachSide(1)->links('pagination::tailwind') }}
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

{{-- MODAL (UNCHANGED) --}}
<div id="editModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-900 w-full max-w-6xl rounded-lg p-6 max-h-[90vh] overflow-y-auto">

        <h3 class="text-lg font-semibold mb-4">Edit Product Plan</h3>

        <hr class="mb-4">

        <h4 class="font-semibold text-sm mb-3">
            Automation Providers
        </h4>

        <div id="provider-list-container">
        </div>

        <div class="border rounded-lg p-4 mb-4">

            <h5 class="font-medium mb-3">
                Add Provider
            </h5>
       
            <form method="POST"
                action="{{ route('admin.automation-product-plans.store') }}">

                @csrf

                <input type="hidden"
                    name="product_plan_id"
                    id="provider_plan_id">

                <div class="grid gap-3">

                    {{-- PROVIDER --}}
                    <select name="automation_id"
                            class="ti-form-select"
                            required>
                        <option value="">Select Provider</option>
                        @foreach($automations as $automation)
                            <option value="{{ $automation->id }}">
                                {{ $automation->automation_name }}
                            </option>
                        @endforeach
                    </select>

                    {{-- 🔥 NEW: PROVIDER PLAN ID --}}
                    <input type="text"
                        name="provider_plan_id"
                        class="ti-form-input"
                        placeholder="Provider Plan ID"
                        required>

                    {{-- PRIORITY --}}
                    <input type="number"
                        name="priority"
                        class="ti-form-input"
                        placeholder="Priority"
                        required>

                    {{-- 🔥 NEW: STATUS --}}
                    <select name="status"
                            class="ti-form-select"
                            required>
                        <option value="1">Active (1)</option>
                        <option value="0">Inactive (0)</option>
                    </select>

                    {{-- COST --}}
                    <input type="number"
                        step="0.01"
                        name="cost_price"
                        class="ti-form-input"
                        placeholder="Cost Price">

                    {{-- SELLING --}}
                    <input type="number"
                        step="0.01"
                        name="selling_price"
                        class="ti-form-input"
                        placeholder="Selling Price">

                </div>

                <div class="mt-4 flex justify-end gap-2">

                    <button type="button"
                            onclick="closeProviderModal()"
                            class="ti-btn ti-btn-light ti-btn-sm">
                        Cancel
                    </button>

                    <button type="submit"
                            class="ti-btn ti-btn-primary ti-btn-sm">
                        Save
                    </button>

                </div>

            </form>
        
        </div>

        <form method="POST" id="editForm">
            @csrf
            @method('PUT')

            <input type="hidden" name="id" id="plan_id">

            <div class="grid gap-3">

                <input type="text" name="product_plan_name" id="plan_name" class="ti-form-input">
                <input type="number" name="data_size_in_mb" id="data_size" class="ti-form-input">
                <input type="number" name="validity_in_days" id="validity" class="ti-form-input">
                <input type="number" name="cost_price" id="cost_price" class="ti-form-input">
                <input type="number" name="user_level_1_selling_price" id="selling_price" class="ti-form-input">

            </div>

            <div class="mt-4 flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="ti-btn ti-btn-light ti-btn-sm">
                    Cancel
                </button>

                <button type="submit" class="ti-btn ti-btn-primary ti-btn-sm">
                    Update
                </button>
            </div>

        </form>

    </div>



</div>

<div id="manage-plan-modal"
     class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 p-3 sm:p-6"
     role="dialog"
     aria-modal="true"
     aria-hidden="true"
     aria-labelledby="manage-plan-modal-title">
    <div class="flex max-h-[94vh] w-full max-w-7xl flex-col overflow-hidden rounded-xl bg-white shadow-2xl dark:bg-bodybg">
        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-gray-700">
            <div>
                <h2 id="manage-plan-modal-title" class="text-base font-semibold text-gray-900 dark:text-gray-100">Manage product plan</h2>
                <p class="text-xs text-gray-500">Edit plan details, pricing, and provider routing.</p>
            </div>
            <button type="button"
                    data-close-manage-plan
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-gray-100"
                    aria-label="Close management modal">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M6 6l12 12M18 6L6 18" stroke-linecap="round" />
                </svg>
            </button>
        </div>

        <div id="manage-plan-modal-content" class="min-h-48 flex-1 overflow-y-auto p-3 sm:p-5">
            <div class="flex min-h-40 items-center justify-center text-sm text-gray-500">Select a plan to manage.</div>
        </div>

        <div class="flex items-center justify-between border-t border-gray-200 px-4 py-3 dark:border-gray-700">
            <a id="manage-plan-standalone-link" href="#" class="text-xs font-medium text-primary hover:underline">Open full management page</a>
            <button type="button" data-close-manage-plan class="ti-btn ti-btn-light ti-btn-sm">Close</button>
        </div>
    </div>
</div>






<script>

    const managePlanModal = document.getElementById('manage-plan-modal');
    const managePlanModalContent = document.getElementById('manage-plan-modal-content');
    const managePlanStandaloneLink = document.getElementById('manage-plan-standalone-link');

    function closeManagePlanModal() {
        managePlanModal.classList.add('hidden');
        managePlanModal.classList.remove('flex');
        managePlanModal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
    }

    async function openManagePlanModal(link) {
        managePlanModal.classList.remove('hidden');
        managePlanModal.classList.add('flex');
        managePlanModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
        managePlanStandaloneLink.href = link.href;
        managePlanModalContent.innerHTML = '<div class="flex min-h-40 items-center justify-center text-sm text-gray-500">Loading plan management…</div>';

        try {
            const response = await fetch(link.dataset.manageUrl, {
                credentials: 'same-origin',
                headers: { 'Accept': 'text/html' },
            });

            if (!response.ok) {
                throw new Error(`Management request failed with status ${response.status}`);
            }

            managePlanModalContent.innerHTML = await response.text();
            managePlanModalContent.querySelectorAll('script').forEach((oldScript) => {
                const script = document.createElement('script');
                script.textContent = oldScript.textContent;
                oldScript.replaceWith(script);
            });
        } catch (error) {
            managePlanModalContent.innerHTML = `
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <div class="font-semibold">Plan management could not be loaded.</div>
                    <div class="mt-1">Use the full management page to continue.</div>
                </div>`;
            console.error('Product plan management modal failed', error);
        }
    }

    document.addEventListener('click', (event) => {
        const manageLink = event.target.closest('[data-manage-plan]');
        if (manageLink) {
            event.preventDefault();
            openManagePlanModal(manageLink);
            return;
        }

        if (event.target.closest('[data-close-manage-plan]') || event.target === managePlanModal) {
            closeManagePlanModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && managePlanModal.getAttribute('aria-hidden') === 'false') {
            closeManagePlanModal();
        }
    });

    // =========================
    // EDIT MODAL (UNCHANGED FIXED)
    // =========================
    function openEditModal(plan) {

const modal = document.getElementById('editModal');

modal.classList.remove('hidden');
modal.classList.add('flex');

document.getElementById('plan_id').value = plan.id;
document.getElementById('provider_modal_plan_id').value = plan.id;

document.getElementById('plan_name').value = plan.product_plan_name;
document.getElementById('data_size').value = plan.data_size_in_mb;
document.getElementById('validity').value = plan.validity_in_days;
document.getElementById('cost_price').value = plan.cost_price;
document.getElementById('selling_price').value =
    plan.user_level_1_selling_price;

document.getElementById('editForm').action =
    `/admin/product-plans-new/${plan.id}`;

let html = '';

plan.providers.forEach(provider => {

    html += `
        <div class="border rounded p-2 mb-2 flex justify-between">

            <div>
                <div class="font-medium">
                    ${provider.automation?.automation_name ?? 'Unknown'}
                </div>

                <div class="text-xs text-gray-500">
                    Priority ${provider.priority}
                </div>
            </div>

            <div class="text-right text-xs">
                ₦${Number(provider.cost_price).toLocaleString()}
                →
                ₦${Number(provider.selling_price).toLocaleString()}
            </div>

        </div>
    `;
});

document.getElementById('provider-list-container')
    .innerHTML = html;
}
    function closeModal() {
        const modal = document.getElementById('editModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }


    // =========================
    // PROVIDER MODAL (FIXED)
    // =========================
    function openProviderModal(planId) {
        const modal = document.getElementById('providerModal');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        document.getElementById('provider_plan_id').value = planId;
    }

    function closeProviderModal() {
        const modal = document.getElementById('providerModal');

        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }


    // =========================
    // TOGGLE PROVIDERS LIST
    // =========================
    function toggleProviders(planId) {
        const el = document.getElementById(`providers-${planId}`);

        if (!el) return;

        el.classList.toggle('hidden');
    }

</script>

@endsection
