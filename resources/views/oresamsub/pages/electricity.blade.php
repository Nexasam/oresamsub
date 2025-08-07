@extends('oresamsub.layouts.app')

@section('content')
<div class="pt-6 max-w-sm mx-auto" x-data="{ isSubmitting: false }">

    <!-- Back Button -->
    <div class="mb-4">
       <a 
    href="{{ route('dashboard') }}"
    @click.prevent="showLoader = true; setTimeout(() => window.location.href = '{{ route('dashboard') }}', 1000)"
    class="inline-flex items-center px-3 py-1.5 rounded-md 
           bg-emerald-600 hover:bg-emerald-700 
           text-white 
           text-xs font-semibold 
           transition-all duration-200 shadow"
  >
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    Back to Dashboard
  </a>
    </div>
    

  <h2 class="text-xl font-bold text-center mb-6">Buy Electricity Token</h2>

  @if(session('success'))
    <div class="mb-4 bg-green-100 text-green-800 text-sm px-4 py-2 rounded-lg">
      {{ session('success') }}
    </div>
  @elseif(session('error'))
    <div class="mb-4 bg-red-100 text-red-800 text-sm px-4 py-2 rounded-lg">
      {{ session('error') }}
    </div>
  @endif

  <form id="electricityWrapper" method="POST" @submit.prevent="isSubmitting = true" action="">
    @csrf

    <input type="hidden" name="product_slug" id="product_slug" value="utility_bills">
    <input type="hidden" name="wallet_category" id="wallet_category" value="main_wallet">
    <input type="hidden" name="electricity_product_plan_id" id="electricity_product_plan_id">

    <!-- Product Category -->
    <div class="mb-4">
      <label for="electricity_product_plan_category_id" class="block text-sm mb-1">Electricity Provider</label>
      <select
        name="electricity_product_plan_category_id"
        id="electricity_product_plan_category_id"
        required
        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        <option value="">Select</option>
        @foreach($product_plan_categories as $category)
          <option value="{{ $category->id }}">{{ $category->product_plan_category_name }}</option>
        @endforeach
      </select>
    </div>

    <!-- Amount -->
    <div class="mb-4">
      <label for="utility_amount" class="block text-sm mb-1">Amount</label>
      <input
        type="number"
        min="50"
        name="utility_amount"
        id="utility_amount"
        required
        placeholder="Enter amount e.g. 1000"
        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
    </div>

    <!-- Plan Grid (Optional: skip if not used for electricity) -->
    {{-- <div class="mb-4">
      <label class="block text-sm mb-1">Plans display here</label>
      <div id="plan_grid" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
      </div>
      <div id="plan_error" class="text-red-500 text-sm mt-2 hidden">Please select an electricity plan</div>
    </div> --}}

    <div class="mb-4">
      <label class="block text-sm mb-1">Plans display here</label>
    
      <!-- Scrollable container -->
      <div class="relative max-h-64 overflow-y-auto border rounded-lg p-2 pr-3 scrollbar-thin scrollbar-thumb-emerald-500 scrollbar-track-transparent">
    
        <!-- Grid layout: 3 columns on small screens, smaller font -->
        <div id="plan_grid" class="grid grid-cols-3 gap-2 text-xs">
          {{-- AJAX-loaded plans here --}}
        </div>
    
        <!-- Scroll hint -->
        <div class="absolute bottom-1 right-2 text-[10px] text-gray-400 dark:text-gray-500 italic pointer-events-none">
          Scroll for more ↓
        </div>
      </div>
    
      <div id="plan_error" class="text-red-500 text-sm mt-2 hidden">Please select an electricity plan</div>
    </div>
    

    <!-- Meter Number -->
    <div class="mb-4">
      <label for="metre_number" class="block text-sm mb-1">Meter Number</label>
      <input
        type="text"
        name="metre_number"
        id="metre_number"
        required
        placeholder="Enter meter number"
        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
    </div>

    <!-- Name on Meter -->
    <div class="mb-4">
      <label class="block text-sm mb-1">Customer Name</label>
      <div id="meter_name_preview" class="w-full px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 border text-gray-600 dark:text-gray-300">
        Not yet verified
      </div>
    </div>

    <!-- Address Preview -->
    <div class="mb-4">
      <label class="block text-sm mb-1">Customer Address</label>
      <div id="meter_address_preview" class="w-full px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 border text-gray-600 dark:text-gray-300">
        Not yet verified
      </div>
    </div>

    <!-- PIN -->
    <input type="hidden" name="no_of_slots" value="1">
    <div class="mb-6">
      <label for="pin" class="block text-sm mb-1">Transaction PIN</label>
      <input
        type="password"
        name="pin"
        id="pin"
        required
        maxlength="4"
        minlength="4"
        inputmode="numeric"
        pattern="[0-9]*"
        placeholder="Enter 4-digit PIN"
        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
    </div>

    <!-- Submit -->
    <button
    type="submit"
    id="buy_electricity_btn"
    class="w-full py-2 px-4 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition disabled:opacity-50"
    :disabled="isSubmitting"
    >
    <span x-show="!isSubmitting">⚡ Buy Token</span>
    <span x-show="isSubmitting" x-cloak class="flex items-center justify-center gap-2">
      <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8z"/>
      </svg>
      Processing...
    </span>
   </button>
  
  </form>
</div>
@endsection
