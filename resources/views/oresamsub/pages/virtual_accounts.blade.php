@extends('oresamsub.layouts.app')

@section('content')
<div class="pt-6 max-w-sm mx-auto">

  <!-- Back to Home Navigation -->
  <div class="mb-4 flex items-center gap-2">
    <a href="{{ route('ore.dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm flex items-center">
      ← Back to Dashboard
    </a>
  </div>

  <h2 class="text-xl font-bold text-center mb-6">My Virtual Accounts</h2>

  <div class="space-y-4">
    @foreach ([
      ['bank' => 'Wema Bank', 'account_name' => 'Samuel Adebunmi', 'account_number' => '1234567890'],
      ['bank' => 'Providus Bank', 'account_name' => 'Samuel Adebunmi', 'account_number' => '0912345678'],
      ['bank' => 'Titan Bank', 'account_name' => 'Samuel Adebunmi', 'account_number' => '3456789012'],
    ] as $account)
      <div x-data="{ copied: false }" class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow space-y-1">
        <div class="flex items-center justify-between">
          <div class="font-semibold text-blue-600 dark:text-blue-400">{{ $account['bank'] }}</div>
          <span x-show="copied" x-transition class="text-xs text-green-500">Copied ✅</span>
        </div>
        <div class="text-sm text-gray-700 dark:text-gray-300">Acct Name: {{ $account['account_name'] }}</div>
        <div class="flex justify-between items-center mt-1">
          <div class="text-lg font-mono tracking-wide">{{ $account['account_number'] }}</div>
          <button
            @click="navigator.clipboard.writeText('{{ $account['account_number'] }}'); copied = true; setTimeout(() => copied = false, 2000)"
            class="text-sm text-blue-500 hover:underline"
          >Copy</button>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
