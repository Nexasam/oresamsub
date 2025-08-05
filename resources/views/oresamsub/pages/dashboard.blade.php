@extends('oresamsub.layouts.app')

@section('content')
<div class="space-y-6 pt-2" x-data="{ isWalletLoading: false, isRefreshing: false }">

  <div class="">
    <a href="{{route('admin.exit_impersonate')}}">
            @if (session()->has('impersonator'))
               <div class="bg-green-800 text-white p-2 rounded-xl">
                <h1>You are now impersonating <u>{{ auth()->user()->first_name }} {{ auth()->user()->pin }}</u> as an Administrator.</h1>
                <div class="text-lg"><b>Click to EXIT Impersonation</b></div>
                </div>

            @endif
    </a>
  </div>

  <!-- Logout Button -->
  <div class="flex justify-between items-center px-4" x-data="{ isRefreshing: false }">
    <h1 class="text-lg font-bold text-gray-800 dark:text-white">
      Hi, {{ auth()->user()->username }} 👋
    </h1>


    <!-- Refresh Button -->
    <button
      @click="isRefreshing = true; setTimeout(() => location.reload(), 800)"
      class="relative group inline-flex items-center space-x-2 px-3 py-1.5 bg-gradient-to-r from-emerald-500 to-green-500 text-white rounded-full shadow-md hover:from-emerald-600 hover:to-green-600 transition duration-300"
      title="Refresh Dashboard"
    >
      <div class="text-sm font-semibold">
        <template x-if="!isRefreshing">
          <span class="inline-flex items-center space-x-1">
            <svg class="w-4 h-4 group-hover:animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-width="2" d="M4 4v6h6M20 20v-6h-6"></path>
              <path stroke="currentColor" stroke-width="2" d="M20 4a9 9 0 0 0-16 5.5M4 20a9 9 0 0 0 16-5.5" />
            </svg>
            <span>Refresh</span>
          </span>
        </template>
        <template x-if="isRefreshing">
          <span class="inline-flex items-center space-x-1">
            <svg class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8v4l4-4-4-4v4a8 8 0 000 16v-4l-4 4 4 4v-4a8 8 0 01-8-8z" />
            </svg>
            <span>Refreshing...</span>
          </span>
        </template>
      </div>
    </button>
  </div>
  
  
  


  
  
  <div class="relative" x-data="{ isWalletLoading: false, showBalance: false }">
    <div class="bg-gradient-to-r from-emerald-500 to-green-500 text-white p-4 rounded-xl shadow space-y-2">
      <div class="flex justify-between items-center">
        <div>
          <p class="text-xs">Wallet Balance</p>
          <p class="text-2xl font-semibold mt-1 flex items-center space-x-2" x-show="!isWalletLoading" x-cloak>
            <!-- Hidden by default -->
            <span x-show="showBalance" x-cloak>₦{{ number_format(auth()->user()->main_wallet, 2) }}</span>
            <span x-show="!showBalance" x-cloak class="tracking-widest">•••••••</span>
  
            <!-- Toggle Button -->
            <button
              @click="showBalance = !showBalance"
              class="ml-2 text-white hover:text-gray-200 transition"
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
  
        <!-- Refresh Wallet -->
        {{-- <button
          @click="isWalletLoading = true; setTimeout(() => isWalletLoading = false, 2000)"
          class="text-white hover:text-gray-200 transition text-xl"
          title="Refresh Balance"
        >
          🔄
        </button> --}}
      </div>
  
      <!-- Top Up -->
      <div class="text-right">
        <a href="{{ route('ore.virtual_accounts') }}"
          @click.prevent="showLoader = true; setTimeout(() => window.location.href = '{{ route('dashboard') }}', 1000)"
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

  <!-- LOGOUT CARD -->
  <form method="POST" action="{{ route('logout') }}"
        class="p-5 bg-white dark:bg-gray-900 rounded-2xl ring-2 ring-red-200 dark:ring-red-800 shadow-xl hover:shadow-2xl transition transform hover:scale-[1.02] cursor-pointer">
    @csrf
    <button type="submit" class="w-full h-full text-center">
      <div class="w-12 h-12 mx-auto rounded-full bg-gradient-to-r from-red-500 to-rose-500 text-white text-2xl flex items-center justify-center shadow-sm hover:scale-110 transition duration-200 ease-in-out">
        🚪
      </div>
      <div class="mt-3 font-semibold text-red-600 dark:text-red-400">Logout</div>
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
        $types = ['Airtime', 'Data', 'Electricity', 'Cable'];
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
