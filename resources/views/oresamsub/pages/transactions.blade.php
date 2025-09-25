@extends('oresamsub.layouts.app')

@section('content')

{{-- @include('partials.announcements')   --}}

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


  <!-- Transactions Table (Scrollable) -->
  <div class="bg-white dark:bg-gray-800 mt-6 rounded-xl shadow overflow-hidden">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-700 dark:text-gray-200">
      Transactions
    </div>
    {{-- <div class="max-h-[400px] overflow-y-auto divide-y divide-gray-200 dark:divide-gray-700 text-sm"> --}}
    <div class="relative max-h-[650px] overflow-y-auto divide-y divide-gray-200 dark:divide-gray-700 text-sm scrollbar-thin scrollbar-thumb-emerald-500 scrollbar-track-transparent">

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
              @if ($transaction->status == 2 && $transaction->refund_reason)
                <div class="flex justify-between">
                  <span>Refund reason:</span>
                  {{-- {{ $transaction->refund_reason }} --}}
                  <span class="font-semibold">Downtime from provider</span>
                </div>
              @endif
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
