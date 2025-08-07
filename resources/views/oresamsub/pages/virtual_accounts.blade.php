@extends('oresamsub.layouts.app')

@section('content')
<div class="pt-6 max-w-sm mx-auto">

  <!-- Back to Home Navigation -->
  <div class="mb-4">
    <a 
    href="{{ route('dashboard') }}"
    @click.prevent="showLoader = true; setTimeout(() => window.location.href = '{{ route('dashboard') }}', 1000)"
    class="inline-flex items-center px-3 py-1.5 rounded-md 
           bg-emerald-600 hover:bg-emerald-700 
           text-white 
           text-xs font-semibold 
           transition-all duration-200 shadow"
  >
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    Back to Dashboard
  </a>
  
  
  </div>

  <h2 class="text-xl font-bold text-center mb-6 text-emerald-700 dark:text-emerald-300">My Virtual Accounts</h2>

  <div class="space-y-4">
    @foreach ($virtualccts as $account)
      <div x-data="{ copied: false }" class="p-4 bg-white dark:bg-gray-900 rounded-xl shadow space-y-1 border border-emerald-100 dark:border-emerald-800">
        <div class="flex items-center justify-between">
          <div class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $account->bank_name }}</div>
          <span x-show="copied" x-transition class="text-xs text-emerald-500">Copied ✅</span>
        </div>
        <div class="text-sm text-gray-700 dark:text-gray-300">Acct Name: {{ $account->account_name }}</div>
        <div class="flex justify-between items-center mt-1">
          <div class="text-lg font-mono tracking-wide text-gray-900 dark:text-white">
            {{ $account->account_number }}
          </div>
          <button
          @click="navigator.clipboard.writeText('{{ $account->account_number }}'); copied = true; setTimeout(() => copied = false, 2000)"
          class="text-xs px-3 py-1 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-emerald-500 dark:hover:bg-emerald-600 transition"
        >
          Copy
        </button>
        
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
