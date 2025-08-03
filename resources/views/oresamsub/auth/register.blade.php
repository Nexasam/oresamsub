@extends('oresamsub.layouts.app')

@section('content')
<div class="pt-8 pb-6 max-w-xs mx-auto" x-data="{ isRegistering: false }">
  <h2 class="text-2xl font-bold text-center mb-6">Create an Account</h2>

  @if($errors->any())
    <div class="mb-4 text-red-600 text-sm text-center">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('register') }}" @submit.prevent="isRegistering = true; $el.submit();">
    @csrf

    <!-- Full Name -->
    <div class="mb-4">
      <label for="name" class="block text-sm mb-1">Full Name</label>
      <input
        type="text"
        name="name"
        id="name"
        required
        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
        value="{{ old('name') }}"
      >
    </div>

    <!-- Username -->
    <div class="mb-4">
      <label for="username" class="block text-sm mb-1">Username</label>
      <input
        type="text"
        name="username"
        id="username"
        required
        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
        value="{{ old('username') }}"
      >
    </div>

    <!-- Phone -->
    <div class="mb-4">
      <label for="phone" class="block text-sm mb-1">Phone Number</label>
      <input
        type="tel"
        name="phone"
        id="phone"
        required
        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
        value="{{ old('phone') }}"
      >
    </div>

    <!-- Email -->
    <div class="mb-4">
      <label for="email" class="block text-sm mb-1">Email Address</label>
      <input
        type="email"
        name="email"
        id="email"
        required
        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
        value="{{ old('email') }}"
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

    <!-- Confirm Password -->
    <div class="mb-6">
      <label for="password_confirmation" class="block text-sm mb-1">Confirm Password</label>
      <input
        type="password"
        name="password_confirmation"
        id="password_confirmation"
        required
        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
    </div>

    <!-- Submit -->
    <div>
      <button
        type="submit"
        class="w-full py-2 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
        :disabled="isRegistering"
      >
        <span x-show="!isRegistering">📝 Create Account</span>
        <span x-show="isRegistering" x-cloak class="flex items-center justify-center gap-2">
          <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10"
                    stroke="currentColor" stroke-width="4" fill="none"/>
            <path class="opacity-75" fill="currentColor"
                  d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8z"/>
          </svg>
          Registering...
        </span>
      </button>
    </div>
  </form>

  <p class="text-xs text-center mt-6 text-gray-500 dark:text-gray-400">
    Already have an account? <a href="{{ route('login') }}" class="text-blue-600 dark:text-blue-400 font-semibold">Login</a>
  </p>
</div>
@endsection
