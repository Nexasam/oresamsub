@extends('oresamsub.layouts.app')

@section('content')

<div class="space-y-6 pt-2" x-data="{ isWalletLoading: false, isRefreshing: false }">

  {{-- Admin Impersonation Notice --}}
  @if (session()->has('impersonator'))
    <div class="bg-[#10b981] text-white p-2 rounded-xl">
      <a href="{{ route('admin.exit_impersonate') }}" class="underline">
        Exit Impersonation
      </a>
    </div>
  @endif

  {{-- Header --}}
  <div class="flex items-center justify-between">
    <h1 class="text-lg font-semibold">Hi, {{ auth()->user()->name }}</h1>
    <button 
      @click="isRefreshing = true; window.location.reload();" 
      class="flex items-center gap-2 px-3 py-1 rounded-xl border border-[#10b981]/30 hover:bg-[#10b981]/10 text-[#10b981] transition">
      <svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6"/>
      </svg>
      <span>Refresh</span>
    </button>
  </div>

  {{-- Wallet Balance --}}
  <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl shadow ring-1 ring-[#10b981]/20">
    <p class="text-gray-500 dark:text-gray-400">Wallet Balance</p>
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
      ₦{{ number_format(auth()->user()->wallet_balance, 2) }}
    </h2>
    <button class="mt-3 px-3 py-1 bg-[#10b981] hover:bg-[#0ea972] text-white rounded-xl transition">
      Top Up
    </button>
  </div>

  {{-- Quick Actions --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <a href="{{ route('airtime') }}" class="p-4 bg-white dark:bg-gray-800 rounded-2xl shadow hover:shadow-md border border-[#10b981]/20 transition text-center">
      <div class="text-[#10b981] font-semibold">Airtime</div>
    </a>
    <a href="{{ route('data') }}" class="p-4 bg-white dark:bg-gray-800 rounded-2xl shadow hover:shadow-md border border-[#10b981]/20 transition text-center">
      <div class="text-[#10b981] font-semibold">Data</div>
    </a>
    <a href="{{ route('cable') }}" class="p-4 bg-white dark:bg-gray-800 rounded-2xl shadow hover:shadow-md border border-[#10b981]/20 transition text-center">
      <div class="text-[#10b981] font-semibold">Cable</div>
    </a>
    <a href="{{ route('electricity') }}" class="p-4 bg-white dark:bg-gray-800 rounded-2xl shadow hover:shadow-md border border-[#10b981]/20 transition text-center">
      <div class="text-[#10b981] font-semibold">Electricity</div>
    </a>
  </div>

  {{-- Referral --}}
  <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl shadow ring-1 ring-[#10b981]/20">
    <p class="text-gray-600 dark:text-gray-400 mb-2">Invite friends & earn</p>
    <div class="flex items-center gap-2">
      <input type="text" readonly value="{{ route('register', ['ref' => auth()->user()->username]) }}" class="flex-1 p-2 border rounded-lg bg-gray-50 dark:bg-gray-700 border-[#10b981]/30">
      <button 
        @click="navigator.clipboard.writeText('{{ route('register', ['ref' => auth()->user()->username]) }}')" 
        class="px-3 py-1 bg-[#10b981] hover:bg-[#0ea972] text-white rounded-xl transition">
        Copy
      </button>
    </div>
  </div>

  {{-- Community CTA --}}
  <div class="p-4 bg-[#10b981] text-white rounded-2xl text-center shadow">
    <h3 class="font-semibold mb-2">Join Our Community</h3>
    <a href="https://t.me/oresamsub" target="_blank" class="px-3 py-1 bg-white text-[#10b981] font-semibold rounded-xl hover:bg-gray-100 transition">
      Join Telegram
    </a>
  </div>

  {{-- Transactions --}}
  <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl shadow ring-1 ring-[#10b981]/20">
    <h3 class="font-semibold text-gray-700 dark:text-white mb-3">Recent Transactions</h3>
    <div class="overflow-y-auto max-h-64 scrollbar-thin scrollbar-thumb-[#10b981]/60 scrollbar-track-gray-200 dark:scrollbar-track-gray-700">
      <table class="w-full text-sm">
        <thead>
          <tr class="text-left text-gray-500 dark:text-gray-400">
            <th class="py-2">Type</th>
            <th class="py-2">Amount</th>
            <th class="py-2">Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($transactions as $txn)
            <tr class="border-t border-gray-200 dark:border-gray-700">
              <td class="py-2">{{ $txn->type }}</td>
              <td class="py-2">₦{{ number_format($txn->amount, 2) }}</td>
              <td class="py-2">
                <span class="px-2 py-1 rounded-xl text-xs font-semibold
                  {{ $txn->status == 'success' ? 'bg-[#10b981]/20 text-[#10b981]' : 'bg-red-100 text-red-600' }}">
                  {{ ucfirst($txn->status) }}
                </span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="py-4 text-center text-gray-500">No transactions yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

@endsection
