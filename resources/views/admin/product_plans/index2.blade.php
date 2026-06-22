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

                            <select name="per_page" class="ti-form-select py-1 text-xs">
                                <option value="50" {{ request('per_page', 100) == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page', 100) == 100 ? 'selected' : '' }}>100</option>
                                <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                                <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500</option>
                            </select>

                            <div class="flex gap-2 items-center">
                                <button class="ti-btn ti-btn-primary ti-btn-sm">Filter</button>
                                <a href="{{ route('admin.product_plans.index') }}"
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
                                                {{ count($plan->automationProductPlans) .' providers'}} <br>
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






<script>

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
