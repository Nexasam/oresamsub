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
           text-white dark:text-white 
           text-xs font-medium 
           transition-all duration-200"
  >
      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
      </svg>
      Back to Dashboard
    </a>
    </div>
  
    
  <h2 class="text-xl font-bold text-center mb-6">Buy Airtime</h2>

  @if(session('success'))
    <div class="mb-4 bg-green-100 text-green-800 text-sm px-4 py-2 rounded-lg">
      {{ session('success') }}
    </div>
  @elseif(session('error'))
    <div class="mb-4 bg-red-100 text-red-800 text-sm px-4 py-2 rounded-lg">
      {{ session('error') }}
    </div>
  @endif

  <form id="airtimeWrapper" @submit.prevent="isSubmitting = true" method="POST">
    @csrf

    <!-- Hidden Fields -->
    <input type="hidden" name="product_slug" id="product_slug" value="airtime">
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

    <!-- Amount -->
    <div class="mb-4">
      <label for="amount" class="block text-sm mb-1">Amount (₦)</label>
      <input
        type="number"
        name="amount"
        id="amount"
        required
        min="50"
        placeholder="e.g. 100"
        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
    </div>

    <!-- Product Plan -->
    <div class="mb-4">
      <label for="product_plan_id" class="block text-sm mb-1">Product Plan</label>
      <select
        name="product_plan_id"
        id="product_plan_id"
        required
        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        <option value="">Select</option>
        {{-- Dynamic plans will be loaded here --}}
      </select>
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
    id="buy_airtime_btn"
    class="w-full py-2 px-4 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition disabled:opacity-50"
    :disabled="isSubmitting"
    >
    <span x-show="!isSubmitting">🔌 Buy Airtime</span>
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
