@extends('oresamsub.layouts.app')

@section('content')
<div x-data="{ loading: false }" class="p-4 space-y-6">
    {{-- Admin Impersonation Notice --}}
    @if(auth()->check() && auth()->user()->is_admin_impersonating)
        <div class="bg-green-100 text-green-800 p-3 rounded-lg shadow">
            You are logged in as <strong>{{ auth()->user()->name }}</strong>
            <a href="{{ route('admin.stopImpersonate') }}" class="ml-2 text-red-500 underline">Stop Impersonating</a>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold">Hi, {{ auth()->user()->name }}</h2>
        <button @click="window.location.reload()" class="px-3 py-1 text-sm bg-gray-200 rounded">Refresh</button>
    </div>

    {{-- Wallet Balance --}}
    <div class="bg-emerald-500 text-white p-4 rounded-lg shadow">
        <p class="text-sm">Wallet Balance</p>
        <div class="flex items-center justify-between">
            <span x-data="{ show: false }">
                <span x-show="show">₦{{ number_format(auth()->user()->wallet_balance, 2) }}</span>
                <span x-show="!show">••••••</span>
                <button @click="show = !show" class="ml-2 text-xs underline">Show/Hide</button>
            </span>
            <a href="{{ route('wallet.topup') }}" class="text-xs underline">Top Up</a>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <a href="{{ route('airtime') }}" @click="loading = true" class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow text-center">Airtime</a>
        <a href="{{ route('data') }}" @click="loading = true" class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow text-center">Data</a>
        <a href="{{ route('electricity') }}" @click="loading = true" class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow text-center">Electricity</a>
        <a href="{{ route('cable') }}" @click="loading = true" class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow text-center">Cable</a>
        <a href="{{ route('logout') }}" @click="loading = true" class="bg-red-100 dark:bg-red-900 p-4 rounded-xl shadow text-center">Logout</a>
    </div>

    {{-- Refer & Earn --}}
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow space-y-2">
        <p class="font-medium">Refer & Earn</p>
        <div class="flex items-center justify-between">
            <input type="text" value="{{ route('referral', auth()->user()->username) }}" readonly class="w-full border rounded px-2 py-1 text-sm" id="refLink">
            <button onclick="navigator.clipboard.writeText(document.getElementById('refLink').value)" class="ml-2 text-xs bg-emerald-500 text-white px-2 py-1 rounded">Copy</button>
        </div>
        <div class="flex space-x-3 pt-2">
            <a href="https://wa.me/?text={{ urlencode(route('referral', auth()->user()->username)) }}" target="_blank" class="text-green-600"><i class="fab fa-whatsapp"></i></a>
            <a href="https://t.me/share/url?url={{ urlencode(route('referral', auth()->user()->username)) }}" target="_blank" class="text-blue-500"><i class="fab fa-telegram"></i></a>
            <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('referral', auth()->user()->username)) }}" target="_blank" class="text-sky-500"><i class="fab fa-twitter"></i></a>
        </div>
    </div>

    {{-- Community Join --}}
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
        <p class="font-medium mb-2">Join Our Community</p>
        <a href="https://chat.whatsapp.com/..." target="_blank" class="block text-sm text-emerald-600 underline">WhatsApp Group</a>
    </div>

    {{-- Recent Transactions --}}
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
        <p class="font-medium mb-3">Recent Transactions</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">ID</th>
                        <th class="text-left py-2">Amount</th>
                        <th class="text-left py-2">Status</th>
                        <th class="text-left py-2">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                        <tr class="border-b hover:bg-gray-50">
                            <td>{{ $tx->id }}</td>
                            <td>₦{{ number_format($tx->amount, 2) }}</td>
                            <td>{{ ucfirst($tx->status) }}</td>
                            <td>{{ $tx->created_at->format('d M, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-3">No transactions yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Loading Overlay --}}
    <div x-show="loading" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg flex items-center space-x-3">
            <svg class="animate-spin h-5 w-5 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>Loading...</span>
        </div>
    </div>
</div>
@endsection
