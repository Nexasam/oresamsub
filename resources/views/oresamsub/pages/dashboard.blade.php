@extends('oresamsub.layouts.app')

@section('content')
<div class="space-y-6 pt-2" x-data="{ showLoader: false }">

  {{-- 🟢 Admin Impersonation Notice (topmost) --}}
  @if (session()->has('impersonator'))
    <a href="{{ route('admin.exit_impersonate') }}">
      <div class="bg-green-800 text-white p-2 rounded-xl">
        <h1>
          You are now viewing <u>{{ auth()->user()->first_name }} {{ auth()->user()->pin }}</u> as an Administrator.
        </h1>
        <div class="text-lg"><b>Click to EXIT User Account</b></div>
      </div>
    </a>
  @endif

  {{-- Header: Hi Username + Refresh --}}
  <div class="flex items-center justify-between px-3">
    <h1 class="text-base font-semibold text-gray-800 dark:text-gray-100">
      Hi, {{ auth()->user()->username }}
    </h1>
    <a
      href="{{ url()->current() }}"
      @click.prevent="showLoader = true; setTimeout(() => window.location.href = '{{ url()->current() }}', 150)"
      class="group p-2 bg-white dark:bg-gray-900 rounded-xl ring-2 ring-green-200 dark:ring-green-700 shadow-lg hover:shadow-2xl transition transform hover:scale-[1.02] flex items-center space-x-2"
      title="Refresh page"
    >
      <div class="w-5 h-5 mx-auto rounded-full bg-gradient-to-r from-emerald-500 to-green-500 flex items-center justify-center text-white text-sm shadow-sm group-hover:scale-110 transition duration-200 ease-in-out">
        <i class="fas fa-sync-alt"></i>
      </div>
      <div class="mt-1 text-xs font-medium text-gray-800 dark:text-gray-100 group-hover:text-green-600">
        Refresh
      </div>
    </a>
  </div>

  {{-- 💰 Wallet Balance (primary) --}}
  <div class="relative px-3" x-data="{ isWalletLoading: false, showBalance: false }">
    <div class="bg-emerald-600 dark:bg-emerald-700 text-white p-4 rounded-xl shadow space-y-2">
      <div class="flex justify-between items-center">
        <div>
          <p class="text-xs text-white/80">Wallet Balance</p>
          <p class="text-2xl font-semibold mt-1 flex items-center space-x-2" x-show="!isWalletLoading" x-cloak>
            <span x-show="showBalance" x-cloak>₦{{ number_format(auth()->user()->main_wallet, 2) }}</span>
            <span x-show="!showBalance" x-cloak class="tracking-widest">•••••••</span>
            <button
              @click="showBalance = !showBalance"
              class="ml-2 text-white hover:text-white/80 transition"
              title="Toggle Balance"
            >
              {{-- Eye icon (show) --}}
              <svg x-show="!showBalance" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              {{-- Eye-off icon (hide) --}}
              <svg x-show="showBalance" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.269-2.943-9.543-7a10.06 10.06 0 013.232-4.568M6.223 6.223A10.05 10.05 0 0112 5c4.478 0 8.269 2.943 9.543 7a10.06 10.06 0 01-4.676 5.316M15 12a3 3 0 00-3-3M3 3l18 18" />
              </svg>
            </button>
          </p>
        </div>
      </div>
      <div class="text-right">
        <a href="{{ route('ore.virtual_accounts') }}"
           @click.prevent="showLoader = true; setTimeout(() => window.location.href = '{{ route('ore.virtual_accounts') }}', 1000)"
           class="text-xs font-bold underline text-white/90 hover:text-white transition">
          + Top Up Wallet
        </a>
      </div>
    </div>
  </div>

  {{-- ⚡ Quick Actions (primary tasks) --}}
  <div class="grid grid-cols-3 md:grid-cols-3 gap-4 text-sm text-center px-3">
    @foreach ([
      ['label' => 'Buy Airtime', 'icon' => '📞', 'route' => 'ore.airtime'],
      ['label' => 'Buy Data', 'icon' => '📶', 'route' => 'ore.data'],
      ['label' => 'Electricity', 'icon' => '⚡', 'route' => 'ore.electricity'],
      ['label' => 'Subscribe Cable', 'icon' => '📺', 'route' => 'ore.cable'],
    ] as $item)
      <a
        href="{{ route($item['route']) }}"
        @click.prevent="showLoader = true; setTimeout(() => window.location.href = '{{ route($item['route']) }}', 150)"
        class="group p-5 bg-white dark:bg-gray-900 rounded-2xl ring-2 ring-green-200 dark:ring-green-700 shadow-xl hover:shadow-2xl transition transform hover:scale-[1.02]"
      >
        <div class="w-12 h-12 mx-auto rounded-full bg-gradient-to-r from-emerald-500 to-green-500 flex items-center justify-center text-white text-2xl shadow-sm group-hover:scale-110 transition duration-200 ease-in-out">
          {{ $item['icon'] }}
        </div>
        <div class="mt-3 font-semibold text-gray-800 dark:text-gray-100 group-hover:text-green-600">{{ $item['label'] }}</div>
      </a>
    @endforeach

    {{-- 🚪 Logout --}}
    <form method="POST" action="{{ route('logout') }}"
          x-data="{ isLoggingOut: false }"
          @submit.prevent="isLoggingOut = true; $el.submit()"
          class="p-5 bg-white dark:bg-gray-900 rounded-2xl ring-2 ring-red-200 dark:ring-red-800 shadow-xl hover:shadow-2xl transition transform hover:scale-[1.02] cursor-pointer">
      @csrf
      <button type="submit" class="w-full h-full text-center">
        <div class="w-12 h-12 mx-auto rounded-full bg-gradient-to-r from-red-500 to-rose-500 text-white text-2xl flex items-center justify-center shadow-sm transition duration-200 ease-in-out"
             :class="{ 'animate-pulse opacity-70 scale-90': isLoggingOut }">
          <template x-if="!isLoggingOut"><span>🚪</span></template>
          <template x-if="isLoggingOut">
            <svg class="h-6 w-6 animate-spin" viewBox="0 0 24 24" fill="none">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8z"/>
            </svg>
          </template>
        </div>
        <div class="mt-3 font-semibold text-red-600 dark:text-red-400" x-text="isLoggingOut ? 'Logging out...' : 'Logout'"></div>
      </button>
    </form>
  </div>

  {{-- 🎁 Referral & Earn (secondary) --}}
  <div x-data="{ copied: false }" class="px-3">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-3 border border-gray-200 dark:border-gray-700">
      <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
        Buy airtime, data, and pay bills at affordable rates — get started now! 🚀
      </p>
      <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-md overflow-hidden">
        <input x-ref="refInput" type="text" readonly value="{{ url('/register?ref=' . auth()->user()->phone_number) }}" class="flex-grow px-2 py-1 text-sm bg-transparent border-none focus:outline-none text-gray-700 dark:text-gray-200">
        <button @click="navigator.clipboard.writeText($refs.refInput.value).then(() => { copied = true; setTimeout(() => copied = false, 2000) })" class="px-2 py-1 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-medium flex items-center justify-center" type="button" title="Copy link">
          <i :class="copied ? 'fas fa-check' : 'fas fa-copy'"></i>
        </button>
      </div>
      <span x-show="copied" x-transition x-cloak class="text-xs text-emerald-500 mt-1 block">✅ Link copied!</span>
      <div class="flex space-x-2 mt-3">
        {{-- Socials --}}
        <a href="https://wa.me/?text={{ urlencode('Buy airtime, data and pay bills at affordable rates - get started now! ' . url('/register?ref=' . auth()->user()->phone_number)) }}" target="_blank" class="flex items-center justify-center w-8 h-8 bg-green-500 hover:bg-green-600 rounded-full text-white" title="Share on WhatsApp">
          <i class="fab fa-whatsapp"></i>
        </a>
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('/register?ref=' . auth()->user()->phone_number)) }}&quote={{ urlencode('Buy airtime, data and pay bills at affordable rates - get started now!') }}" target="_blank" class="flex items-center justify-center w-8 h-8 bg-blue-600 hover:bg-blue-700 rounded-full text-white" title="Share on Facebook">
          <i class="fab fa-facebook-f"></i>
        </a>
        <a href="https://www.instagram.com/?url={{ urlencode(url('/register?ref=' . auth()->user()->phone_number)) }}" target="_blank" class="flex items-center justify-center w-8 h-8 bg-pink-500 hover:bg-pink-600 rounded-full text-white" title="Share on Instagram">
          <i class="fab fa-instagram"></i>
        </a>
        <a href="https://www.tiktok.com/share?url={{ urlencode(url('/register?ref=' . auth()->user()->phone_number)) }}" target="_blank" class="flex items-center justify-center w-8 h-8 bg-black hover:bg-gray-800 rounded-full text-white" title="Share on TikTok">
          <i class="fab fa-tiktok"></i>
        </a>
      </div>
    </div>
  </div>

  {{-- 🚀 Community Join (secondary) --}}
  <div class="px-3">
    @if(auth()->user()->customer_category === 'pos')
      <a href="https://chat.whatsapp.com/GoIik4DCz0k1cH3zyEtFrk?mode=ac_t" target="_blank" class="block bg-gradient-to-r from-green-500 via-green-600 to-green-700 text-white p-6 rounded-2xl shadow-lg transition transform hover:scale-[1.02] hover:shadow-xl">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
          <div>
            <h2 class="text-lg font-bold flex items-center gap-2">🔥 Join Reseller Community</h2>
            <p class="text-sm text-white/90 mt-1">Get <span class="font-semibold">real-time updates</span>, promos & alerts directly in our WhatsApp group.</p>
          </div>
          <div class="flex-shrink-0"><i class="fab fa-whatsapp text-3xl"></i></div>
        </div>
      </a>
    @else
      <a href="https://chat.whatsapp.com/DnFkmQ9cCYF0DomvyThHLq" target="_blank" class="block bg-gradient-to-r from-green-500 via-green-600 to-green-700 text-white p-6 rounded-2xl shadow-lg transition transform hover:scale-[1.02] hover:shadow-xl">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
          <div>
            <h2 class="text-lg font-bold flex items-center gap-2">🔥 Join Our Community</h2>
            <p class="text-sm text-white/90 mt-1">Get <span class="font-semibold">real-time updates</span>, promos & alerts directly in our WhatsApp group.</p>
          </div>
          <div class="flex-shrink-0"><i class="fab fa-whatsapp text-3xl"></i></div>
        </div>
      </a>
    @endif
  </div>

  {{-- 📜 Transactions (history last) --}}
  <div class="bg-white dark:bg-gray-800 mt-6 rounded-xl shadow overflow-hidden mx-3">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-700 dark:text-gray-200">Recent Transactions</div>
    <div class="relative max-h-[400px] overflow-y-auto divide-y divide-gray-200 dark:divide-gray-700 text-sm scrollbar-thin scrollbar-thumb-emerald-500 scrollbar-track-transparent">
      @foreach ($transactions as $key => $transaction)
        @php
          $time = Carbon\Carbon::parse($transaction->created_at)->subMinutes(($key+1) * 10)->format('M j, g:i A');
          $status = match($transaction->status) {
            '1' => ['text' => 'Success', 'color' => 'text-green-500', 'color2' => 'text-green-600'],
            '0' => ['text' => 'Pending', 'color' => 'text-yellow-500', 'color2' => 'text-yellow-600'],
            '-1' => ['text' => 'Unsuccessful', 'color' => 'text-red-500', 'color2' => 'text-red-600'],
            '2' => ['text' => 'Refunded', 'color' => 'text-blue-500', 'color2' => 'text-blue-600'],
            default => ['text' => 'Unknown', 'color' => 'text-gray-500', 'color2' => 'text-gray-600'],
          };
        @endphp

        <div x-data="{ showModal: false }" class="relative">
          {{-- Row trigger --}}
          <div @click="showModal = true" class="px-4 py-3 flex justify-between items-center bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 rounded transition">
            <div>
              <div class="font-semibold text-xs text-gray-800 dark:text-gray-100">{{ strtoupper($transaction->transaction_category) }}</div>
              <div class="text-xs text-gray-500 dark:text-gray-400">{{ $time }}</div>
            </div>
            <div class="text-right">
              <div class="font-bold {{ $status['color'] }}">₦{{ number_format($transaction->discounted_amount ?? $transaction->amount) }}</div>
              <div class="text-xs {{ $status['color2'] }}">{{ $status['text'] }}</div>
            </div>
          </div>

          {{-- Modal --}}
          <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div @click.away="showModal = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-sm w-full p-6">
              <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Transaction Details</h2>
              <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                <div class="flex justify-between"><span>Plan:</span><span class="font-semibold">{{ $transaction->product_plan->product_plan_name ?? '—' }}</span></div>
                <div class="flex justify-between"><span>Phone Recharged:</span><span class="font-semibold">{{ $transaction->phone_number }}</span></div>
                <div class="flex justify-between"><span>Discounted Amount:</span><span class="font-semibold">₦{{ number_format($transaction->discounted_amount ?? $transaction->amount) }}</span></div>
                <div class="flex justify-between"><span>Amount:</span><span class="font-semibold">₦{{ number_format($transaction->amount) }}</span></div>
                <div class="flex justify-between"><span>Status:</span><span class="{{ $status['color2'] }}">{{ $status['text'] }}</span></div>
                <div class="flex justify-between"><span>Date:</span><span>{{ $time }}</span></div>
                <div class="flex justify-between"><span>Category:</span><span>{{ strtoupper($transaction->transaction_category) }}</span></div>
              </div>
              <div class="mt-6 text-center">
                <button @click="showModal = false" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 text-sm">Close</button>
              </div>
            </div>
          </div>
        </div>
      @endforeach

      {{-- Scroll hint --}}
      <div class="sticky bottom-0 text-center text-[11px] text-gray-400 dark:text-gray-500 bg-white dark:bg-gray-800 py-1 border-t border-gray-200 dark:border-gray-700">
        Scroll to view more ⬇️
      </div>
    </div>
  </div>

  {{-- Dependencies (only include if not already in layout) --}}
  {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script> --}}
</div>
@endsection
