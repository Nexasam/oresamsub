@extends('oresamsub.layouts.authapp')

@section('content')
<div class="pt-10 pb-6 max-w-xs mx-auto" x-data="{ isLoggingIn: false }">
  <h2 class="text-2xl font-bold text-center mb-6">Login to OresamSub</h2>

  @if(session('error'))
    <div class="mb-4 text-red-600 text-sm text-center">{{ session('error') }}</div>
  @endif


  @if (Session::has('success'))
  <div class="bg-success/10 border border-success/10 alert text-success" role="alert">
      Success {{-- {{ Session::get('success') }} --}}
  </div>
  @endif

  @if (Session::has('failure'))
  <div class="bg-danger/10 border border-danger/10 alert text-danger" role="alert">
  {{ Session::get('failure') }}
  </div>
  @endif

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
        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
      <x-input-error :messages="$errors->get('email')" class="mt-2" />
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
      <x-input-error :messages="$errors->get('password')" class="mt-2" />
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
      Redirecting to registration...
    </span>
  </p>
  
</div>
@endsection
