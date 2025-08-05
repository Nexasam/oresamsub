@extends('oresamsub.layouts.app')

@section('content')
<div class="space-y-6 pt-2" x-data="{ isWalletLoading: false }">

  <!-- Wallet Card -->
  <div class="relative">
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white p-6 rounded-xl shadow space-y-3">
  
      <div class="flex justify-between items-center">
        <div>
          <p class="text-sm">Wallet Balance</p>
          <p class="text-3xl font-bold mt-2" x-show="!isWalletLoading" x-cloak>₦5,000.00</p>
        </div>
        <button
          @click="isWalletLoading = true; setTimeout(() => isWalletLoading = false, 2000)"
          class="text-white hover:text-gray-200 transition"
          title="Refresh Balance">
          🔄
        </button>
      </div>
  
      <!-- Top Up Link -->
      <div class="text-right">
        <a href="{{ route('ore.virtual_accounts') }}"
           class="text-sm underline text-white/90 hover:text-white transition">
          + Top Up Wallet
        </a>
      </div>
    </div>
  
    <!-- Loader Overlay -->
    <div x-show="isWalletLoading" x-cloak class="absolute inset-0 bg-blue-600/70 flex items-center justify-center rounded-xl z-10">
      <div class="animate-spin h-8 w-8 border-4 border-white border-t-transparent rounded-full"></div>
    </div>
  </div>
  

  <!-- Action Buttons -->
  <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-center text-sm">
    @foreach ([
      ['label' => 'Buy Airtime', 'icon' => '📞', 'route' => 'ore.airtime'],
      ['label' => 'Buy Data', 'icon' => '📶', 'route' => 'ore.data'],
      ['label' => 'Electricity', 'icon' => '⚡', 'route' => 'ore.electricity'],
      ['label' => 'Cable Subscription', 'icon' => '📺', 'route' => 'ore.cable'],
    ] as $item)
      <a
        href="{{ route($item['route']) }}"
        @click.prevent="showLoader = true; setTimeout(() => window.location.href = '{{ route($item['route']) }}', 150)"
        class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition"
      >
        <div class="text-2xl">{{ $item['icon'] }}</div>
        <div class="mt-1 font-semibold">{{ $item['label'] }}</div>
      </a>
    @endforeach
  </div>
  

  <!-- Transactions Table (Scrollable) -->
  <div class="bg-white dark:bg-gray-800 mt-6 rounded-xl shadow overflow-hidden">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-700 dark:text-gray-200">
      Recent Transactions
    </div>
    <div class="max-h-[400px] overflow-y-auto divide-y divide-gray-200 dark:divide-gray-700 text-sm">
      @foreach (range(1, 15) as $i)
        @php
          $types = ['Airtime', 'Data', 'Electricity', 'Cable'];
          $type = $types[array_rand($types)];
          $amount = '₦' . number_format(rand(200, 5000), 2);
          $status = rand(0, 1) ? 'Success' : 'Failed';
          $time = now()->subMinutes($i * 10)->format('M j, g:i A');
        @endphp
        <div class="px-4 py-3 flex justify-between items-center">
          <div>
            <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $type }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $time }}</div>
          </div>
          <div class="text-right">
            <div class="font-bold {{ $status === 'Success' ? 'text-green-500' : 'text-red-500' }}">{{ $amount }}</div>
            <div class="text-xs {{ $status === 'Success' ? 'text-green-600' : 'text-red-600' }}">{{ $status }}</div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

</div>
@endsection
