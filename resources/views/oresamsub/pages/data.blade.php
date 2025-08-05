@extends('oresamsub.layouts.app')

@section('content')
<div class="pt-6 max-w-sm mx-auto" x-data="{ isSubmitting: false }">

  <!-- Back Button -->
  <div class="mb-4">
    <a 
    href="{{ route('ore.dashboard') }}"
    @click.prevent="showLoader = true; setTimeout(() => window.location.href = '{{ route('ore.dashboard') }}', 1000)"
       class="inline-flex items-center px-3 py-1.5 rounded-md bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 text-xs font-medium hover:bg-green-200 dark:hover:bg-green-700 transition-all duration-200">
      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
      </svg>
      Back to Dashboard
    </a>
  </div>
  
  

  <h2 class="text-xl font-bold text-center mb-6">Buy Data</h2>

  @if(session('success'))
    <div class="mb-4 bg-green-100 text-green-800 text-sm px-4 py-2 rounded-lg">
      {{ session('success') }}
    </div>
  @elseif(session('error'))
    <div class="mb-4 bg-red-100 text-red-800 text-sm px-4 py-2 rounded-lg">
      {{ session('error') }}
    </div>
  @endif

  <form id="dataWrapper" method="POST" @submit.prevent="isSubmitting = true" action="{{ route('ore.data.submit') }}">
    @csrf

    <!-- Hidden Fields -->
    <input type="hidden" name="product_slug" id="product_slug" value="data">
    <input type="hidden" name="wallet_category" id="wallet_category" value="main_wallet">

    <!-- Network -->
    <div class="mb-4">
      <label for="network_id" class="block text-sm mb-1">Network</label>
      <select
        name="network_id"
        id="network_id"
        required
        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        <option value="">Select</option>
        @foreach ($networks as $network)
          <option value="{{ $network->id }}">{{ $network->network_name }}</option>
        @endforeach
      </select>
    </div>

    <!-- Phone Number -->
    <div class="mb-4">
      <label for="phone_number" class="block text-sm mb-1">Phone Number</label>
      <input
        type="tel"
        name="phone_number"
        id="phone_number"
        required
        placeholder="e.g. 08012345678"
        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
    </div>

   <!-- Hidden field for selected product_plan_id -->
    <input type="hidden" name="product_plan_id" id="product_plan_id">

    <!-- Data Plan Grid -->
    {{-- <div class="mb-4">
      <label class="block text-sm mb-1">Data Plan</label>
      <div id="plan_grid" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
      </div>
      <div id="plan_error" class="text-red-500 text-sm mt-2 hidden">Please select a data plan</div>
    </div> --}}
    <div class="mb-4">
      <label class="block text-sm mb-1">Data Plan</label>
    
      <!-- Scrollable container -->
      <div class="relative max-h-64 overflow-y-auto border rounded-lg p-2 pr-3 scrollbar-thin scrollbar-thumb-emerald-500 scrollbar-track-transparent">
    
        <!-- Grid with 3 columns -->
        <div id="plan_grid" class="grid grid-cols-3 gap-2 text-xs">
          {{-- Plans will be appended here dynamically --}}
        </div>
    
        <!-- Scroll hint -->
        <div class="absolute bottom-1 right-2 text-[10px] text-gray-400 dark:text-gray-500 italic pointer-events-none">
          Scroll for more ↓
        </div>
      </div>
    
      <div id="plan_error" class="text-red-500 text-sm mt-2 hidden">Please select a data plan</div>
    </div>
    
    
    
    

    

    <!-- Transaction PIN -->
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

    <!-- Submit Button -->
    <button
    type="submit"
    id="buy_data_btn"
    class="w-full py-2 px-4 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition disabled:opacity-50"
    :disabled="isSubmitting"
  >
    <span x-show="!isSubmitting">📶 Buy Data</span>
    <span x-show="isSubmitting" x-cloak class="flex items-center justify-center gap-2">
      <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10"
                stroke="currentColor" stroke-width="4" fill="none"/>
        <path class="opacity-75" fill="currentColor"
              d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8z"/>
      </svg>
      Processing...
    </span>
  </button>
  
  </form>
</div>
@endsection
