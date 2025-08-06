@extends('oresamsub.layouts.authapp')

@section('content')
<div class="relative min-h-screen w-full flex items-center justify-center overflow-hidden">

  <!-- Background Pattern -->
  <div class="absolute inset-0 z-0 pointer-events-none">
    <div class="w-full h-full bg-gray-50 dark:bg-gray-900 bg-[radial-gradient(circle,_rgba(0,0,0,0.05)_1px,_transparent_1px)] dark:bg-[radial-gradient(circle,_rgba(255,255,255,0.05)_1px,_transparent_1px)] [background-size:22px_22px]"></div>
  </div>

  <!-- Reset Password Card -->
  <div class="relative z-10 pt-10 pb-6 max-w-xs w-full mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6" x-data="{ isSubmitting: false }">
    <h2 class="text-2xl font-bold text-center mb-4 text-gray-900 dark:text-white">Forgot Password</h2>

    <!-- Note -->
    <p class="text-sm text-gray-600 dark:text-gray-300 text-center mb-6">
      Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
      <br><br>
      <strong>Please check your spam folder too</strong> in case you don’t find the email notification sent to you in your inbox.
    </p>

    {{-- Success Message --}}
    @if (session('status'))
      <div class="bg-green-100 border border-green-300 text-green-700 text-sm p-2 rounded mb-4 text-center">
        {{ session('status') }}
      </div>
    @endif

    <!-- Form -->
    <form method="POST" action="{{ route('password.email') }}" @submit.prevent="isSubmitting = true; $el.submit();">
      @csrf

      <!-- Email -->
      <div class="mb-4">
        <label for="email" class="block text-sm mb-1">Email</label>
        <input
          type="email"
          name="email"
          id="email"
          required
          autofocus
          class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
      </div>

      <!-- Submit Button -->
      <div class="mt-6">
        <button
          type="submit"
          class="w-full py-2 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
          :disabled="isSubmitting"
        >
          <span x-show="!isSubmitting">📩 Send Reset Link</span>
          <span x-show="isSubmitting" x-cloak class="flex items-center justify-center gap-2">
            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10"
                      stroke="currentColor" stroke-width="4" fill="none"/>
              <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8z"/>
            </svg>
            Sending...
          </span>
        </button>
      </div>
    </form>

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
