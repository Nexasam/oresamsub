@extends('oresamsub.layouts.authapp')

@section('content')
<div class="pt-10 pb-6 max-w-full mx-auto" 
    x-data="{ 
      isRegistering: false, 
      showPassword: false, 
      showConfirm: false,
      acceptTerms: false
    }">

  <a href="{{ route('dashboard') }}" class="flex flex-col items-center mb-4">
    <img src="{{ asset('assets/logo_imgs/oresamsublogo.jpeg') }}" 
         alt="OresamSub Logo" 
         class="h-20 w-20 rounded-full shadow-md">
  </a>
  
  <h2 class="text-2xl font-bold text-center mb-6">Create Your Account</h2>

  <!-- Alerts -->
  <div class="cols-span-1">
    @if (Session::has('success'))
      <div class="bg-success/10 border border-success/10 alert text-success">
        {{ Session::get('success') }}
      </div>
    @endif

    @if (Session::has('failure'))
      <div class="bg-danger/10 border border-danger/10 alert text-danger">
        {{ Session::get('failure') }}
      </div>
    @endif
  </div>

  <form method="POST" action="{{ route('store2') }}" 
        @submit.prevent="
        
         if(!acceptTerms) return;
          isRegistering = true; 
          $el.submit();
        ">
    @csrf

    <!-- Inputs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">

      <!-- Full Name -->
      <div>
        <label class="block text-sm mb-1">Full Name</label>
        <input type="text" name="fullname" value="{{ old('fullname') }}" required
          placeholder="Firstname Lastname"
          class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600">
        <x-input-error :messages="$errors->get('fullname')" />
      </div>

      <!-- Username -->
      <div>
        <label class="block text-sm mb-1">Username</label>
        <input type="text" name="username" value="{{ old('username') }}" required
          class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600">
        <x-input-error :messages="$errors->get('username')" />
      </div>

      <!-- Phone -->
      <div>
        <label class="block text-sm mb-1">Phone Number</label>
        <input type="tel" name="phone_number" value="{{ old('phone_number') }}" required
          placeholder="08012345678"
          class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600">
        <x-input-error :messages="$errors->get('phone_number')" />
      </div>

      <!-- Email -->
      <div>
        <label class="block text-sm mb-1">Email Address</label>
        <input type="email" name="email" value="{{ old('email') }}" required
          class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600">
        <x-input-error :messages="$errors->get('email')" />
      </div>

      <!-- Referral -->
      <div class="sm:col-span-2">
        <label class="block text-sm mb-1">Referral Phone (Optional)</label>
        <input type="number"
          @if ($upline) readonly @endif
          name="upline_referral_phone_number"
          value="{{ $upline ?? old('upline_referral_phone_number') }}"
          placeholder="Upline's phone number"
          class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600">
        <x-input-error :messages="$errors->get('upline_referral_phone_number')" />
      </div>

    </div>

    <!-- Password -->
    <div class="mb-4">
      <label class="block text-sm mb-1">Password</label>
      <div class="relative">
        <input :type="showPassword ? 'text' : 'password'" name="password" required
          class="w-full px-4 py-2 pr-10 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600">
        <button type="button" @click="showPassword = !showPassword"
          class="absolute right-3 top-2 text-sm">Show</button>
      </div>
      <x-input-error :messages="$errors->get('password')" />
    </div>

    <!-- Confirm -->
    <div class="mb-6">
      <label class="block text-sm mb-1">Confirm Password</label>
      <div class="relative">
        <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" required
          class="w-full px-4 py-2 pr-10 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600">
        <button type="button" @click="showConfirm = !showConfirm"
          class="absolute right-3 top-2 text-sm">Show</button>
      </div>
    </div>

    <!-- Usage -->
    <div class="mb-4" x-data="{ usage: '{{ old('usage') ?? '' }}' }">
      <label class="block text-sm mb-1">How will you use OresamSub?</label>
      <select name="usage" x-model="usage" required
        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600">
        <option value="">Select</option>
        <option value="Personal">Personal</option>
        <option value="Business">Business</option>
      </select>

      <div x-show="usage === 'Business'" class="mt-3">
        <label class="block text-sm mb-1">Weekly volume</label>
        <select name="transaction_volume"
          class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600">
          <option value="">Select</option>
          <option>100 - 199</option>
          <option>200 - 999</option>
          <option>1000 - 4999</option>
          <option>5000+</option>
        </select>
      </div>
    </div>

    <!-- ✅ LEGAL CONSENT -->
    <div class="mt-4">
      <label class="flex items-start gap-2 text-sm text-gray-600 dark:text-white/70">
        <input type="checkbox" x-model="acceptTerms" class="mt-1">
        <span>
          I agree to the
          <a href="{{ route('privacy.policy') }}" target="_blank" class="text-blue-600 underline">Privacy Policy</a>
          and
          <a href="{{ route('terms') }}" target="_blank" class="text-blue-600 underline">Terms of Service</a>
        </span>
      </label>
    </div>

    <!-- Trust -->
    <div class="mt-3 text-xs text-gray-500 dark:text-white/60">
      🔒 Your data is secure. Payments are processed safely. Failed transactions are refunded automatically.
    </div>

    <!-- Submit -->
    <div class="mt-6">
      <button type="submit"
        class="w-full py-2 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
        :disabled="!acceptTerms || isRegistering">
        <span x-show="!isRegistering">📝 Create Account</span>
        <span x-show="isRegistering" class="flex justify-center gap-2">
          <span class="animate-spin">⏳</span> Registering...
        </span>
      </button>
    </div>

  </form>

  <!-- Login -->
  <p class="text-xs text-center mt-6 text-gray-500 dark:text-gray-400">
    Already have an account?
    <a href="{{ route('login') }}" class="text-blue-600 font-semibold">Login</a>
  </p>

</div>
@endsection