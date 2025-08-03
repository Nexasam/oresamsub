@extends('layouts.app')

@section('content')
<div class="space-y-6 pt-2">
  <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white p-6 rounded-xl shadow">
    <p class="text-sm">Wallet Balance</p>
    <p class="text-3xl font-bold mt-2">₦5,000.00</p>
  </div>

  <div class="grid grid-cols-2 gap-4 text-center text-sm">
    <a href="{{ route('airtime') }}" class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition">
      📞<div class="mt-1 font-semibold">Buy Airtime</div>
    </a>
    <a href="{{ route('data') }}" class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition">
      📶<div class="mt-1 font-semibold">Buy Data</div>
    </a>
    <a href="{{ route('electricity') }}" class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition">
      ⚡<div class="mt-1 font-semibold">Electricity</div>
    </a>
    <a href="{{ route('virtual') }}" class="col-span-2 p-4 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition">
      🏦<div class="mt-1 font-semibold">Virtual Account</div>
    </a>
  </div>
</div>
@endsection
