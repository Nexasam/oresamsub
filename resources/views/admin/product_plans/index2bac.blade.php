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
                                <tr class="text-xs">
                                    <th>#</th>
                                    <th>Plan</th>
                                    <th>Val|CP|SP</th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($data as $plan)
                                    <tr class="!py-1 align-top">

                                        <td class="py-1">
                                            {{ $data->firstItem() + $loop->index }}
                                        </td>

                                        {{-- PLAN (COMPACT) --}}
                                        <td class="py-1 leading-tight">

                                            <div class="font-semibold text-sm">
                                                {{ $plan->product_plan_name }}
                                            </div>
                                        
                                            <div class="text-gray-500 text-xs">
                                                {{ $plan->product_plan_category->network->network_name ?? '-' }}
                                            </div>
                                        
                                            <div class="text-gray-400 text-xs">
                                                {{ $plan->visibility ? 'Active' : 'Off' }}
                                            </div>
                                        
                                            {{-- 🔥 NEW: AUTOMATION STACK --}}
                                            <div class="mt-1">

                                                {{-- Toggle --}}
                                                <button type="button"
                                                        onclick="toggleProviders({{ $plan->id }})"
                                                        class="text-[11px] text-blue-600 underline">
                                                    + Providers ({{ $plan->automationProductPlans->count() }})
                                                </button>
                                            
                                                {{-- Provider List --}}
                                                <div id="providers-{{ $plan->id }}" class="hidden mt-2 space-y-1">
                                            
                                                    @foreach($plan->automationProductPlans->sortBy('priority') as $autoPlan)
                                            
                                                        <div class="text-[11px] flex justify-between items-center">
                                            
                                                            <span>
                                                                {{ $autoPlan->automation->automation_name }}
                                                                <span class="text-gray-400">(P{{ $autoPlan->priority }})</span>
                                                            </span>
                                            
                                                            <span class="text-gray-500">
                                                                ₦{{ number_format($autoPlan->cost_price, 2) }}
                                                                →
                                                                ₦{{ number_format($autoPlan->selling_price, 2) }}
                                                            </span>
                                            
                                                        </div>
                                            
                                                    @endforeach
                                            
                                                    {{-- Add Button --}}
                                                    <button type="button"
                                                            onclick="openProviderModal({{ $plan->id }})"
                                                            class="text-[11px] text-green-600 mt-1">
                                                        + Add Provider
                                                    </button>
                                            
                                                </div>
                                            
                                            </div>
                                        
                                        </td>

                                        {{-- PRICES --}}
                                        <td class="py-1 text-xs">
                                            {{ $plan->validity_in_days }}d |
                                            ₦{{ number_format($plan->cost_price, 2) }} |
                                            ₦{{ number_format($plan->user_level_1_selling_price, 2) }}
                                        </td>

                                        {{-- ACTION --}}
                                        <td class="py-1">
                                            @php
                                            $modalData = [
                                                'id' => $plan->id,
                                                'product_plan_name' => $plan->product_plan_name,
                                                'data_size_in_mb' => $plan->data_size_in_mb,
                                                'validity_in_days' => $plan->validity_in_days,
                                                'cost_price' => $plan->cost_price,
                                                'user_level_1_selling_price' => $plan->user_level_1_selling_price,
                                                'providers' => $plan->automationProductPlans
                                                    ->load('automation')
                                                    ->values(),
                                            ];
                                        @endphp
                                        
                                     
                                            <button
                                                type="button"
                                                class="ti-btn ti-btn-primary ti-btn-sm text-xs"
                                                onclick='openEditModal(@json($modalData))'
                                            >
                                                Manage
                                            </button>
                                        </td>
                                        

                                    </tr>
                                    <tr
                                            x-show="activePlan == {{ $plan->id }}"
                                            x-transition
                                            style="display:none;"
                                        >
                                            <td colspan="4">

                                                <div class="bg-gray-50 dark:bg-gray-800 border rounded-lg p-4">

                                                    {{-- Providers --}}
                                                    <div class="mb-6">

                                                        <div class="flex justify-between items-center mb-3">
                                                            <h6 class="font-semibold">
                                                                Automation Providers
                                                            </h6>
                                                        </div>

                                                        <div class="space-y-2">

                                                            @foreach($plan->automationProductPlans->sortBy('priority') as $autoPlan)

                                                                <div
                                                                    class="border rounded p-2 flex justify-between items-center"
                                                                >

                                                                    <div>
                                                                        <div class="font-medium">
                                                                            {{ $autoPlan->automation->automation_name }}
                                                                        </div>

                                                                        <div class="text-xs text-gray-500">
                                                                            Priority {{ $autoPlan->priority }}
                                                                        </div>
                                                                    </div>

                                                                    <div class="text-right text-xs">
                                                                        ₦{{ number_format($autoPlan->cost_price,2) }}
                                                                        →
                                                                        ₦{{ number_format($autoPlan->selling_price,2) }}
                                                                    </div>

                                                                </div>

                                                            @endforeach

                                                        </div>

                                                    </div>

                                             

                                                    {{-- Update Plan --}}
                                                    <div>

                                                        <h6 class="font-semibold mb-3">
                                                            Update Product Plan
                                                        </h6>

                                                        <form
                                                            method="POST"
                                                            action="/admin/product-plans-new/{{ $plan->id }}"
                                                        >
                                                            @csrf
                                                            @method('PUT')

                                                            <div class="grid grid-cols-5 gap-2">

                                                                <input
                                                                    type="text"
                                                                    name="product_plan_name"
                                                                    value="{{ $plan->product_plan_name }}"
                                                                    class="ti-form-input"
                                                                >

                                                                <input
                                                                    type="number"
                                                                    name="data_size_in_mb"
                                                                    value="{{ $plan->data_size_in_mb }}"
                                                                    class="ti-form-input"
                                                                >

                                                                <input
                                                                    type="number"
                                                                    name="validity_in_days"
                                                                    value="{{ $plan->validity_in_days }}"
                                                                    class="ti-form-input"
                                                                >

                                                                <input
                                                                    type="number"
                                                                    step="0.01"
                                                                    name="cost_price"
                                                                    value="{{ $plan->cost_price }}"
                                                                    class="ti-form-input"
                                                                >

                                                                <input
                                                                    type="number"
                                                                    step="0.01"
                                                                    name="user_level_1_selling_price"
                                                                    value="{{ $plan->user_level_1_selling_price }}"
                                                                    class="ti-form-input"
                                                                >

                                                            </div>

                                                            <div class="mt-3">
                                                                <button
                                                                    class="ti-btn ti-btn-primary ti-btn-sm"
                                                                >
                                                                    Update Plan
                                                                </button>
                                                            </div>

                                                        </form>

                                                    </div>

                                                </div>

                                            </td>
                                        </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-xs">
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
