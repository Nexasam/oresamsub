@extends('layouts.app')
@section('content')

<div class="main-content bg-gray-50 min-h-screen" x-data="adminTransactionsPage()" x-init="init()">

    <!-- Page Header -->
    <div class="px-6 pt-6 pb-4 flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">💳 All Transactions</h1>
            <p class="text-gray-500 text-sm mt-1">View and manage all platform transactions</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="px-6 pb-6">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">

            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Transactions</h2>
                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                    <span x-text="pagination.total || 0"></span> Total
                </span>
            </div>

            <!-- Filters -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                <div class="flex flex-wrap gap-3">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" x-model="filters.search" @input.debounce.500ms="fetchTransactions()"
                            placeholder="🔍 Search user, phone, product, reference..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <select x-model="filters.status" @change="fetchTransactions()"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="1">✓ Success</option>
                        <option value="0">⏳ Pending</option>
                        <option value="-1">✗ Failed</option>
                        <option value="2">↩ Refunded</option>
                    </select>
                    <select x-model="filters.category" @change="fetchTransactions()"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        <option value="data">Data</option>
                        <option value="airtime">Airtime</option>
                        <option value="cable_subscription">Cable</option>
                        <option value="utility_bills">Utility Bills</option>
                    </select>
                    <input type="date" x-model="filters.date_from" @change="fetchTransactions()"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <input type="date" x-model="filters.date_to" @change="fetchTransactions()"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <select x-model="filters.per_page" @change="fetchTransactions()"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <button @click="fetchTransactions()" :disabled="loading"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                        <svg class="w-4 h-4 inline" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Loading -->
            <div x-show="loading" class="py-16 text-center">
                <div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
                <p class="mt-3 text-gray-500 text-sm">Loading transactions...</p>
            </div>

            <!-- Table -->
            <div x-show="!loading" class="overflow-x-auto">
                <table class="w-full" style="font-size:11px">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-2 py-3 text-left font-semibold text-gray-600 uppercase">#</th>
                            <th class="px-2 py-3 text-left font-semibold text-gray-600 uppercase">User</th>
                            <th class="px-2 py-3 text-left font-semibold text-gray-600 uppercase">Wallet</th>
                            <th class="px-2 py-3 text-left font-semibold text-gray-600 uppercase">Product</th>
                            <th class="px-2 py-3 text-left font-semibold text-gray-600 uppercase">Category</th>
                            <th class="px-2 py-3 text-left font-semibold text-gray-600 uppercase">Phone</th>
                            <th class="px-2 py-3 text-right font-semibold text-gray-600 uppercase">Amount</th>
                            <th class="px-2 py-3 text-right font-semibold text-gray-600 uppercase">Discount</th>
                            <th class="px-2 py-3 text-right font-semibold text-gray-600 uppercase">Bal Before</th>
                            <th class="px-2 py-3 text-right font-semibold text-gray-600 uppercase">Bal After</th>
                            <th class="px-2 py-3 text-center font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-2 py-3 text-left font-semibold text-gray-600 uppercase">Date</th>
                            <th class="px-2 py-3 text-center font-semibold text-gray-600 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-if="transactions.length === 0">
                            <tr>
                                <td colspan="13" class="py-16 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    No transactions found
                                </td>
                            </tr>
                        </template>
                        <template x-for="(txn, i) in transactions" :key="txn.id">
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-2 py-2 text-gray-500" x-text="(pagination.current_page - 1) * parseInt(filters.per_page) + i + 1"></td>
                                <td class="px-2 py-2">
                                    <div class="font-semibold text-gray-900 whitespace-nowrap" x-text="(txn.user?.first_name || '') + ' ' + (txn.user?.last_name || '')"></div>
                                    <div class="text-gray-400" x-text="'@' + (txn.user?.username || '')"></div>
                                </td>
                                <td class="px-2 py-2">
                                    <span class="inline-flex px-1.5 py-0.5 font-semibold rounded bg-purple-100 text-purple-800"
                                        x-text="txn.wallet_category === 'main_wallet' ? 'MAIN' : 'DATA'"></span>
                                </td>
                                <td class="px-2 py-2 max-w-[130px]">
                                    <div class="font-medium text-gray-900 truncate" x-text="txn.product_plan?.product_plan_name || 'N/A'"></div>
                                    <div class="text-gray-400 truncate" x-text="txn.product_plan?.product_plan_category_name || ''"></div>
                                </td>
                                <td class="px-2 py-2">
                                    <span class="inline-flex px-1.5 py-0.5 font-semibold rounded"
                                        :class="{
                                            'bg-blue-100 text-blue-800': txn.transaction_category === 'data',
                                            'bg-orange-100 text-orange-800': txn.transaction_category === 'airtime',
                                            'bg-purple-100 text-purple-800': txn.transaction_category === 'cable_subscription',
                                            'bg-yellow-100 text-yellow-800': txn.transaction_category === 'utility_bills'
                                        }"
                                        x-text="txn.transaction_category"></span>
                                </td>
                                <td class="px-2 py-2 text-gray-700 whitespace-nowrap" x-text="txn.phone_number || 'N/A'"></td>
                                <td class="px-2 py-2 text-right font-bold text-gray-900">
                                    ₦<span x-text="parseFloat(txn.amount || 0).toLocaleString('en-NG', {minimumFractionDigits: 2})"></span>
                                </td>
                                <td class="px-2 py-2 text-right text-gray-600">
                                    ₦<span x-text="parseFloat(txn.discounted_amount || 0).toLocaleString('en-NG', {minimumFractionDigits: 2})"></span>
                                </td>
                                <td class="px-2 py-2 text-right text-gray-600">
                                    ₦<span x-text="parseFloat(txn.balance_before || 0).toLocaleString('en-NG', {minimumFractionDigits: 2})"></span>
                                </td>
                                <td class="px-2 py-2 text-right text-gray-600">
                                    ₦<span x-text="parseFloat(txn.balance_after || 0).toLocaleString('en-NG', {minimumFractionDigits: 2})"></span>
                                </td>
                                <td class="px-2 py-2 text-center">
                                    <span class="inline-flex px-2 py-0.5 font-semibold rounded-full"
                                        :class="{
                                            'bg-green-100 text-green-800': txn.status == 1,
                                            'bg-yellow-100 text-yellow-800': txn.status == 0,
                                            'bg-red-100 text-red-800': txn.status == -1,
                                            'bg-blue-100 text-blue-800': txn.status == 2
                                        }"
                                        x-text="statusText(txn.status)"></span>
                                </td>
                                <td class="px-2 py-2 text-gray-500 whitespace-nowrap" x-text="formatDate(txn.created_at)"></td>
                                <td class="px-2 py-2 text-center">
                                    <button @click="viewDetails(txn.id)"
                                        class="inline-flex items-center px-2 py-1 bg-blue-600 text-white font-medium rounded hover:bg-blue-700 transition">
                                        Details
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div x-show="!loading && pagination.last_page > 1" class="px-6 py-4 bg-gray-50 border-t flex items-center justify-between flex-wrap gap-3">
                <div class="text-sm text-gray-600">
                    Showing <span class="font-semibold" x-text="pagination.from"></span>–<span class="font-semibold" x-text="pagination.to"></span>
                    of <span class="font-semibold" x-text="pagination.total"></span>
                </div>
                <div class="flex space-x-1">
                    <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1"
                        class="px-3 py-1 border rounded-lg hover:bg-gray-100 disabled:opacity-40 text-sm">Previous</button>
                    <template x-for="page in getPageNumbers()" :key="page">
                        <button @click="changePage(page)"
                            :class="page === pagination.current_page ? 'bg-blue-600 text-white' : 'bg-white hover:bg-gray-50'"
                            class="px-3 py-1 border rounded-lg text-sm" x-text="page"></button>
                    </template>
                    <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page"
                        class="px-3 py-1 border rounded-lg hover:bg-gray-100 disabled:opacity-40 text-sm">Next</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Details Modal -->
    <div x-show="showModal" x-cloak @click.self="closeModal()"
        class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4"
        style="display:none">
        <div @click.stop class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between rounded-t-xl">
                <h3 class="text-xl font-bold text-white">Transaction Details</h3>
                <button @click="closeModal()" class="text-white hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div x-show="loadingModal" class="py-16 text-center">
                <div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
            </div>
            <div x-show="!loadingModal && txnDetails" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Transaction ID</p>
                        <p class="text-sm font-mono font-semibold text-gray-900 break-all" x-text="txnDetails?.id"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Status</p>
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full"
                            :class="{
                                'bg-green-100 text-green-800': txnDetails?.status == 1,
                                'bg-yellow-100 text-yellow-800': txnDetails?.status == 0,
                                'bg-red-100 text-red-800': txnDetails?.status == -1,
                                'bg-blue-100 text-blue-800': txnDetails?.status == 2
                            }"
                            x-text="statusText(txnDetails?.status)"></span>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">User</p>
                        <p class="text-sm font-semibold text-gray-900" x-text="(txnDetails?.user?.first_name || '') + ' ' + (txnDetails?.user?.last_name || '')"></p>
                        <p class="text-xs text-gray-500" x-text="txnDetails?.user?.email"></p>
                        <p class="text-xs text-gray-500" x-text="txnDetails?.user?.phone_number"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Product</p>
                        <p class="text-sm font-semibold text-gray-900" x-text="txnDetails?.product_plan?.product_plan_name || 'N/A'"></p>
                        <p class="text-xs text-gray-500" x-text="txnDetails?.transaction_category"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Phone Number</p>
                        <p class="text-sm font-semibold font-mono text-gray-900" x-text="txnDetails?.phone_number || 'N/A'"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Wallet</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded bg-purple-100 text-purple-800"
                            x-text="txnDetails?.wallet_category === 'main_wallet' ? 'MAIN WALLET' : 'DATA WALLET'"></span>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                        <p class="text-xs text-green-700 mb-1">Amount</p>
                        <p class="text-lg font-bold text-green-900">₦<span x-text="parseFloat(txnDetails?.amount || 0).toLocaleString('en-NG', {minimumFractionDigits: 2})"></span></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Discount</p>
                        <p class="text-sm font-semibold text-gray-900">₦<span x-text="parseFloat(txnDetails?.discounted_amount || 0).toLocaleString('en-NG', {minimumFractionDigits: 2})"></span></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Balance Before</p>
                        <p class="text-sm font-semibold text-gray-900">₦<span x-text="parseFloat(txnDetails?.balance_before || 0).toLocaleString('en-NG', {minimumFractionDigits: 2})"></span></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Balance After</p>
                        <p class="text-sm font-semibold text-gray-900">₦<span x-text="parseFloat(txnDetails?.balance_after || 0).toLocaleString('en-NG', {minimumFractionDigits: 2})"></span></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Txn Reference</p>
                        <p class="text-sm font-mono text-gray-900" x-text="txnDetails?.txn_reference || 'N/A'"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Route</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800" x-text="txnDetails?.transaction_route || 'N/A'"></span>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 md:col-span-2">
                        <p class="text-xs text-gray-500 mb-1">Date</p>
                        <p class="text-sm font-semibold text-gray-900" x-text="formatDate(txnDetails?.created_at)"></p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div x-show="txnDetails?.user_screen_message" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-xs text-blue-700 font-semibold mb-1">User Message</p>
                        <p class="text-sm text-blue-900" x-text="txnDetails?.user_screen_message"></p>
                    </div>
                    <div x-show="txnDetails?.admin_screen_message" class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <p class="text-xs text-purple-700 font-semibold mb-1">Admin Message</p>
                        <p class="text-sm text-purple-900" x-text="txnDetails?.admin_screen_message"></p>
                    </div>
                    <div x-show="txnDetails?.description" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <p class="text-xs text-gray-700 font-semibold mb-1">Description</p>
                        <p class="text-sm text-gray-900" x-text="txnDetails?.description"></p>
                    </div>
                </div>
            </div>
            <div class="sticky bottom-0 bg-gray-50 px-6 py-4 flex justify-end rounded-b-xl border-t">
                <button @click="closeModal()" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-medium">Close</button>
            </div>
        </div>
    </div>

</div>

<script>
function adminTransactionsPage() {
    return {
        loading: false,
        transactions: [],
        filters: { search: '', status: '', category: '', date_from: '', date_to: '', per_page: 10, page: 1 },
        pagination: { current_page: 1, last_page: 1, from: 0, to: 0, total: 0 },
        showModal: false,
        loadingModal: false,
        txnDetails: null,

        init() { this.fetchTransactions(); },

        async fetchTransactions() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    search: this.filters.search,
                    status: this.filters.status,
                    per_page: this.filters.per_page,
                    page: this.filters.page,
                    date_from: this.filters.date_from,
                    date_to: this.filters.date_to,
                    product_plan_category_filter: '',
                    phone_recharged: ''
                });
                const res = await fetch(`/admin/transactions/admin_fetch_transactions_paginated?${params}`);
                const data = await res.json();
                this.transactions = data.data || [];
                this.pagination = { current_page: data.current_page, last_page: data.last_page, from: data.from, to: data.to, total: data.total };
            } catch(e) { console.error(e); }
            finally { this.loading = false; }
        },

        async viewDetails(id) {
            this.showModal = true;
            this.loadingModal = true;
            this.txnDetails = null;
            try {
                const res = await fetch(`/transactions/details/${id}/json`);
                this.txnDetails = await res.json();
            } catch(e) { console.error(e); this.closeModal(); }
            finally { this.loadingModal = false; }
        },

        closeModal() { this.showModal = false; this.txnDetails = null; },

        changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.filters.page = page;
                this.fetchTransactions();
            }
        },

        getPageNumbers() {
            const pages = [], cur = this.pagination.current_page, last = this.pagination.last_page;
            let start = Math.max(1, cur - 2), end = Math.min(last, start + 4);
            if (end - start < 4) start = Math.max(1, end - 4);
            for (let i = start; i <= end; i++) pages.push(i);
            return pages;
        },

        statusText(s) {
            return { 1: 'Success', '1': 'Success', 0: 'Pending', '0': 'Pending', '-1': 'Failed', 2: 'Refunded', '2': 'Refunded' }[s] || 'Unknown';
        },

        formatDate(d) {
            if (!d) return '—';
            return new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        }
    }
}
</script>

@endsection
