@extends('oresamsub.layouts.app')

@section('content')

<div class="space-y-6 pt-2" x-data="{ isWalletLoading: false, isRefreshing: false }">


 

  <div class="">
    <a href="{{route('admin.exit_impersonate')}}">
            @if (session()->has('impersonator'))
               <div class="bg-green-800 text-white p-2 rounded-xl">
                <h1>You are now viewing <u>{{ auth()->user()->first_name }} {{ auth()->user()->pin }}</u> as an Administrator.</h1>
                <div class="text-lg"><b>Click to EXIT User Account</b></div>
                </div>

            @endif
    </a>
  </div>


<!-- Alpine (only include if not already loaded in your layout) -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<!-- Font Awesome Free CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<div x-data="{ copied: false }" class="flex flex-col space-y-2 px-3 mt-1">
    
    <!-- Header: Hi Username + Refresh -->
   <!-- Header: Hi Username + Refresh -->
    <div class="flex items-center justify-between">
      <h1 class="text-base font-semibold text-gray-800 dark:text-gray-100">
          Hi, {{ auth()->user()->username }}
      </h1>

      <a
          href="{{ url()->current() }}"
          @click.prevent="showLoader = true; setTimeout(() => window.location.href = '{{ url()->current() }}', 150)"
          class="group p-2 bg-white dark:bg-gray-900 rounded-xl 
                ring-2 ring-green-200 dark:ring-green-700 
                shadow-lg hover:shadow-2xl 
                transition transform hover:scale-[1.02] flex items-center space-x-2"
          title="Refresh page"
      >
          <div class="w-5 h-5 mx-auto rounded-full 
                      bg-gradient-to-r from-emerald-500 to-green-500 
                      flex items-center justify-center 
                      text-white text-sm shadow-sm 
                      group-hover:scale-110 transition duration-200 ease-in-out">
              <i class="fas fa-sync-alt"></i>
          </div>
          <div class="mt-1 text-xs font-medium text-gray-800 dark:text-gray-100 group-hover:text-green-600">
              Refresh
          </div>
      </a>
    </div>



    {{-- <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Refer & Earn</h2> --}}

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-3 border border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
            Buy airtime, data, and pay bills at affordable rates — get started now! 🚀
        </p>

        <!-- Referral Link with Copy Button -->
        <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-md overflow-hidden">
            <input 
                x-ref="refInput"
                type="text" 
                readonly 
                value="{{ url('/register?ref=' . auth()->user()->phone_number) }}"
                class="flex-grow px-2 py-1 text-sm bg-transparent border-none focus:outline-none text-gray-700 dark:text-gray-200"
            >
            <button 
                @click="navigator.clipboard.writeText($refs.refInput.value).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                class="px-2 py-1 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-medium flex items-center justify-center"
                title="Copy link"
                type="button"
            >
                <i :class="copied ? 'fas fa-check' : 'fas fa-copy'"></i>
            </button>
        </div>

        <!-- Link Copied Notice -->
        <span x-show="copied" x-transition x-cloak class="text-xs text-emerald-500 mt-1 block">
            ✅ Link copied!
        </span>

        <!-- Share Buttons -->
        <div class="flex space-x-2 mt-3">
            <a href="https://wa.me/?text={{ urlencode('Buy airtime, data and pay bills at affordable rates - get started now! ' . url('/register?ref=' . auth()->user()->phone_number)) }}"
               target="_blank" 
               class="flex items-center justify-center w-8 h-8 bg-green-500 hover:bg-green-600 rounded-full text-white"
               title="Share on WhatsApp">
               <i class="fab fa-whatsapp"></i>
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('/register?ref=' . auth()->user()->phone_number)) }}&quote={{ urlencode('Buy airtime, data and pay bills at affordable rates - get started now!') }}" 
               target="_blank" 
               class="flex items-center justify-center w-8 h-8 bg-blue-600 hover:bg-blue-700 rounded-full text-white"
               title="Share on Facebook">
               <i class="fab fa-facebook-f"></i>
            </a>
            <a href="https://www.instagram.com/?url={{ urlencode(url('/register?ref=' . auth()->user()->phone_number)) }}" 
               target="_blank" 
               class="flex items-center justify-center w-8 h-8 bg-pink-500 hover:bg-pink-600 rounded-full text-white"
               title="Share on Instagram">
               <i class="fab fa-instagram"></i>
            </a>
            <a href="https://www.tiktok.com/share?url={{ urlencode(url('/register?ref=' . auth()->user()->phone_number)) }}" 
               target="_blank" 
               class="flex items-center justify-center w-8 h-8 bg-black hover:bg-gray-800 rounded-full text-white"
               title="Share on TikTok">
               <i class="fab fa-tiktok"></i>
            </a>
        </div>
    </div>
</div>


<!-- 🚀 Join the Community Section -->
<div class="mb-4">
  <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white p-5 rounded-2xl shadow-lg flex flex-col md:flex-row items-center justify-between gap-4">
    
    <!-- Text -->
    <div>
      <h2 class="text-lg md:text-xl font-bold">🔥 Join Our Community</h2>
      <p class="text-sm text-white/90 mt-1">
        Get <span class="font-semibold">real-time updates</span>, promos & special alerts directly in our WhatsApp community.  
      </p>
    </div>

    <!-- CTA Button -->
    <div class="flex justify-center">
      @if(auth()->user()->customer_category === 'pos')
          <a href="https://chat.whatsapp.com/GoIik4DCz0k1cH3zyEtFrk?mode=ac_t" 
             target="_blank"
             class="flex items-center gap-2 px-5 py-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-xl shadow-lg transition transform hover:scale-105">
              <!-- WhatsApp Icon -->
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.477 2 2 6.263 2 11.657c0 1.877.56 3.668 1.52 5.178L2 22l5.332-1.414a10.145 10.145 0 004.668 1.071c5.523 0 10-4.263 10-9.657S17.523 2 12 2zm0 17.486c-1.465 0-2.902-.392-4.156-1.14l-.297-.176-3.164.84.847-3.088-.194-.316A7.784 7.784 0 014.22 11.66c0-4.085 3.48-7.406 7.78-7.406s7.78 3.321 7.78 7.406-3.48 7.406-7.78 7.406zm4.332-5.527c-.237-.118-1.405-.691-1.622-.769-.217-.079-.376-.118-.535.118-.158.237-.613.769-.751.927-.138.158-.277.178-.514.059-.237-.118-1.002-.366-1.907-1.168-.705-.63-1.182-1.408-1.32-1.645-.138-.237-.014-.365.104-.483.107-.106.237-.277.356-.415.118-.138.158-.237.237-.395.079-.158.04-.296-.02-.415-.059-.118-.534-1.284-.732-1.759-.192-.46-.387-.398-.535-.405-.138-.007-.296-.009-.455-.009s-.415.059-.633.296c-.217.237-.831.812-.831 1.979s.851 2.297.97 2.454c.118.158 1.676 2.56 4.059 3.588.568.245 1.01.392 1.354.502.568.18 1.085.154 1.493.093.455-.068 1.405-.574 1.603-1.128.197-.554.197-1.028.138-1.128-.059-.099-.217-.158-.455-.277z"/>
              </svg>
              Join Reseller Community
          </a>
      @else
          <a href="https://chat.whatsapp.com/DnFkmQ9cCYF0DomvyThHLq" 
             target="_blank"
             class="flex items-center gap-2 px-5 py-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-xl shadow-lg transition transform hover:scale-105">
              <!-- WhatsApp Icon -->
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.477 2 2 6.263 2 11.657c0 1.877.56 3.668 1.52 5.178L2 22l5.332-1.414a10.145 10.145 0 004.668 1.071c5.523 0 10-4.263 10-9.657S17.523 2 12 2zm0 17.486c-1.465 0-2.902-.392-4.156-1.14l-.297-.176-3.164.84.847-3.088-.194-.316A7.784 7.784 0 014.22 11.66c0-4.085 3.48-7.406 7.78-7.406s7.78 3.321 7.78 7.406-3.48 7.406-7.78 7.406zm4.332-5.527c-.237-.118-1.405-.691-1.622-.769-.217-.079-.376-.118-.535.118-.158.237-.613.769-.751.927-.138.158-.277.178-.514.059-.237-.118-1.002-.366-1.907-1.168-.705-.63-1.182-1.408-1.32-1.645-.138-.237-.014-.365.104-.483.107-.106.237-.277.356-.415.118-.138.158-.237.237-.395.079-.158.04-.296-.02-.415-.059-.118-.534-1.284-.732-1.759-.192-.46-.387-.398-.535-.405-.138-.007-.296-.009-.455-.009s-.415.059-.633.296c-.217.237-.831.812-.831 1.979s.851 2.297.97 2.454c.118.158 1.676 2.56 4.059 3.588.568.245 1.01.392 1.354.502.568.18 1.085.154 1.493.093.455-.068 1.405-.574 1.603-1.128.197-.554.197-1.028.138-1.128-.059-.099-.217-.158-.455-.277z"/>
              </svg>
              Join Community
          </a>
      @endif
    </div>
    
  </div>
</div>

  <div class="relative" x-data="{ isWalletLoading: false, showBalance: false }">
    <div class="bg-emerald-600 dark:bg-emerald-700 text-white p-4 rounded-xl shadow space-y-2">
      <div class="flex justify-between items-center">
        <div>
          <p class="text-xs text-white/80">Wallet Balance</p>
          <p class="text-2xl font-semibold mt-1 flex items-center space-x-2" x-show="!isWalletLoading" x-cloak>
            <!-- Hidden by default -->
            <span x-show="showBalance" x-cloak>₦{{ number_format(auth()->user()->main_wallet, 2) }}</span>
            <span x-show="!showBalance" x-cloak class="tracking-widest">•••••••</span>
  
            <!-- Toggle Button -->
            <button
              @click="showBalance = !showBalance"
              class="ml-2 text-white hover:text-white/80 transition"
              title="Toggle Balance"
            >
              <!-- Eye icon (show balance) -->
              <svg x-show="!showBalance" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
  
              <!-- Eye-off icon (hide balance) -->
              <svg x-show="showBalance" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.269-2.943-9.543-7a10.06 10.06 0 013.232-4.568M6.223 6.223A10.05 10.05 0 0112 5c4.478 0 8.269 2.943 9.543 7a10.06 10.06 0 01-4.676 5.316M15 12a3 3 0 00-3-3M3 3l18 18" />
              </svg>
            </button>
          </p>
        </div>
      </div>
  
      <!-- Top Up -->
      <div class="text-right">
        <a href="{{ route('ore.virtual_accounts') }}"
          @click.prevent="showLoader = true; setTimeout(() => window.location.href = '{{ route('ore.virtual_accounts') }}', 1000)"
          class="text-xs font-bold underline text-white/90 hover:text-white transition">
          + Top Up Wallet
        </a>
      </div>
    </div>
  </div>
  

<!-- Action Buttons -->
<!-- ACTION BUTTONS -->
<div class="grid grid-cols-3 md:grid-cols-3 gap-4 text-sm text-center">
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
  <form method="POST" action="{{ route('logout') }}"
      x-data="{ isLoggingOut: false }"
      @submit.prevent="isLoggingOut = true; $el.submit()"
      class="p-5 bg-white dark:bg-gray-900 rounded-2xl ring-2 ring-red-200 dark:ring-red-800 shadow-xl hover:shadow-2xl transition transform hover:scale-[1.02] cursor-pointer">
  @csrf
  <button type="submit" class="w-full h-full text-center">
    <div class="w-12 h-12 mx-auto rounded-full bg-gradient-to-r from-red-500 to-rose-500 text-white text-2xl flex items-center justify-center shadow-sm transition duration-200 ease-in-out"
         :class="{ 'animate-pulse opacity-70 scale-90': isLoggingOut }">
      <template x-if="!isLoggingOut">
        <span>🚪</span>
      </template>
      <template x-if="isLoggingOut">
        <svg class="h-6 w-6 animate-spin" viewBox="0 0 24 24" fill="none">
          <circle class="opacity-25" cx="12" cy="12" r="10"
                  stroke="currentColor" stroke-width="4" />
          <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8z"/>
        </svg>
      </template>
    </div>
    <div class="mt-3 font-semibold text-red-600 dark:text-red-400" x-text="isLoggingOut ? 'Logging out...' : 'Logout'"></div>
  </button>
  </form>

</div>



  
  

  <!-- Transactions Table (Scrollable) -->
  <div class="bg-white dark:bg-gray-800 mt-6 rounded-xl shadow overflow-hidden">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-700 dark:text-gray-200">
      Recent Transactions
    </div>
    {{-- <div class="max-h-[400px] overflow-y-auto divide-y divide-gray-200 dark:divide-gray-700 text-sm"> --}}
    <div class="relative max-h-[400px] overflow-y-auto divide-y divide-gray-200 dark:divide-gray-700 text-sm scrollbar-thin scrollbar-thumb-emerald-500 scrollbar-track-transparent">

      @foreach ($transactions as $key => $transaction)
      @php
        $types = ['Data','Airtime', 'Electricity', 'Cable'];
        $type = $types[array_rand($types)];
        $amount = '₦' . number_format(rand(200, 5000), 2);
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
        <!-- Trigger -->
        <div @click="showModal = true" class="px-4 py-3 flex justify-between items-center bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 rounded transition">
          <div>
            <div class="font-semibold text-xs text-gray-800 dark:text-gray-100">{{ strtoupper($transaction->transaction_category) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $time }}</div>
          </div>
          <div class="text-right">
            <div class="font-bold {{ $status['color'] }}">₦{{ number_format($transaction->discounted_amount ?? $transaction->amount)  }}</div>
            <div class="text-xs {{ $status['color2'] }}">{{ $status['text'] }}</div>
          </div>
        </div>
    
        <!-- Modal -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
          <div @click.away="showModal = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-sm w-full p-6">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Transaction Details</h2>
    
            <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
              <div class="flex justify-between">
                <span>Plan:</span>
                <span class="font-semibold">{{ $transaction->product_plan->product_plan_name }}</span>
              </div>
              <div class="flex justify-between">
                <span>Phone Recharged:</span>
                <span class="font-semibold">{{ $transaction->phone_number }}</span>
              </div>
              <div class="flex justify-between">
                <span>Discounted Amount:</span>
                <span class="font-semibold">₦{{ number_format($transaction->discounted_amount ?? $transaction->amount)  }}</span>
              </div>
          
              <div class="flex justify-between">
                <span>Amount:</span>
                <span class="font-semibold">₦{{  number_format($transaction->amount)  }}</span>
              </div>
          

              <div class="flex justify-between">
                <span>Status:</span>
                <span class="{{ $status['color2'] }}">{{ $status['text'] }}</span>
              </div>
              <div class="flex justify-between">
                <span>Date:</span>
                <span>{{ $time }}</span>
              </div>
              <div class="flex justify-between">
                <span>Category:</span>
                <span>{{ strtoupper($transaction->transaction_category) }}</span>
              </div>
            </div>
    
            <div class="mt-6 text-center">
              <button @click="showModal = false"
              class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 text-sm">
              Close
              </button>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    
        <!-- Scroll hint -->
      <div class="sticky bottom-0 text-center text-[11px] text-gray-400 dark:text-gray-500 bg-white dark:bg-gray-800 py-1 border-t border-gray-200 dark:border-gray-700">
        Scroll to view more ⬇️
      </div>
    </div>


  </div>

</div>
@endsection
