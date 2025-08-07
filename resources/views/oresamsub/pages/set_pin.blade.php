@extends('oresamsub.layouts.app')

@section('content')
<div class="pt-6 max-w-sm mx-auto pb-24" x-data="{ isSubmitting: false }">

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
  
  

  {{-- <h2 class="text-xl font-bold text-center mb-6">Set PIN</h2> --}}

  @if(session('success'))
    <div class="mb-4 bg-green-100 text-green-800 text-sm px-4 py-2 rounded-lg">
      {{ session('success') }}
    </div>
  @elseif(session('failure'))
    <div class="mb-4 bg-red-100 text-red-800 text-sm px-4 py-2 rounded-lg">
      {{ session('failure') }}
    </div>
  @endif




  <div class="pt-6 max-w-sm mx-auto pb-24" x-data="{ isSubmitting: false }">
    {{-- <form method="POST" action="{{ route('user.settings.store_set_pin') }}" @submit.prevent="isSubmitting = true" x-data="{ showPin: false, showConfirmPin: false, isSubmitting: false }"> --}}
    <form method="POST" action="{{ route('user.settings.store_set_pin') }}" x-data="{ showPin: false, showConfirmPin: false, isSubmitting: false }">

        @csrf
          
            <div class="mb-4">
              <h2 class="text-lg font-bold text-gray-800 dark:text-white text-center">Set Your Transaction PIN</h2>
              <p class="text-sm text-gray-500 dark:text-gray-400 text-center">Enter a secure 4-digit PIN to authorize transactions.</p>
            </div>
          
            <!-- PIN -->
            <div class="mb-4">
              <label for="pin" class="block text-sm mb-1">PIN</label>
              <div class="relative">
                <input
                  :type="showPin ? 'text' : 'password'"
                  name="pin"
                  id="pin"
                  required
                  maxlength="4"
                  minlength="4"
                  inputmode="numeric"
                  pattern="[0-9]*"
                  placeholder="Enter 4-digit PIN"
                  class="w-full px-4 py-2 pr-10 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                <button type="button" @click="showPin = !showPin" class="absolute inset-y-0 right-2 flex items-center text-sm text-gray-500">
                  <span x-text="showPin ? 'Hide' : 'Show'"></span>
                </button>
              </div>
            </div>
          
            <!-- Confirm PIN -->
            <div class="mb-6">
              <label for="confirm_pin" class="block text-sm mb-1">Confirm PIN</label>
              <div class="relative">
                <input
                  :type="showConfirmPin ? 'text' : 'password'"
                  name="confirm_pin"
                  id="confirm_pin"
                  required
                  maxlength="4"
                  minlength="4"
                  inputmode="numeric"
                  pattern="[0-9]*"
                  placeholder="Re-enter 4-digit PIN"
                  class="w-full px-4 py-2 pr-10 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                <button type="button" @click="showConfirmPin = !showConfirmPin" class="absolute inset-y-0 right-2 flex items-center text-sm text-gray-500">
                  <span x-text="showConfirmPin ? 'Hide' : 'Show'"></span>
                </button>
              </div>
            </div>
          
            <!-- Submit -->
            <button
              type="submit"
              class="w-full py-2 px-4 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition disabled:opacity-50"
              :disabled="isSubmitting"
            >
              <span x-show="!isSubmitting">🔐 Set PIN</span>
              <span x-show="isSubmitting" x-cloak class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10"
                          stroke="currentColor" stroke-width="4" fill="none"/>
                  <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8z"/>
                </svg>
                Setting PIN...
              </span>
            </button>
          </form>
          
      
  
</div>
@endsection
