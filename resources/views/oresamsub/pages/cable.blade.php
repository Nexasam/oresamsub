@extends('oresamsub.layouts.app')

@section('content')
<div class="pt-6 max-w-sm mx-auto" x-data="{ isSubmitting: false }">
  <h2 class="text-xl font-bold text-center mb-6">Buy Cable Subscription</h2>

  @if(session('success'))
    <div class="mb-4 bg-green-100 text-green-800 text-sm px-4 py-2 rounded-lg">
      {{ session('success') }}
    </div>
  @elseif(session('error'))
    <div class="mb-4 bg-red-100 text-red-800 text-sm px-4 py-2 rounded-lg">
      {{ session('error') }}
    </div>
  @endif

  {{-- {{ route('ore.cable.submit') }} --}}
  <form id="cableWrapper" method="POST" @submit.prevent="isSubmitting = true" action="">
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
    <div class="mb-4">
      <label class="block text-sm mb-1">Plans display here</label>
      <div id="plan_grid" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        {{-- AJAX-loaded plans here --}}
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
      class="w-full py-2 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
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
