@extends('layouts.app')

@section('content')

<div class="main-content">

    <div class="box p-4">

        {{-- ALERTS --}}
        @if (Session::has('success') || Session::has('failure'))
        <div class="mb-4">

            @if (Session::has('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 p-3 text-sm rounded">
                    {{ Session::get('success') }}
                </div>
            @endif

            @if (Session::has('failure'))
                <div class="bg-red-50 border border-red-200 text-red-700 p-3 text-sm rounded mt-2">
                    {{ Session::get('failure') }}
                </div>
            @endif

        </div>
        @endif

        <h3 class="text-lg font-semibold mb-4">
            Manage: {{ $plan->product_plan_name }}
        </h3>
        <a href="{{ route('admin.product_plans.index2') }}"
        class="inline-flex items-center gap-1 text-xs text-gray-600 hover:text-gray-900 mb-4">
            
            ← Back to Product Plans
        </a>

        {{-- BASIC INFO --}}
        <div class="mb-6 text-sm">
            <p><b>Product:</b> {{ $plan->product_plan_category->product->product_name ?? '-' }}</p>
            <p><b>Network:</b> {{ $plan->product_plan_category->network->network_name ?? '-' }}</p>
            <p><b>Validity:</b> {{ $plan->validity_in_days }} days</p>
        </div>


        {{-- EDIT PRODUCT PLAN --}}
        <h4 class="font-semibold text-sm mb-2">Edit Product Plan</h4>

        <form method="POST" action="{{ route('admin.product_plans.update_product_plan_new', $plan->id) }}">
            @csrf
            @method('PUT')


            <input type="hidden" name="user_level_1_selling_price" id="input_level_1" value="{{ $plan->user_level_1_selling_price }}">
            <input type="hidden" name="user_level_2_selling_price" id="input_level_2" value="{{ $plan->user_level_2_selling_price }}">
            <input type="hidden" name="user_level_3_selling_price" id="input_level_3" value="{{ $plan->user_level_3_selling_price }}">
            <input type="hidden" name="user_level_4_selling_price" id="input_level_4" value="{{ $plan->user_level_4_selling_price }}">
            <input type="hidden" name="user_level_5_selling_price" id="input_level_5" value="{{ $plan->user_level_5_selling_price }}">
            <input type="hidden" name="user_level_6_selling_price" id="input_level_6" value="{{ $plan->user_level_6_selling_price }}">
            <input type="hidden" name="user_level_7_selling_price" id="input_level_7" value="{{ $plan->user_level_7_selling_price }}">
        
            <div class="grid md:grid-cols-2 gap-2 text-xs">
        
                {{-- PLAN NAME --}}
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] text-gray-400">Plan Name</label>
                    <input type="text"
                           name="product_plan_name"
                           value="{{ $plan->product_plan_name }}"
                           class="ti-form-input h-8 text-xs">
                </div>
        
                {{-- DATA SIZE --}}
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] text-gray-400">Data Size (MB)</label>
                    <input type="number"
                           name="data_size_in_mb"
                           value="{{ $plan->data_size_in_mb }}"
                           class="ti-form-input h-8 text-xs">
                </div>

                                {{-- VISIBILITY --}}
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] text-gray-400">Visibility</label>

                    <select name="is_visible" class="ti-form-select h-10 text-xs">
                        <option value="1" {{ $plan->visibility ? 'selected' : '' }}>Visible</option>
                        <option value="0" {{ !$plan->visibility ? 'selected' : '' }}>Hidden</option>
                    </select>
                </div>

                {{-- PRODUCT PLAN CATEGORY --}}
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] text-gray-400">
                        Plan Category
                    </label>

                    <select
                        name="product_plan_category_id"
                        class="ti-form-select h-10 text-xs">

                        <option value="">Select Category</option>

                        @foreach($productPlanCategories as $category)
                            <option
                                value="{{ $category->id }}"
                                {{ $plan->product_plan_category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->product_plan_category_name }}
                            </option>
                        @endforeach

                    </select>
                </div>
        
                {{-- VALIDITY --}}
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] text-gray-400">Validity (Days)</label>
                    <input type="number"
                           name="validity_in_days"
                           value="{{ $plan->validity_in_days }}"
                           class="ti-form-input h-8 text-xs">
                </div>
        
                {{-- COST --}}
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] text-gray-400">Cost Price</label>
                    <input type="number"
                           step="0.01"
                           name="cost_price"
                           value="{{ $plan->cost_price }}"
                           class="ti-form-input h-8 text-xs">
                </div>
        
             


                <div class="flex flex-col gap-1 md:col-span-12">

                    <div class="divide-y border rounded-lg overflow-hidden mt-4">

                        @for($i = 1; $i <= 7; $i++)
                            @php
                                $label = $labels[$i] ?? 'LEVEL '.$i;
                                $price = $plan->{'user_level_'.$i.'_selling_price'} ?? 0;
                            @endphp
                        
                            <div class="flex justify-between items-center p-2 text-xs">
                                <div>
                                    <div class="font-medium">{{ $label }}</div>
                                    <div class="text-[10px] text-gray-400">Level {{ $i }}</div>
                                </div>
                        
                                <div class="font-bold text-green-600">
                                    ₦{{ number_format($price, 2) }}
                                </div>
                            </div>
                        @endfor
                        
                        </div>
                </div>


                   {{-- SELLING --}}
               {{-- SELLING PRICE TIER --}}
               <div class="flex flex-col gap-1 md:col-span-2">

                    <label class="text-[10px] text-gray-400">
                        Selling Price Base (Level 4: Popular Level)
                        {{-- {{$newplan}} --}}
                        {{-- {{ json_decode($newplan,true)[2][1] }} --}}
                        {{-- {{ json_decode($newplan,true)[1][1] }} --}}
                    </label>

                    <input type="number"
                        id="base_price"
                        {{-- name="user_level_4_selling_price" --}}
                        name="base_price"
                        value="{{ $plan->user_level_4_selling_price ?? $plan->user_level_1_selling_price }}"
                        class="ti-form-input h-8 text-xs">
                </div>

                
                {{-- DIFFERENCE --}}
                <div class="flex flex-col gap-1 md:col-span-2">

                    <label class="text-[10px] text-gray-400">
                        Difference Step
                    </label>

                    <input type="number"
                        id="diff_step"
                        value="10"
                        class="ti-form-input h-8 text-xs">

                </div>
        
            </div>
        
            <button class="ti-btn ti-btn-primary ti-btn-sm mt-3">
                Update Plan
            </button>
        </form>



        <div class="mt-3 grid grid-cols-7 mt-3 gap-1 text-[13px]">
            <div>PRICE PREVIEW</div>
            @for($i = 1; $i <= 7; $i++)
                <div class="border rounded p-1 text-center">
                    <div class="text-gray-400">L{{ $i }}</div>
                    <div id="level_{{ $i }}">
                        ₦{{ number_format($plan->{'user_level_'.$i.'_selling_price'} ?? 0, 2) }}
                    </div>
                </div>
            @endfor
        
        </div>
     




        @php
            $providerCosts = $plan->automationProductPlans->pluck('cost_price')->filter();

            $minCost = $providerCosts->min();
            $maxCost = $providerCosts->max();

            $safeMin = $minCost ? $minCost + 6 : 0; // enforce +₦6 rule
        @endphp

        <div class="mb-2 grid grid-cols-3 gap-2 mt-3 text-[11px]">

            <div class="border rounded p-2">
                <div class="text-gray-400">Lowest Cost</div>
                <div class="font-semibold">₦{{ number_format($minCost, 2) }}</div>
            </div>

            <div class="border rounded p-2">
                <div class="text-gray-400">Highest Cost</div>
                <div class="font-semibold">₦{{ number_format($maxCost, 2) }}</div>
            </div>

            <div class="border rounded p-2 bg-green-50 dark:bg-green-900/10">
                <div class="text-gray-400">Min Allowed Selling (Cost + 6)</div>
                <div class="font-semibold text-green-600">
                    ₦{{ number_format($safeMin, 2) }}
                </div>
            </div>

        </div>

        </form>



        <form method="POST"
      action="{{ route('admin.product_plans.update_selling_prices', $plan->id) }}"
      class="mt-4">

    @csrf
    @method('PUT')

    <div class="border rounded-lg p-4">

        <h5 class="font-semibold mb-3">
            Update Selling Prices
        </h5>

        <div class="grid md:grid-cols-2 gap-3">

            @for($i = 1; $i <= 7; $i++)
                @php
                    $label = $labels[$i] ?? 'LEVEL '.$i;
                @endphp

                <div>
                    <label class="block text-xs text-gray-500 mb-1">
                        {{ $label }}
                    </label>

                    <input type="number"
                           step="0.01"
                           name="user_level_{{ $i }}_selling_price"
                           value="{{ $plan->{'user_level_'.$i.'_selling_price'} }}"
                           class="ti-form-input">
                </div>
            @endfor

        </div>

        <button type="submit"
                class="ti-btn ti-btn-success mt-4">
            Update Selling Prices
        </button>

    </div>

        </form>





        <hr class="my-6">



            {{-- PROVIDERS HEADER --}}
        <div class="flex justify-between items-center mb-2">
            <h4 class="font-semibold text-sm">
                Providers ({{ $plan->automationProductPlans->count() }})
            </h4>
        </div>

        {{-- PROVIDERS LIST --}}
        <div class="space-y-1 mb-6">

        @forelse($plan->automationProductPlans as $provider)

        <form method="POST"
        action="{{ route('admin.automation-product-plans.update', $provider->id) }}"
        class="grid grid-cols-12 gap-1 items-end text-xs bg-gray-50 dark:bg-gray-800 p-1 rounded">
  
      @csrf
      @method('PUT')

  
      {{-- NAME --}}
      <div class="col-span-3 px-1 leading-tight">
          <div class="font-medium text-xs">
              {{ $provider->automation->automation_name }}
          </div>
          <div class="text-[10px] text-gray-400">
              {{ $provider->provider_plan_id }} · {{ $provider->is_active ? 'ON' : 'OFF' }}
          </div>
      </div>
  
      {{-- PRIORITY --}}
      <div class="col-span-1 flex flex-col">
          <span class="text-[9px] text-gray-400">Priority</span>
          <input type="number"
                 name="priority"
                 value="{{ $provider->priority }}"
                 class="ti-form-input text-xs h-7 px-1">
      </div>
  
      {{-- COST --}}
      <div class="col-span-2 flex flex-col">
          <span class="text-[9px] text-gray-400">Cost</span>
          <input type="number"
                 step="0.01"
                 name="cost_price"
                 value="{{ $provider->cost_price }}"
                 class="ti-form-input text-xs h-7 px-1">
      </div>
  
      {{-- PLAN ID --}}
      <div class="col-span-2 flex flex-col">
          <span class="text-[9px] text-gray-400">Plan ID</span>
          <input type="text"
                 name="provider_plan_id"
                 value="{{ $provider->provider_plan_id }}"
                 class="ti-form-input text-xs h-7 px-1">
      </div>
  
      {{-- STATUS --}}
      <div class="col-span-2 flex flex-col">
          <span class="text-[9px] text-gray-400">Status</span>
          <select name="is_active"
                  class="ti-form-select text-xs h-10 px-1">
                  <option value="1" {{ (int)$provider->is_active === 1 ? 'selected' : '' }}>Active</option>
                  <option value="0" {{ (int)$provider->is_active === 0 ? 'selected' : '' }}>Off</option>
          </select>
      </div>
  
      {{-- SAVE --}}
      <div class="col-span-2 flex flex-col justify-end">
          <span class="text-[9px] text-transparent">Save</span>
          <button class="ti-btn ti-btn-primary ti-btn-sm text-[11px] h-7 px-2">
              Save
          </button>
      </div>
  
  </form>

        @empty

            <p class="text-xs text-gray-500">No providers yet</p>

        @endforelse

        </div>

        {{-- ADD PROVIDER --}}
        <h4 class="font-semibold mb-2">Add Provider</h4>

        <form method="POST" action="{{ route('admin.automation-product-plans.store') }}">
            @csrf

            <input type="hidden" name="product_plan_id" value="{{ $plan->id }}">

            <div class="grid gap-3">

                {{-- PROVIDER --}}
                <select name="automation_id" class="ti-form-select" required>
                    <option value="">Select Provider</option>
                    @foreach($automations as $auto)
                        <option value="{{ $auto->id }}">
                            {{ $auto->automation_name }}
                        </option>
                    @endforeach
                </select>

                {{-- PROVIDER PLAN ID --}}
                <input type="text"
                    name="provider_plan_id"
                    class="ti-form-input"
                    placeholder="Provider Plan ID"
                    required>

                {{-- PRIORITY --}}
                <input type="number"
                    name="priority"
                    class="ti-form-input"
                    placeholder="Priority">

                {{-- IS ACTIVE --}}
                <select name="is_active" class="ti-form-select">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>

                {{-- COST PRICE --}}
                <input type="number"
                    step="0.01"
                    name="cost_price"
                    class="ti-form-input"
                    placeholder="Cost Price">

            </div>

            <button class="ti-btn ti-btn-primary ti-btn-sm mt-4">
                Add Provider
            </button>

        </form>

    </div>

</div>

<script>
    let isFirstLoad = true;

    function calculateLevels() {

        let base = parseFloat(document.getElementById('base_price').value || 0);
        let diff = parseFloat(document.getElementById('diff_step').value || 0);

        let minAllowed = {{ $safeMin ?? 0 }};

        let levels = {
            1: base + (3 * diff),
            2: base + (2 * diff),
            3: base + diff,
            4: base,
            5: base - diff,
            6: base - (2 * diff),
            7: base - (3 * diff),
        };

        for (let i = 1; i <= 7; i++) {

            let value = levels[i];

            if (value < minAllowed) {
                value = minAllowed;
            }

            document.getElementById('level_' + i).innerText = '₦' + value.toFixed(2);
            document.getElementById('input_level_' + i).value = value.toFixed(2);
        }
    }

    // ONLY recalc when user edits
    document.getElementById('base_price').addEventListener('input', calculateLevels);
    document.getElementById('diff_step').addEventListener('input', calculateLevels);
    calculateLevels();
    </script>
@endsection