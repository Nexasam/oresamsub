@extends('oresamsub.layouts.authapp')

@section('content')
<div class="relative min-h-screen w-full flex items-center justify-center overflow-hidden">


  
  <!-- Background Pattern -->
 <!-- Background Pattern -->
<div class="absolute inset-0 z-0 pointer-events-none">
  <div class="w-full h-full bg-gray-50 dark:bg-gray-900 bg-[radial-gradient(circle,_rgba(0,0,0,0.05)_1px,_transparent_1px)] dark:bg-[radial-gradient(circle,_rgba(255,255,255,0.05)_1px,_transparent_1px)] [background-size:22px_22px]"></div>
</div>



  <!-- Login Card -->
  <div class="relative z-10 pt-10 pb-6 max-w-xs w-full mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6" x-data="{ isLoggingIn: false }">

    <<a href="{{ route('ore.dashboard') }}" class="flex flex-col items-center mb-4">
      <img 
        src="{{ asset('assets/logo_imgs/oresamsublogo.jpeg') }}" 
        alt="OresamSub Logo" 
        class="h-20 w-20 rounded-full shadow-md"
      >
      {{-- Optional text below logo --}}
      {{-- <span class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-2">OresamSub</span> --}}
    </a>

    <h2 class="text-2xl font-bold text-center mb-6 text-gray-900 dark:text-white">Login to OresamSub</h2>

    {{-- Feedback Alerts --}}
    @if(session('error'))
      <div class="mb-4 text-red-600 text-sm text-center">{{ session('error') }}</div>
    @endif

    @if (Session::has('success'))
      <div class="bg-green-100 border border-green-300 text-green-700 text-sm p-2 rounded mb-4 text-center">
        {{ Session::get('success') }}
      </div>
    @endif

    @if (Session::has('failure'))
      <div class="bg-red-100 border border-red-300 text-red-700 text-sm p-2 rounded mb-4 text-center">
        {{ Session::get('failure') }}
      </div>
    @endif

    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}" @submit.prevent="isLoggingIn = true; $el.submit();">
      @csrf

      <!-- Email -->
      <div class="mb-4">
        <label for="email" class="block text-sm mb-1">Email</label>
        <input
          type="text"
          name="email"
          id="email"
          placeholder="Email or Username or Phone"
          required
          class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
      </div>

      <!-- Password -->
      <div class="mb-0">
        <label for="password" class="block text-sm mb-1">Password</label>
        <input
          type="password"
          name="password"
          id="password"
          required
          class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
      </div>

      <!-- Forgot Password Link -->
      <div class="mb-2 text-right">
        <a href="{{ route('password.request') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
          Forgot your password?
        </a>
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

    <!-- Register Link -->
    <p class="text-xs text-center mt-6 text-gray-500 dark:text-gray-400" x-data="{ loading: false }">
      Don't have an account?
      <a 
        href="{{ route('register') }}"
        @click.prevent="loading = true; setTimeout(() => window.location.href = '{{ route('register') }}', 300)"
        class="text-blue-600 dark:text-blue-400 font-semibold"
        x-show="!loading"
      >
        Register
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

