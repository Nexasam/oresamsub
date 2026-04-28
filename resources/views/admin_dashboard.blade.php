@extends('layouts.app')

@section('content')

<div class="main-content bg-gray-50 min-h-screen" x-data="adminDashboard()" x-init="init()">
    
    @php
    $sidebar_color = App\Models\AdminColorSetting::where('color_name','site_admin_sidebar_color')->first(); 
    $sidebar_color = $sidebar_color->color_value ?? '#6B21A8';
    @endphp

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Admin Dashboard 
                </h1>
                <p class="text-gray-600 mt-1">{{ __('messages.Welcome') }}, {{ $user->first_name }} {{ $user->last_name }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <button @click="refreshAll()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center space-x-2">
                    <svg class="w-4 h-4" :class="{'animate-spin': refreshing}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span class="text-sm font-medium">Refresh</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Date Filter Tabs -->
    <div class="mb-6 bg-white rounded-xl shadow-sm p-2 inline-flex space-x-2">
        <a href="?filter=all_time" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $filter=='all_time' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
             All Time
        </a>
        <a href="?filter=today" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $filter=='today' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
             Today
        </a>
        <a href="?filter=yesterday" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $filter=='yesterday' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
             Yesterday
        </a>
        <a href="?filter=this_week" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $filter=='this_week' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
             This Week
        </a>
        <a href="?filter=last_week" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $filter=='last_week' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
             Last Week
        </a>
        <a href="?filter=this_month" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $filter=='this_month' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
             This Month
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        
        <!-- Total Transactions -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-5 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-blue-100 mb-1">Total Transactions</p>
            <h3 class="text-2xl font-bold">{{ number_format($total_transactions_count) }}</h3>
            <p class="text-xs text-blue-200 mt-1">₦{{ number_format($total_transactions_amount, 2) }}</p>
        </div>

        <!-- Wallet Funding -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-5 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-green-100 mb-1">Wallet Funding</p>
            <h3 class="text-2xl font-bold">{{ number_format($wallet_funding_count) }}</h3>
            <p class="text-xs text-green-200 mt-1">₦{{ number_format($wallet_funding_amount, 2) }}</p>
        </div>

        <!-- Successful Txns -->
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-5 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-emerald-100 mb-1">Successful</p>
            <h3 class="text-2xl font-bold">{{ number_format($successful_txns) }}</h3>
            <p class="text-xs text-emerald-200 mt-1">Completed</p>
        </div>

        <!-- Failed Txns -->
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-5 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-red-100 mb-1">Failed</p>
            <h3 class="text-2xl font-bold">{{ number_format($failed_txns) }}</h3>
            <p class="text-xs text-red-200 mt-1">Transactions</p>
        </div>

        <!-- Refunded Txns -->
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-5 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-yellow-100 mb-1">Refunded</p>
            <h3 class="text-2xl font-bold">{{ number_format($refunded_txns) }}</h3>
            <p class="text-xs text-yellow-200 mt-1">Transactions</p>
        </div>

        <!-- Users -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-5 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-purple-100 mb-1">New Users</p>
            <h3 class="text-2xl font-bold">{{ number_format($userss) }}</h3>
            <p class="text-xs text-purple-200 mt-1">Registered</p>
        </div>

    </div>

    <!-- Secondary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        
        <!-- Total Users -->
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Users</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ number_format(count($users ?? [])) }}</h3>
                </div>
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Product Plans -->
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Product Plans</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ number_format(count($product_plans ?? [])) }}</h3>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Your Balance -->
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
            <a href="{{route('wallet_creditings.index')}}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Your Balance</p>
                        <h3 class="text-2xl font-bold text-gray-900">₦{{ number_format(auth()->user()->main_wallet, 2) }}</h3>
                    </div>
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                </div>
            </a>
        </div>

        <!-- Total Balances (AJAX) -->
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm text-gray-600 mb-1">Total User Balances</p>
                    <template x-if="loadingBalances">
                        <div class="h-8 w-32 bg-gray-200 rounded animate-pulse"></div>
                    </template>
                    <template x-if="!loadingBalances">
                        <h3 class="text-2xl font-bold text-gray-900">₦<span x-text="totalBalances"></span></h3>
                    </template>
                    <p class="text-xs text-gray-500 mt-1">Updates every 20s</p>
                </div>
                <button @click="fetchTotalBalances()" class="p-3 bg-orange-100 rounded-lg hover:bg-orange-200 transition">
                    <svg class="w-6 h-6 text-orange-600" :class="{'animate-spin': loadingBalances}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
            </div>
        </div>

    </div>


    <!-- Transactions Table (Full Width) -->
    <div class="mb-6">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900">Recent Transactions</h2>
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                            <span x-text="transactionsPagination.total || 0"></span> Total
                        </span>
                    </div>
                </div>

                <!-- Filters -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex flex-wrap gap-3">
                        <div class="flex-1 min-w-[200px]">
                            <input 
                                type="text" 
                                x-model="transactionsFilters.search" 
                                @input.debounce.500ms="fetchTransactions()"
                                placeholder="🔍 Search transactions..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                            >
                        </div>
                        <select 
                            x-model="transactionsFilters.status" 
                            @change="fetchTransactions()"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                        >
                            <option value="">All Status</option>
                            <option value="1">✓ Success</option>
                            <option value="0">⏳ Pending</option>
                            <option value="-1">✗ Failed</option>
                            <option value="2">↩ Refunded</option>
                        </select>
                        <select 
                            x-model="transactionsFilters.per_page" 
                            @change="fetchTransactions()"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                        >
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <button 
                            @click="fetchTransactions()"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium"
                            :disabled="loadingTransactions"
                        >
                            <svg class="w-4 h-4 inline" :class="{'animate-spin': loadingTransactions}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Loading -->
                <div x-show="loadingTransactions" class="px-6 py-12 text-center">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                    <p class="mt-4 text-gray-600">Loading transactions...</p>
                </div>

                <!-- Table -->
                <div x-show="!loadingTransactions" class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-0.5 py-2 text-left text-[10px] font-semibold text-gray-700 uppercase">ID</th>
                                <th class="px-1 py-2 text-left text-[10px] font-semibold text-gray-700 uppercase">User</th>
                                <th class="px-1 py-2 text-left text-[10px] font-semibold text-gray-700 uppercase">Wallet</th>
                                <th class="px-0.5 py-2 text-left text-[10px] font-semibold text-gray-700 uppercase">Product</th>
                                <th class="px-1 py-2 text-left text-[10px] font-semibold text-gray-700 uppercase">Category</th>
                                <th class="px-0.5 py-2 text-left text-[10px] font-semibold text-gray-700 uppercase">Phone</th>
                                <th class="px-1 py-2 text-right text-[10px] font-semibold text-gray-700 uppercase">Amount</th>
                                <th class="px-1 py-2 text-right text-[10px] font-semibold text-gray-700 uppercase">Discount</th>
                                <th class="px-1 py-2 text-right text-[10px] font-semibold text-gray-700 uppercase">Bal Before</th>
                                <th class="px-1 py-2 text-right text-[10px] font-semibold text-gray-700 uppercase">Bal After</th>
                                <th class="px-0.5 py-2 text-center text-[10px] font-semibold text-gray-700 uppercase">Status</th>
                                <th class="px-1 py-2 text-left text-[10px] font-semibold text-gray-700 uppercase">Date</th>
                                <th class="px-1 py-2 text-center text-[10px] font-semibold text-gray-700 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-if="transactions.length === 0">
                                <tr>
                                    <td colspan="13" class="px-4 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="mt-4 text-gray-600">No transactions found</p>
                                    </td>
                                </tr>
                            </template>
                            <template x-for="(txn, index) in transactions" :key="txn.id || index">
                                <tr class="hover:bg-blue-50 transition">
                                    <td class="px-0.5 py-2 text-xs font-medium text-gray-500" x-text="index + 1 + ((transactionsPagination.current_page - 1) * transactionsFilters.per_page)"></td>
                                    <td class="px-2 py-2">
                                        <div class="text-xs font-medium text-gray-900 truncate max-w-[100px]" x-text="(txn.user?.first_name || '') + ' ' + (txn.user?.last_name || '')"></div>
                                    </td>
                                    <td class="px-2 py-2">
                                        <span class="inline-flex px-1.5 py-0.5 text-[10px] font-semibold rounded bg-purple-100 text-purple-800" x-text="txn.wallet_category == 'main_wallet' ? 'MAIN' : 'DATA'"></span>
                                    </td>
                                    <td class="px-0.5 py-2">
                                        <div class="text-xs text-gray-900 truncate max-w-[120px]" x-text="txn.product_plan?.product_plan_name || 'N/A'"></div>
                                    </td>
                                    <td class="px-2 py-2">
                                        <span class="inline-flex px-1.5 py-0.5 text-[10px] font-semibold rounded bg-blue-100 text-blue-800 whitespace-nowrap" x-text="txn.transaction_category || 'N/A'"></span>
                                    </td>
                                    <td class="px-0.5 py-2 text-xs text-gray-900 whitespace-nowrap" x-text="txn.phone_number || 'N/A'"></td>
                                    <td class="px-2 py-2 text-right text-xs font-bold text-gray-900">
                                        <span x-text="'₦' + parseFloat(txn.amount || 0).toLocaleString('en-NG', {minimumFractionDigits: 0})"></span>
                                    </td>
                                    <td class="px-2 py-2 text-right text-xs text-gray-900">
                                        <span x-text="'₦' + parseFloat(txn.discounted_amount || 0).toLocaleString('en-NG', {minimumFractionDigits: 0})"></span>
                                    </td>
                                    <td class="px-2 py-2 text-right text-xs text-gray-900">
                                        <span x-text="'₦' + parseFloat(txn.balance_before || 0).toLocaleString('en-NG', {minimumFractionDigits: 0})"></span>
                                    </td>
                                    <td class="px-2 py-2 text-right text-xs text-gray-900">
                                        <span x-text="'₦' + parseFloat(txn.balance_after || 0).toLocaleString('en-NG', {minimumFractionDigits: 0})"></span>
                                    </td>
                                    <td class="px-0.5 py-2 text-center">
                                        <span 
                                            class="inline-flex px-1.5 py-0.5 text-[10px] font-semibold rounded-full"
                                            :class="{
                                                'bg-green-100 text-green-800': txn.status == 1 || txn.status == '1',
                                                'bg-yellow-100 text-yellow-800': txn.status == 0 || txn.status == '0',
                                                'bg-red-100 text-red-800': txn.status == -1 || txn.status == '-1',
                                                'bg-blue-100 text-blue-800': txn.status == 2 || txn.status == '2'
                                            }"
                                            x-text="getStatusText(txn.status)"
                                        ></span>
                                    </td>
                                    <td class="px-2 py-2">
                                        <div class="text-xs text-gray-900 whitespace-nowrap" x-text="formatDate(txn.created_at)"></div>
                                    </td>
                                    <td class="px-2 py-2 text-center">
                                        <button 
                                            @click="viewTransactionDetails(txn.id)" 
                                            class="inline-flex items-center px-2 py-1 bg-blue-600 text-white text-[10px] font-medium rounded hover:bg-blue-700 transition"
                                        >
                                            View
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div x-show="!loadingTransactions && transactionsPagination.last_page > 1" class="px-6 py-4 bg-gray-50 border-t">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing <span class="font-semibold" x-text="transactionsPagination.from"></span> to 
                            <span class="font-semibold" x-text="transactionsPagination.to"></span> of 
                            <span class="font-semibold" x-text="transactionsPagination.total"></span>
                        </div>
                        <div class="flex space-x-2">
                            <button 
                                @click="changeTransactionsPage(transactionsPagination.current_page - 1)"
                                :disabled="transactionsPagination.current_page === 1"
                                class="px-3 py-1 border rounded-lg hover:bg-gray-100 disabled:opacity-50 text-sm"
                            >
                                Previous
                            </button>
                            <template x-for="page in getTransactionsPageNumbers()" :key="page">
                                <button 
                                    @click="changeTransactionsPage(page)"
                                    :class="page === transactionsPagination.current_page ? 'bg-blue-600 text-white' : 'bg-white hover:bg-gray-50'"
                                    class="px-3 py-1 border rounded-lg text-sm"
                                    x-text="page"
                                ></button>
                            </template>
                            <button 
                                @click="changeTransactionsPage(transactionsPagination.current_page + 1)"
                                :disabled="transactionsPagination.current_page === transactionsPagination.last_page"
                                class="px-3 py-1 border rounded-lg hover:bg-gray-100 disabled:opacity-50 text-sm"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wallet Creditings Table (Full Width) -->
    <div class="mb-6">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">Wallet Creditings</h2>
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                        <span x-text="fundingPagination.total || 0"></span> Total
                    </span>
                </div>
            </div>

            <!-- Filters -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex flex-wrap gap-3">
                    <div class="flex-1 min-w-[200px]">
                        <input 
                            type="text" 
                            x-model="fundingFilters.search" 
                            @input.debounce.500ms="fetchFunding()"
                            placeholder="🔍 Search by user, reference, bank..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm"
                        >
                    </div>
                    <select 
                        x-model="fundingFilters.per_page" 
                        @change="fetchFunding()"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm"
                    >
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <button 
                        @click="fetchFunding()"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium"
                        :disabled="loadingFunding"
                    >
                        <svg class="w-4 h-4 inline" :class="{'animate-spin': loadingFunding}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Loading -->
            <div x-show="loadingFunding" class="px-6 py-12 text-center">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
                <p class="mt-4 text-gray-600">Loading wallet creditings...</p>
            </div>

            <!-- Table -->
            <div x-show="!loadingFunding" class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-2 py-2 text-left text-[10px] font-semibold text-gray-700 uppercase">ID</th>
                            <th class="px-1 py-2 text-left text-[10px] font-semibold text-gray-700 uppercase">User</th>
                            <th class="px-1 py-2 text-left text-[10px] font-semibold text-gray-700 uppercase">Txn Ref</th>
                            <th class="px-2 py-2 text-center text-[10px] font-semibold text-gray-700 uppercase">Txn Status</th>
                            <th class="px-2 py-2 text-center text-[10px] font-semibold text-gray-700 uppercase">Fund Status</th>
                            <th class="px-2 py-2 text-left text-[10px] font-semibold text-gray-700 uppercase">Bank</th>
                            <th class="px-2 py-2 text-left text-[10px] font-semibold text-gray-700 uppercase">Account Name</th>
                            <th class="px-2 py-2 text-left text-[10px] font-semibold text-gray-700 uppercase">Account No</th>
                            <th class="px-2 py-2 text-right text-[10px] font-semibold text-gray-700 uppercase">Amt Paid</th>
                            <th class="px-2 py-2 text-right text-[10px] font-semibold text-gray-700 uppercase">Amt Settled</th>
                            <th class="px-2 py-2 text-left text-[10px] font-semibold text-gray-700 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-if="funding.length === 0">
                            <tr>
                                <td colspan="11" class="px-4 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    <p class="mt-4 text-gray-600">No wallet creditings found</p>
                                </td>
                            </tr>
                        </template>
                        <template x-for="fund in funding" :key="fund.id">
                            <tr class="hover:bg-green-50 transition">
                                <td class="px-2 py-2 text-xs font-medium text-gray-900" x-text="fund.id"></td>
                                <td class="px-1 py-2">
                                    <div class="text-xs font-medium text-gray-900 truncate max-w-[100px]" x-text="fund.account_name"></div>
                                </td>
                                <td class="px-1 py-2">
                                    <div class="text-xs text-gray-900 font-mono truncate max-w-[100px]" x-text="fund.transaction_reference"></div>
                                </td>
                                <td class="px-2 py-2 text-center">
                                    <span 
                                        class="inline-flex px-1.5 py-0.5 text-[10px] font-semibold rounded-full"
                                        :class="{
                                            'bg-green-100 text-green-800': fund.transaction_status === 'PAID',
                                            'bg-yellow-100 text-yellow-800': fund.transaction_status === 'PENDING',
                                            'bg-red-100 text-red-800': fund.transaction_status === 'FAILED'
                                        }"
                                        x-text="fund.transaction_status || 'N/A'"
                                    ></span>
                                </td>
                                <td class="px-2 py-2 text-center">
                                    <span 
                                        class="inline-flex px-1.5 py-0.5 text-[10px] font-semibold rounded-full"
                                        :class="{
                                            'bg-green-100 text-green-800': fund.funding_status === 'completed',
                                            'bg-yellow-100 text-yellow-800': fund.funding_status === 'pending',
                                            'bg-red-100 text-red-800': fund.funding_status === 'failed'
                                        }"
                                        x-text="fund.funding_status || 'N/A'"
                                    ></span>
                                </td>
                                <td class="px-2 py-2 text-xs text-gray-900 truncate max-w-[80px]" x-text="fund.bank_name"></td>
                                <td class="px-2 py-2 text-xs text-gray-900 truncate max-w-[100px]" x-text="fund.account_name"></td>
                                <td class="px-2 py-2 text-xs font-mono text-gray-900" x-text="fund.account_number"></td>
                                <td class="px-2 py-2 text-right text-xs font-bold text-gray-900">
                                    ₦<span x-text="parseFloat(fund.amount_paid || 0).toLocaleString('en-NG', {minimumFractionDigits: 0})"></span>
                                </td>
                                <td class="px-2 py-2 text-right text-xs font-bold text-green-600">
                                    ₦<span x-text="parseFloat(fund.amount_settled || 0).toLocaleString('en-NG', {minimumFractionDigits: 0})"></span>
                                </td>
                                <td class="px-2 py-2">
                                    <div class="text-xs text-gray-900 whitespace-nowrap" x-text="formatDate(fund.created_at)"></div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div x-show="!loadingFunding && fundingPagination.last_page > 1" class="px-6 py-4 bg-gray-50 border-t">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing <span class="font-semibold" x-text="fundingPagination.from"></span> to 
                        <span class="font-semibold" x-text="fundingPagination.to"></span> of 
                        <span class="font-semibold" x-text="fundingPagination.total"></span>
                    </div>
                    <div class="flex space-x-2">
                        <button 
                            @click="changeFundingPage(fundingPagination.current_page - 1)"
                            :disabled="fundingPagination.current_page === 1"
                            class="px-3 py-1 border rounded-lg hover:bg-gray-100 disabled:opacity-50 text-sm"
                        >
                            Previous
                        </button>
                        <template x-for="page in getFundingPageNumbers()" :key="page">
                            <button 
                                @click="changeFundingPage(page)"
                                :class="page === fundingPagination.current_page ? 'bg-green-600 text-white' : 'bg-white hover:bg-gray-50'"
                                class="px-3 py-1 border rounded-lg text-sm"
                                x-text="page"
                            ></button>
                        </template>
                        <button 
                            @click="changeFundingPage(fundingPagination.current_page + 1)"
                            :disabled="fundingPagination.current_page === fundingPagination.last_page"
                            class="px-3 py-1 border rounded-lg hover:bg-gray-100 disabled:opacity-50 text-sm"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Details Modal -->
    <div x-show="showModal" 
         x-cloak
         @click.self="closeModal()"
         class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4"
         style="display: none;">
        <div @click.stop class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between rounded-t-xl">
                <h3 class="text-xl font-bold text-white">Transaction Details</h3>
                <button @click="closeModal()" class="text-white hover:text-gray-200 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Loading State -->
            <div x-show="loadingModal" class="px-6 py-12 text-center">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                <p class="mt-4 text-gray-600">Loading transaction details...</p>
            </div>

            <!-- Modal Content -->
            <div x-show="!loadingModal && transactionDetails" class="p-6">
                <!-- Transaction Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <!-- Transaction ID -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-600 mb-1">Transaction ID</p>
                        <p class="text-sm font-semibold text-gray-900 font-mono" x-text="transactionDetails?.id"></p>
                    </div>

                    <!-- Status -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-600 mb-1">Status</p>
                        <span 
                            class="inline-flex px-3 py-1 text-xs font-semibold rounded-full"
                            :class="{
                                'bg-green-100 text-green-800': transactionDetails?.status == 1 || transactionDetails?.status == '1',
                                'bg-yellow-100 text-yellow-800': transactionDetails?.status == 0 || transactionDetails?.status == '0',
                                'bg-red-100 text-red-800': transactionDetails?.status == -1 || transactionDetails?.status == '-1',
                                'bg-blue-100 text-blue-800': transactionDetails?.status == 2 || transactionDetails?.status == '2'
                            }"
                            x-text="getStatusText(transactionDetails?.status)"
                        ></span>
                    </div>

                    <!-- User -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-600 mb-1">User</p>
                        <p class="text-sm font-semibold text-gray-900" x-text="(transactionDetails?.user?.first_name || '') + ' ' + (transactionDetails?.user?.last_name || '')"></p>
                        <p class="text-xs text-gray-600" x-text="transactionDetails?.user?.email"></p>
                        <p class="text-xs text-gray-600" x-text="transactionDetails?.user?.phone_number"></p>
                    </div>

                    <!-- Product -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-600 mb-1">Product</p>
                        <p class="text-sm font-semibold text-gray-900" x-text="transactionDetails?.product_plan?.product_plan_name || 'N/A'"></p>
                        <p class="text-xs text-gray-600" x-text="transactionDetails?.transaction_category"></p>
                    </div>

                    <!-- Phone Number -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-600 mb-1">Phone Number</p>
                        <p class="text-sm font-semibold text-gray-900 font-mono" x-text="transactionDetails?.phone_number || 'N/A'"></p>
                    </div>

                    <!-- Wallet -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-600 mb-1">Wallet Type</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded bg-purple-100 text-purple-800" x-text="(transactionDetails?.wallet_category == 'main_wallet' || transactionDetails?.wallet_category == 'MAIN') ? 'MAIN WALLET' : 'DATA WALLET'"></span>
                    </div>

                    <!-- Amount -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                        <p class="text-xs text-green-700 mb-1">Amount</p>
                        <p class="text-lg font-bold text-green-900">₦<span x-text="parseFloat(transactionDetails?.amount || 0).toLocaleString('en-NG', {minimumFractionDigits: 2})"></span></p>
                    </div>

                    <!-- Discount -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-600 mb-1">Discount</p>
                        <p class="text-sm font-semibold text-gray-900">₦<span x-text="parseFloat(transactionDetails?.discounted_amount || 0).toLocaleString('en-NG', {minimumFractionDigits: 2})"></span></p>
                    </div>

                    <!-- Balance Before -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-600 mb-1">Balance Before</p>
                        <p class="text-sm font-semibold text-gray-900">₦<span x-text="parseFloat(transactionDetails?.balance_before || 0).toLocaleString('en-NG', {minimumFractionDigits: 2})"></span></p>
                    </div>

                    <!-- Balance After -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-600 mb-1">Balance After</p>
                        <p class="text-sm font-semibold text-gray-900">₦<span x-text="parseFloat(transactionDetails?.balance_after || 0).toLocaleString('en-NG', {minimumFractionDigits: 2})"></span></p>
                    </div>

                    <!-- Transaction Reference -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-600 mb-1">Transaction Reference</p>
                        <p class="text-sm font-semibold text-gray-900 font-mono" x-text="transactionDetails?.txn_reference || 'N/A'"></p>
                    </div>

                    <!-- Route -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-600 mb-1">Transaction Route</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800" x-text="transactionDetails?.transaction_route || 'N/A'"></span>
                    </div>

                    <!-- Date -->
                    <div class="bg-gray-50 rounded-lg p-4 md:col-span-2">
                        <p class="text-xs text-gray-600 mb-1">Transaction Date</p>
                        <p class="text-sm font-semibold text-gray-900" x-text="formatDate(transactionDetails?.created_at)"></p>
                    </div>
                </div>

                <!-- Messages Section -->
                <div class="space-y-3">
                    <!-- User Message -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4" x-show="transactionDetails?.user_screen_message">
                        <p class="text-xs text-blue-700 font-semibold mb-1">User Message</p>
                        <p class="text-sm text-blue-900" x-text="transactionDetails?.user_screen_message"></p>
                    </div>

                    <!-- Admin Message -->
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4" x-show="transactionDetails?.admin_screen_message">
                        <p class="text-xs text-purple-700 font-semibold mb-1">Admin Message</p>
                        <p class="text-sm text-purple-900" x-text="transactionDetails?.admin_screen_message"></p>
                    </div>

                    <!-- Description -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4" x-show="transactionDetails?.description">
                        <p class="text-xs text-gray-700 font-semibold mb-1">Description</p>
                        <p class="text-sm text-gray-900" x-text="transactionDetails?.description"></p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="sticky bottom-0 bg-gray-50 px-6 py-4 flex justify-end rounded-b-xl border-t">
                <button @click="closeModal()" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-medium">
                    Close
                </button>
            </div>
        </div>
    </div>

</div>


<script>
    function adminDashboard() {
        return {
            // State
            refreshing: false,
            loadingTransactions: false,
            loadingFunding: false,
            loadingBalances: false,
            
            // Transactions
            transactions: [],
            transactionsFilters: {
                search: '',
                status: '',
                per_page: 10,
                page: 1
            },
            transactionsPagination: {
                current_page: 1,
                last_page: 1,
                from: 0,
                to: 0,
                total: 0
            },

            // Modal
            showModal: false,
            loadingModal: false,
            transactionDetails: null,

            // Funding
            funding: [],
            fundingFilters: {
                search: '',
                per_page: 10,
                page: 1
            },
            fundingPagination: {
                current_page: 1,
                last_page: 1,
                from: 0,
                to: 0,
                total: 0
            },

            // Balances
            totalBalances: '0.00',

            init() {
                this.fetchTransactions();
                this.fetchFunding();
                this.fetchTotalBalances();
                
                // Auto-refresh balances every 20 seconds
                setInterval(() => {
                    this.fetchTotalBalances();
                }, 20000);
            },

            async fetchTransactions() {
                this.loadingTransactions = true;
                try {
                    const params = new URLSearchParams({
                        search: this.transactionsFilters.search,
                        status: this.transactionsFilters.status,
                        per_page: this.transactionsFilters.per_page,
                        page: this.transactionsFilters.page,
                        date_from: '',
                        date_to: '',
                        product_plan_category_filter: '',
                        phone_recharged: ''
                    });

                    const response = await fetch(`/admin/transactions/admin_fetch_transactions_paginated?${params}`);
                    const data = await response.json();

                    this.transactions = data.data || [];
                    this.transactionsPagination = {
                        current_page: data.current_page,
                        last_page: data.last_page,
                        from: data.from,
                        to: data.to,
                        total: data.total
                    };
                } catch (error) {
                    console.error('Error fetching transactions:', error);
                } finally {
                    this.loadingTransactions = false;
                }
            },

            async fetchFunding() {
                this.loadingFunding = true;
                try {
                    const params = new URLSearchParams({
                        search: this.fundingFilters.search,
                        per_page: this.fundingFilters.per_page,
                        page: this.fundingFilters.page,
                        date_from: '',
                        date_to: '',
                        reference: ''
                    });

                    const response = await fetch(`/transactions/fetch_crystal_pay_funding_transactions?${params}`);
                    const data = await response.json();

                    this.funding = data.data;
                    this.fundingPagination = {
                        current_page: data.current_page,
                        last_page: data.last_page,
                        from: data.from,
                        to: data.to,
                        total: data.total
                    };
                } catch (error) {
                    console.error('Error fetching funding:', error);
                } finally {
                    this.loadingFunding = false;
                }
            },

            async fetchTotalBalances() {
                this.loadingBalances = true;
                try {
                    const response = await fetch('/admin/wallet/total_balances', {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    const data = await response.json();
                    this.totalBalances = parseFloat(data.balance).toLocaleString('en-NG', {minimumFractionDigits: 2});
                } catch (error) {
                    console.error('Error fetching balances:', error);
                } finally {
                    this.loadingBalances = false;
                }
            },

            async refreshAll() {
                this.refreshing = true;
                await Promise.all([
                    this.fetchTransactions(),
                    this.fetchFunding(),
                    this.fetchTotalBalances()
                ]);
                setTimeout(() => {
                    this.refreshing = false;
                }, 500);
            },

            async viewTransactionDetails(transactionId) {
                this.showModal = true;
                this.loadingModal = true;
                this.transactionDetails = null;

                try {
                    const response = await fetch(`/transactions/details/${transactionId}/json`);
                    const data = await response.json();
                    this.transactionDetails = data;
                } catch (error) {
                    console.error('Error fetching transaction details:', error);
                    alert('Failed to load transaction details. Please try again.');
                    this.closeModal();
                } finally {
                    this.loadingModal = false;
                }
            },

            closeModal() {
                this.showModal = false;
                this.transactionDetails = null;
            },

            changeTransactionsPage(page) {
                if (page >= 1 && page <= this.transactionsPagination.last_page) {
                    this.transactionsFilters.page = page;
                    this.fetchTransactions();
                }
            },

            changeFundingPage(page) {
                if (page >= 1 && page <= this.fundingPagination.last_page) {
                    this.fundingFilters.page = page;
                    this.fetchFunding();
                }
            },

            getTransactionsPageNumbers() {
                const pages = [];
                const current = this.transactionsPagination.current_page;
                const last = this.transactionsPagination.last_page;
                
                let start = Math.max(1, current - 2);
                let end = Math.min(last, start + 4);
                
                if (end - start < 4) {
                    start = Math.max(1, end - 4);
                }
                
                for (let i = start; i <= end; i++) {
                    pages.push(i);
                }
                
                return pages;
            },

            getFundingPageNumbers() {
                const pages = [];
                const current = this.fundingPagination.current_page;
                const last = this.fundingPagination.last_page;
                
                let start = Math.max(1, current - 2);
                let end = Math.min(last, start + 4);
                
                if (end - start < 4) {
                    start = Math.max(1, end - 4);
                }
                
                for (let i = start; i <= end; i++) {
                    pages.push(i);
                }
                
                return pages;
            },

            getStatusText(status) {
                const statuses = {
                    '1': 'Success',
                    '0': 'Pending',
                    '-1': 'Failed',
                    '2': 'Refunded',
                    1: 'Success',
                    0: 'Pending',
                    '-1': 'Failed',
                    2: 'Refunded'
                };
                return statuses[status] || 'Unknown';
            },

            formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            },

            formatTime(dateString) {
                const date = new Date(dateString);
                return date.toLocaleTimeString('en-GB', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        }
    }
</script>

@endsection
