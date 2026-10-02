@extends('oresamsub.layouts.app')

@section('content')
<div
  class="max-w-md mx-auto px-4 pt-5 pb-24"
  x-data="{
    pin: @js(old('pin', '')),
    confirmPin: @js(old('confirm_pin', '')),
    showPin: false,
    showConfirmPin: false,
    isSubmitting: false,
    clean(value) {
      return String(value || '').replace(/\D/g, '').slice(0, 4);
    },
    get pinReady() {
      return this.pin.length === 4;
    },
    get pinsMatch() {
      return this.confirmPin.length === 4 && this.pin === this.confirmPin;
    },
    get weakPin() {
      return ['0000', '1111', '1234', '2222', '3333', '4444', '5555', '6666', '7777', '8888', '9999'].includes(this.pin);
    },
    get canSubmit() {
      return this.pinReady && this.pinsMatch && !this.weakPin && !this.isSubmitting;
    }
  }"
>
  @if(session('success'))
    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
      {{ session('success') }}
    </div>
  @endif

  @if(session('failure'))
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
      {{ session('failure') }}
    </div>
  @endif

  @if($errors->any())
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
      <p class="font-semibold">Please check your PIN and try again.</p>
      <ul class="mt-1 list-disc pl-5">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
    <div class="mb-5 text-center">
      <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V7.75a4.5 4.5 0 0 0-9 0v2.75m-.75 0h10.5A1.75 1.75 0 0 1 19 12.25v6A1.75 1.75 0 0 1 17.25 20H6.75A1.75 1.75 0 0 1 5 18.25v-6a1.75 1.75 0 0 1 1.75-1.75Z" />
        </svg>
      </div>
      <h2 class="text-xl font-bold text-gray-900 dark:text-white">Set Your Transaction PIN</h2>
      <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
        Choose 4 numbers you can remember. You will use this PIN to confirm purchases and wallet actions.
      </p>
    </div>

    <div class="mb-5 rounded-xl bg-amber-50 px-4 py-3 text-xs leading-5 text-amber-800 dark:bg-amber-900/30 dark:text-amber-100">
      Do not use your ATM PIN, date of birth, or simple numbers like 1234 or 0000.
    </div>

    <form
      method="POST"
      action="{{ route('user.settings.store_set_pin') }}"
      @submit="if (!canSubmit) { $event.preventDefault(); return; } isSubmitting = true"
      novalidate
    >
      @csrf

      <div class="mb-4">
        <div class="mb-1 flex items-center justify-between gap-3">
          <label for="pin" class="text-sm font-semibold text-gray-800 dark:text-gray-100">New PIN</label>
          <span class="text-xs text-gray-500" x-text="pin.length + '/4'"></span>
        </div>
        <div class="relative">
          <input
            :type="showPin ? 'text' : 'password'"
            name="pin"
            id="pin"
            required
            maxlength="4"
            inputmode="numeric"
            autocomplete="new-password"
            placeholder="Enter 4 numbers"
            x-model="pin"
            @input="pin = clean($event.target.value)"
            class="w-full rounded-xl border px-4 py-3 pr-16 text-base tracking-[0.35em] text-gray-900 outline-none transition focus:ring-2 dark:bg-gray-800 dark:text-white"
            :class="pin.length > 0 && (!pinReady || weakPin) ? 'border-red-300 focus:border-red-400 focus:ring-red-100' : 'border-gray-300 focus:border-emerald-500 focus:ring-emerald-100 dark:border-gray-600'"
          >
          <button type="button" @click="showPin = !showPin" class="absolute inset-y-0 right-3 text-sm font-semibold text-emerald-700 dark:text-emerald-300">
            <span x-text="showPin ? 'Hide' : 'Show'"></span>
          </button>
        </div>
        <p x-show="pin.length > 0 && !pinReady" x-cloak class="mt-1 text-xs text-red-600">PIN must be exactly 4 digits.</p>
        <p x-show="weakPin" x-cloak class="mt-1 text-xs text-red-600">Please choose a stronger PIN.</p>
      </div>

      <div class="mb-5">
        <div class="mb-1 flex items-center justify-between gap-3">
          <label for="confirm_pin" class="text-sm font-semibold text-gray-800 dark:text-gray-100">Confirm PIN</label>
          <span class="text-xs text-gray-500" x-text="confirmPin.length + '/4'"></span>
        </div>
        <div class="relative">
          <input
            :type="showConfirmPin ? 'text' : 'password'"
            name="confirm_pin"
            id="confirm_pin"
            required
            maxlength="4"
            inputmode="numeric"
            autocomplete="new-password"
            placeholder="Re-enter the same PIN"
            x-model="confirmPin"
            @input="confirmPin = clean($event.target.value)"
            class="w-full rounded-xl border px-4 py-3 pr-16 text-base tracking-[0.35em] text-gray-900 outline-none transition focus:ring-2 dark:bg-gray-800 dark:text-white"
            :class="confirmPin.length > 0 && !pinsMatch ? 'border-red-300 focus:border-red-400 focus:ring-red-100' : 'border-gray-300 focus:border-emerald-500 focus:ring-emerald-100 dark:border-gray-600'"
          >
          <button type="button" @click="showConfirmPin = !showConfirmPin" class="absolute inset-y-0 right-3 text-sm font-semibold text-emerald-700 dark:text-emerald-300">
            <span x-text="showConfirmPin ? 'Hide' : 'Show'"></span>
          </button>
        </div>
        <p x-show="confirmPin.length > 0 && !pinsMatch" x-cloak class="mt-1 text-xs text-red-600">Both PIN entries must match.</p>
        <p x-show="pinsMatch && !weakPin" x-cloak class="mt-1 text-xs text-emerald-700">PIN looks good.</p>
      </div>

      <button
        type="submit"
        class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
        :disabled="!canSubmit"
      >
        <svg x-show="!isSubmitting" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4M12 3.75l7.5 3v5.5c0 4.55-3.1 7.35-7.5 8-4.4-.65-7.5-3.45-7.5-8v-5.5l7.5-3Z" />
        </svg>
        <svg x-show="isSubmitting" x-cloak class="h-4 w-4 animate-spin" viewBox="0 0 24 24" aria-hidden="true">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4l3-3-3-3v4a8 8 0 0 0-8 8z"/>
        </svg>
        <span x-text="isSubmitting ? 'Setting PIN...' : 'Set Transaction PIN'"></span>
      </button>
    </form>

    <a
      href="https://wa.me/2349163128718?text=Hello%20OresamSub%20Support%2C%20I%20need%20help%20setting%20my%20transaction%20PIN."
      class="mt-4 flex items-center justify-center rounded-xl border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800"
    >
      Need help? Chat support
    </a>
  </div>
</div>
@endsection
