@extends('oresamsub.layouts.app')

@section('content')
<div class="max-w-md mx-auto pt-6" x-data="{ isSubmitting: false, showBalance: true }">

    <!-- Back Button -->
    <a 
        href="{{ route('dashboard') }}"
        @click.prevent="showLoader = true; setTimeout(() => window.location.href = '{{ route('dashboard') }}', 1000)"
        class="inline-flex items-center px-4 py-2 mb-2 mt-2 rounded-lg bg-gradient-to-r from-emerald-600 to-emerald-500 text-white text-sm font-medium shadow hover:from-emerald-700 hover:to-emerald-600 transition"
    >
        ← Back to Dashboard
    </a>

    <!-- Wallet Balance -->
    <div class="mb-4 text-center">
        <div class="flex items-center justify-center space-x-2 text-xl font-bold">
            <span x-show="showBalance" x-cloak class="text-emerald-600 dark:text-emerald-400">
                ₦{{ number_format(auth()->user()->main_wallet, 2) }}
            </span>
            <span x-show="!showBalance" x-cloak class="tracking-widest text-gray-400">•••••</span>
            <button @click="showBalance = !showBalance" class="ml-2 hover:text-gray-600 dark:hover:text-gray-200 transition">
                <svg x-show="!showBalance" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 
                             7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg x-show="showBalance" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 
                             0-8.269-2.943-9.543-7a10.06 10.06 
                             0 013.232-4.568M6.223 6.223A10.05 
                             10.05 0 0112 5c4.478 0 8.269 2.943 
                             9.543 7a10.06 10.06 0 01-4.676 
                             5.316M15 12a3 3 0 00-3-3M3 
                             3l18 18"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Card -->
    <div class="bg-white dark:bg-gray-800 text-gray-700 dark:text-white rounded-xl shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 font-semibold">
            Buy Cable Subscription
        </div>

        {{-- {{ route('ore.cable.submit') }} --}}
        <form id="cableWrapper" method="POST" @submit.prevent="isSubmitting = true" action="" class="p-4 space-y-4">
            @csrf
            <input type="hidden" name="product_slug" value="cable_subscription">
            <input type="hidden" name="wallet_category" value="main_wallet">
            <input type="hidden" name="cable_product_plan_id" id="cable_product_plan_id">
            <input type="hidden" name="no_of_slots" value="1">

            <!-- Cable Provider -->
            <div>
                <label for="cable_product_plan_category_id" class="block text-sm mb-1">Cable Provider</label>
                <select
                    name="cable_product_plan_category_id"
                    id="cable_product_plan_category_id"
                    required
                    class="w-full px-3 py-2 rounded-lg bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-emerald-500 text-sm"
                >
                    <option value="">Select</option>
                    @foreach($product_plan_categories as $category)
                        <option value="{{ $category->id }}">{{ $category->product_plan_category_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Plans -->
            <div>
                <label class="block text-sm mb-1">Available Plans</label>
                <div class="relative max-h-64 overflow-y-auto border rounded-lg p-3 scrollbar-thin scrollbar-thumb-emerald-500 scrollbar-track-transparent">
                    <div id="plan_grid" class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                        {{-- AJAX injects plans here --}}
                    </div>
                    <div class="absolute bottom-1 right-2 text-[10px] text-gray-400 italic pointer-events-none">Scroll ↓</div>
                </div>
                <div id="plan_error" class="text-red-500 text-xs mt-2 hidden">Please select a cable plan</div>
            </div>

            <!-- Smartcard Number -->
            <div>
                <label for="smart_card_number" class="block text-sm mb-1">Smartcard Number</label>
                <input
                    type="text"
                    name="smart_card_number"
                    id="smart_card_number"
                    required
                    placeholder="e.g. 1234567890"
                    class="w-full px-3 py-2 rounded-lg bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-emerald-500 text-sm"
                >
            </div>

            <!-- Name Preview -->
            <div>
                <label class="block text-sm mb-1">Name on Card</label>
                <div id="smartcard_name_preview" class="w-full px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 border text-gray-600 dark:text-gray-300 text-sm">
                    Not yet verified
                </div>
            </div>

            <!-- PIN -->
            <div>
                <label for="pin" class="block text-sm mb-1">Transaction PIN</label>
                <input
                    type="password"
                    name="pin"
                    id="pin"
                    maxlength="4"
                    minlength="4"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    required
                    placeholder="****"
                    class="w-full px-3 py-2 rounded-lg bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-emerald-500 text-sm"
                >
            </div>

            <!-- Submit -->
            <button
                type="submit"
                id="buy_cable_btn"
                class="w-full py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition disabled:opacity-50"
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
</div>
@endsection
