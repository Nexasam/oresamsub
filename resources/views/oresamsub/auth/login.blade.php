@extends('oresamsub.layouts.authapp')

@section('content')
<div class="pt-10 pb-6 max-w-xs mx-auto" x-data="{ isLoggingIn: false }">
  <h2 class="text-2xl font-bold text-center mb-6">Login to OresamSub</h2>

  @if(session('error'))
    <div class="mb-4 text-red-600 text-sm text-center">{{ session('error') }}</div>
  @endif

  <form method="POST" action="{{ route('login') }}" @submit.prevent="isLoggingIn = true; $el.submit();">
    @csrf

    <!-- Email -->
    <div class="mb-4">
      <label for="email" class="block text-sm mb-1">Email</label>
      <input
        type="email"
        name="email"
        id="email"
        required
        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
    </div>

    <!-- Password -->
    <div class="mb-4">
      <label for="password" class="block text-sm mb-1">Password</label>
      <input
        type="password"
        name="password"
        id="password"
        required
        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
    </div>

    <!-- Submit -->
    <div class="mt-6">
      <button
        type="submit"
        class="w-full py-2 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
        :disabled="isLoggingIn"
      >
        <span x-show="!isLoggingIn">🔐 Login</span>
        <span x-show="isLoggingIn" x-cloak class="flex items-center justify-center gap-2">
          <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10"
                    stroke="currentColor" stroke-width="4" fill="none"/>
            <path class="opacity-75" fill="currentColor"
                  d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8z"/>
          </svg>
          Logging in...
        </span>
      </button>
    </div>
  </form>

  <p class="text-xs text-center mt-6 text-gray-500 dark:text-gray-400">
    Don't have an account? <a href="{{ route('register') }}" class="text-blue-600 dark:text-blue-400 font-semibold">Register</a>
  </p>
</div>
@endsection
