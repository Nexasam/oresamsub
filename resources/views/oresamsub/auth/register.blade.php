@extends('oresamsub.layouts.authapp')

@section('content')
<div class="pt-10 pb-6 max-w-full mx-auto" x-data="{ isRegistering: false, showPassword: false, showConfirm: false }">

  <a href="{{ route('dashboard') }}" class="flex flex-col items-center mb-4">
    <img 
      src="{{ asset('assets/logo_imgs/oresamsublogo.jpeg') }}" 
      alt="OresamSub Logo" 
      class="h-20 w-20 rounded-full shadow-md"
    >
    {{-- Optional text below logo --}}
    {{-- <span class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-2">OresamSub</span> --}}
  </a>
  
  <h2 class="text-2xl font-bold text-center mb-6">Create Your Account</h2>

  {{-- @if (Session::has('success'))
    <div class="bg-success/10 border border-success/10 alert text-success" role="alert">
        {{ Session::get('success') }}
    </div>
  @endif

  @if (Session::has('failure'))
    <div class="bg-danger/10 border border-danger/10 alert text-danger" role="alert">
      {{ Session::get('failure') }}
    </div>
  @endif --}}

  <div class="cols-span-1">
    @if (Session::has('success'))
    <div class="bg-success/10 border border-success/10 alert text-success" role="alert">
    {{ Session::get('success') }}
    </div>
    @endif

    @if (Session::has('failure'))
    <div class="bg-danger/10 border border-danger/10 alert text-danger" role="alert">
    {{ Session::get('failure') }}
    </div>
    @endif
  </div>

  <form method="POST" action="{{ route('store2') }}" @submit.prevent="isRegistering = true; $el.submit();">
    @csrf

    <!-- Grid Inputs: 2 per row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
      <!-- Full Name -->
      <div>
        <label for="fullname" class="block text-sm mb-1">Full Name</label>
        <input
          placeholder="Firstname Lastname"
          type="text"
          name="fullname"
          id="fullname"
          value="{{ old('fullname') }}"
          required
          class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        <x-input-error :messages="$errors->get('fullname')" class="mt-1" />
      </div>

      <!-- Username -->
      <div>
        <label for="username" class="block text-sm mb-1">Username</label>
        <input
          type="text"
          name="username"
          id="username"
          value="{{ old('username') }}"
          required
          class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        <x-input-error :messages="$errors->get('username')" class="mt-1" />
      </div>

      <!-- Phone -->
      <div>
        <label for="phone_number" class="block text-sm mb-1">Phone Number</label>
        <input
          type="tel"
          name="phone_number"
          id="phone_number"
          value="{{ old('phone_number') }}"
          required
          placeholder="e.g. 08012345678"
          class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        <x-input-error :messages="$errors->get('phone_number')" class="mt-1" />
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block text-sm mb-1">Email Address</label>
        <input
          type="email"
          name="email"
          id="email"
          value="{{ old('email') }}"
          required
          class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        <x-input-error :messages="$errors->get('email')" class="mt-1" />
      </div>

      <!-- Referral Phone -->
      <div class="sm:col-span-2">
        <label for="upline_referral_phone_number" class="block text-sm mb-1">Referral Phone (Optional)</label>
        <input
          type="number"
          @if ($upline)
              readonly
          @endif
          name="upline_referral_phone_number"
          id="upline_referral_phone_number"
          value="{{ $upline ?? old('upline_referral_phone_number') }}"
          placeholder="Upline's phone number"
          class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        <x-input-error :messages="$errors->get('upline_referral_phone_number')" class="mt-1" />
      </div>
    </div>

    <!-- Password -->
    <div class="mb-4">
      <label for="password" class="block text-sm mb-1">Password</label>
      <div class="relative">
        <input
          :type="showPassword ? 'text' : 'password'"
          name="password"
          id="password"
          required
          class="w-full px-4 py-2 pr-10 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-2.5 text-sm text-gray-500">
          <span x-text="showPassword ? 'Hide' : 'Show'"></span>
        </button>
      </div>
      <x-input-error :messages="$errors->get('password')" class="mt-1" />
    </div>

    <!-- Confirm Password -->
    <div class="mb-6">
      <label for="password_confirmation" class="block text-sm mb-1">Confirm Password</label>
      <div class="relative">
        <input
          :type="showConfirm ? 'text' : 'password'"
          name="password_confirmation"
          id="password_confirmation"
          required
          class="w-full px-4 py-2 pr-10 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3 top-2.5 text-sm text-gray-500">
          <span x-text="showConfirm ? 'Hide' : 'Show'"></span>
        </button>
      </div>
    </div>

    <!-- Submit Button -->
    <div class="mt-6">
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

  <p class="text-xs text-center mt-6 text-gray-500 dark:text-gray-400" x-data="{ loading: false }">
    Already have an account?
    <a 
      href="{{ route('login') }}"
      @click.prevent="loading = true; setTimeout(() => window.location.href = '{{ route('login') }}', 300)"
      class="text-blue-600 dark:text-blue-400 font-semibold"
      x-show="!loading"
    >
      Login
    </a>

    <span x-show="loading" x-cloak class="text-blue-500 dark:text-blue-300 font-semibold flex justify-center items-center gap-1">
      <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
        <circle class="opacity-25" cx="12" cy="12" r="10"
                stroke="currentColor" stroke-width="4" />
        <path class="opacity-75" fill="currentColor"
              d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8z"/>
      </svg>
      Redirecting to login...
    </span>
  </p>
</div>
@endsection
