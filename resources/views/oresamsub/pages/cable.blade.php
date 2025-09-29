@extends('oresamsub.layouts.app')

@section('content')
<div class="pt-6 max-w-full mx-auto" x-data="{ isSubmitting: false }">


<div class="mb-4">
    <a 
    href="{{ route('inertia.dashboard.index') }}"
    @click.prevent="showLoader = true; setTimeout(() => window.location.href = '{{ route('inertia.dashboard.index') }}', 1000)"
    class="inline-flex items-center px-4 py-2 mb-2 mt-4 rounded-lg bg-gradient-to-r from-emerald-600 to-emerald-500 text-white text-sm font-medium shadow hover:from-emerald-700 hover:to-emerald-600 transition"
    
    >
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    Back to Dashboard
    </a>
</div>
  
    
  <h2 class="text-xl font-bold text-center mb-1">Buy Cable Subscription</h2>

  @if(session('success'))
    <div class="mb-4 bg-green-100 text-green-800 text-sm px-4 py-2 rounded-lg">
      {{ session('success') }}
    </div>
  @elseif(session('error'))
    <div class="mb-4 bg-red-100 text-red-800 text-sm px-4 py-2 rounded-lg">
      {{ session('error') }}
    </div>
  @endif

  {{-- CANDIDATE FOR DRY --}}
  <!-- Wallet Balance Display -->
  <div class="mb-1 text-center" x-data="{ showBalance: true }">
    {{-- <p class="text-sm text-gray-500 dark:text-gray-400">Your Wallet Balance</p> --}}
    <div class="flex items-center justify-center space-x-2 mt-0 text-xl font-bold">
        <!-- Balance -->
        <span x-show="showBalance" x-cloak class="text-green-600 dark:text-green-400">
            ₦{{ number_format(auth()->user()->main_wallet, 2) }}
        </span>
        <span x-show="!showBalance" x-cloak class="tracking-widest text-gray-400">
            •••••
        </span>

        <!-- Toggle Button -->
        <button @click="showBalance = !showBalance" class="ml-2 hover:text-gray-600 dark:hover:text-gray-200 transition" title="Toggle Balance">
            <!-- Eye icon -->
            <svg x-show="!showBalance" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            <!-- Eye-off icon -->
            <svg x-show="showBalance" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.269-2.943-9.543-7a10.06 10.06 0 013.232-4.568M6.223 6.223A10.05 10.05 0 0112 5c4.478 0 8.269 2.943 9.543 7a10.06 10.06 0 01-4.676 5.316M15 12a3 3 0 00-3-3M3 3l18 18"/>
            </svg>
        </button>
    </div>
  </div>

  {{-- {{ route('ore.cable.submit') }} --}}
  <form class="pt-2" id="cableWrapper" method="POST" @submit.prevent="isSubmitting = true" action="">
    @csrf

    <input type="hidden" name="product_slug" id="product_slug" value="cable_subscription">
    <input type="hidden" name="wallet_category"  id="wallet_category" value="main_wallet">
    <input type="hidden" name="cable_product_plan_id" id="cable_product_plan_id">

    <!-- Product Category -->
    <div class="mb-4">
      <label for="cable_product_plan_category_id" class="block text-sm mb-1">Cable Provider</label>
      <select
        name="cable_product_plan_category_id"
        id="cable_product_plan_category_id"
        required
        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        <option value="">Select</option>
        @foreach($product_plan_categories as $category)
          <option value="{{ $category->id }}">{{ $category->product_plan_category_name }}</option>
        @endforeach
      </select>
    </div>

    <!-- Plan Grid -->
    {{-- <div class="mb-4">
      <label class="block text-sm mb-1">Plans display here</label>
      <div id="plan_grid" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
      </div>
      <div id="plan_error" class="text-red-500 text-sm mt-2 hidden">Please select a cable plan</div>
    </div> --}}

    <div class="mb-4">
      <label class="block text-sm mb-1">Plans display here</label>
    
      <!-- Scrollable container -->
      <div class="relative max-h-64 overflow-y-auto border rounded-lg p-2 pr-3 scrollbar-thin scrollbar-thumb-emerald-500 scrollbar-track-transparent">
    
        <!-- Grid layout: 3 columns, small text -->
        <div id="plan_grid" class="grid grid-cols-3 gap-2 text-xs">
          {{-- AJAX-loaded plans here --}}
        </div>
    
        <!-- Scroll hint -->
        <div class="absolute bottom-1 right-2 text-[10px] text-gray-400 dark:text-gray-500 italic pointer-events-none">
          Scroll for more ↓
        </div>
      </div>
    
      <div id="plan_error" class="text-red-500 text-sm mt-2 hidden">Please select a cable plan</div>
    </div>
    

    

    <!-- Smartcard Number -->
    <div class="mb-4">
      <label for="smart_card_number" class="block text-sm mb-1">Smartcard Number</label>
      <input
        {{-- onkeyup="validateNameOnSmartCard('cable')" --}}
        type="text"
        name="smart_card_number"
        id="smart_card_number"
        required
        placeholder="Enter smartcard number"
        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
    </div>

    <!-- Name Preview -->
    <div class="mb-4">
      <label class="block text-sm mb-1">Name on Card</label>
      <div id="smartcard_name_preview" class="w-full px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 border text-gray-600 dark:text-gray-300">
        Not yet verified
      </div>
    </div>

    <!-- PIN -->
    <input type="hidden" id="no_of_slots" name="no_of_slots" value="1">
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
      id="buy_cable_btn"
      class="w-full py-2 px-4 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition disabled:opacity-50"
      :disabled="isSubmitting"
    >
    <span x-show="!isSubmitting">📺 Subscribe</span>
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
