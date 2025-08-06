@extends('oresamsub.layouts.authapp')

@section('content')
<div class="relative min-h-screen w-full flex items-center justify-center overflow-hidden">

  <!-- Background Pattern -->
  <div class="absolute inset-0 z-0 pointer-events-none">
    <div class="w-full h-full bg-gray-50 dark:bg-gray-900 bg-[radial-gradient(circle,_rgba(0,0,0,0.05)_1px,_transparent_1px)] dark:bg-[radial-gradient(circle,_rgba(255,255,255,0.05)_1px,_transparent_1px)] [background-size:22px_22px]"></div>
  </div>

  <!-- Reset Password Card -->
  <div class="relative z-10 pt-10 pb-6 max-w-xs w-full mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6" x-data="{ isSubmitting: false }">
    <h2 class="text-2xl font-bold text-center mb-4 text-gray-900 dark:text-white">Reset Password</h2>

    {{-- Success Message --}}
    @if (session('status'))
      <div class="bg-green-100 border border-green-300 text-green-700 text-sm p-2 rounded mb-4 text-center">
        {{ session('status') }}
      </div>
    @endif

    <!-- Form -->
    @extends('oresamsub.layouts.authapp')

    @section('content')
    <div class="relative min-h-screen w-full flex items-center justify-center overflow-hidden">
    
      <!-- Background Pattern -->
      <div class="absolute inset-0 z-0 pointer-events-none">
        <div class="w-full h-full bg-gray-50 dark:bg-gray-900 bg-[radial-gradient(circle,_rgba(0,0,0,0.05)_1px,_transparent_1px)] dark:bg-[radial-gradient(circle,_rgba(255,255,255,0.05)_1px,_transparent_1px)] [background-size:22px_22px]"></div>
      </div>
    
      <!-- Card -->
      <div class="relative z-10 pt-10 pb-6 max-w-xs w-full mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6" x-data="{ isSubmitting: false }">
        <h2 class="text-2xl font-bold text-center mb-4 text-gray-900 dark:text-white">Reset Your Password</h2>
    
        <p class="text-sm text-gray-600 dark:text-gray-300 text-center mb-6">
          Enter your email and set a new password and PIN to secure your account.
        </p>
    
        <form method="POST" action="{{ route('password.store') }}" @submit.prevent="isSubmitting = true; $el.submit();">
          @csrf
          <input type="hidden" name="token" value="{{ $request->route('token') }}">
    
          <!-- Email -->
          <div class="mb-4">
            <label for="email" class="block text-sm mb-1 text-gray-700 dark:text-gray-300">Email</label>
            <input
              id="email"
              name="email"
              type="email"
              :value="old('email', $request->email)"
              required
              autofocus
              class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
          </div>
    
          <!-- Password -->
          <div class="mb-4" x-data="{ show: false }">
            <label for="password" class="block text-sm mb-1 text-gray-700 dark:text-gray-300">New Password</label>
            <div class="relative">
              <input
                :type="show ? 'text' : 'password'"
                id="password"
                name="password"
                required
                autocomplete="new-password"
                class="w-full px-4 py-2 pr-10 rounded-lg bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
              <div class="absolute inset-y-0 right-3 flex items-center cursor-pointer" @click="show = !show">
                <svg x-show="!show" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7s-8.268-2.943-9.542-7z" />
                </svg>
                <svg x-show="show" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.055 10.055 0 012.293-3.95M3 3l18 18" />
                </svg>
              </div>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
          </div>
    
          <!-- Confirm Password -->
          <div class="mb-4" x-data="{ show: false }">
            <label for="password_confirmation" class="block text-sm mb-1 text-gray-700 dark:text-gray-300">Confirm Password</label>
            <div class="relative">
              <input
                :type="show ? 'text' : 'password'"
                id="password_confirmation"
                name="password_confirmation"
                required
                autocomplete="new-password"
                class="w-full px-4 py-2 pr-10 rounded-lg bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
              <div class="absolute inset-y-0 right-3 flex items-center cursor-pointer" @click="show = !show">
                <svg x-show="!show" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7s-8.268-2.943-9.542-7z" />
                </svg>
                <svg x-show="show" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19m0-14c4.477 0 8.268 2.943 9.542 7m0 0c-1.274 4.057-5.064 7-9.542 7m0-14c-4.478 0-8.268 2.943-9.542 7M3 3l18 18" />
                </svg>
              </div>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
          </div>
    
          <!-- New PIN -->
          <div class="mb-4" x-data="{ show: false }">
            <label for="new_pin" class="block text-sm mb-1 text-gray-700 dark:text-gray-300">New PIN</label>
            <div class="relative">
              <input
                :type="show ? 'text' : 'password'"
                id="new_pin"
                name="new_pin"
                maxlength="4"
                required
                class="w-full px-4 py-2 pr-10 rounded-lg bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
              <div class="absolute inset-y-0 right-3 flex items-center cursor-pointer" @click="show = !show">
                <svg x-show="!show" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7s-8.268-2.943-9.542-7z" />
                </svg>
                <svg x-show="show" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19M3 3l18 18" />
                </svg>
              </div>
            </div>
            <x-input-error :messages="$errors->get('new_pin')" class="mt-2" />
          </div>
    
          <!-- Confirm New PIN -->
          <div class="mb-6" x-data="{ show: false }">
            <label for="new_pin_confirmation" class="block text-sm mb-1 text-gray-700 dark:text-gray-300">Confirm New PIN</label>
            <div class="relative">
              <input
                :type="show ? 'text' : 'password'"
                id="new_pin_confirmation"
                name="new_pin_confirmation"
                maxlength="4"
                required
                class="w-full px-4 py-2 pr-10 rounded-lg bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
              <div class="absolute inset-y-0 right-3 flex items-center cursor-pointer" @click="show = !show">
                <svg x-show="!show" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7s-8.268-2.943-9.542-7z" />
                </svg>
                <svg x-show="show" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19M3 3l18 18" />
                </svg>
              </div>
            </div>
            <x-input-error :messages="$errors->get('new_pin_confirmation')" class="mt-2" />
          </div>
    
          <!-- Submit -->
          <button
            type="submit"
            class="w-full py-2 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
            :disabled="isSubmitting"
          >
            <span x-show="!isSubmitting">🔄 Reset Password</span>
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
    </div>
    @endsection
    

    <!-- Back to login -->
    <p class="text-xs text-center mt-6 text-gray-500 dark:text-gray-400" x-data="{ loading: false }">
      <a 
        href="{{ route('login') }}"
        @click.prevent="loading = true; setTimeout(() => window.location.href = '{{ route('login') }}', 300)"
        class="text-blue-600 dark:text-blue-400 font-semibold"
        x-show="!loading"
      >
        ← Back to Login
      </a>

      <span x-show="loading" x-cloak class="text-blue-500 dark:text-blue-300 font-semibold flex justify-center items-center gap-1">
        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
          <circle class="opacity-25" cx="12" cy="12" r="10"
                  stroke="currentColor" stroke-width="4" />
          <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8z"/>
        </svg>
        Redirecting...
      </span>
    </p>
  </div>
</div>
@endsection
