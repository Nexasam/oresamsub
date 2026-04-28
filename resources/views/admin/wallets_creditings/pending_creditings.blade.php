@extends('layouts.app')
@section('content')

<div class="main-content bg-gray-50 min-h-screen" x-data="pendingCreditingsPage()" x-init="init()">

    @if(Session::has('success'))
    <div class="mx-6 mt-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">✅ {{ Session::get('success') }}</div>
    @endif
    @if(Session::has('failure'))
    <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">❌ {{ Session::get('failure') }}</div>
    @endif

    <!-- Page Header -->
    <div class="px-6 pt-6 pb-4 flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">⏳ Pending Funding Approvals</h1>
            <p class="text-gray-500 text-sm mt-1">
                Transactions above
                <span class="font-semibold text-orange-600">
                    {{ is_string($setting) ? 'SET MAX AMOUNT' : '₦'.number_format($setting->field_value) }}
                </span>
                awaiting manual approval
            </p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="px-6 pb-6">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">

            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Pending Creditings</h2>
                <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-semibold">
                    <span x-text="pagination.total || 0"></span> Total
                </span>
            </div>

            <!-- Filters -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                <div class="flex flex-wrap gap-3">
                    <div class="flex-1 min-w-[220px]">
                        <input type="text" x-model="filters.search" @input.debounce.500ms="fetchRecords()"
                            placeholder="🔍 Search user, reference, amount..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <input type="text" x-model="filters.reference" @input.debounce.500ms="fetchRecords()"
                        placeholder="Reference..."
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm w-44 focus:ring-2 focus:ring-blue-500">
                    <input type="date" x-model="filters.date_from" @change="fetchRecords()"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <input type="date" x-model="filters.date_to" @change="fetchRecords()"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <select x-model="filters.per_page" @change="fetchRecords()"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <button @click="fetchRecords()" :disabled="loading"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                        <svg class="w-4 h-4 inline" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Loading -->
            <div x-show="loading" class="py-16 text-center">
                <div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-orange-500"></div>
                <p class="mt-3 text-gray-500 text-sm">Loading records...</p>
            </div>

            <!-- Table -->
            <div x-show="!loading" class="overflow-x-auto">
                <table class="w-full" style="font-size:12px">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase">#</th>
                            <th class="px-3 py-3 text-left font-semibold text-gray-600 uppercase">User</th>
                            <th class="px-3 py-3 text-left font-semibold text-gray-600 uppercase">Payment Reference</th>
                            <th class="px-3 py-3 text-right font-semibold text-gray-600 uppercase">Amount</th>
                            <th class="px-3 py-3 text-center font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-3 py-3 text-left font-semibold text-gray-600 uppercase">Date</th>
                            <th class="px-3 py-3 text-center font-semibold text-gray-600 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-if="records.length === 0">
                            <tr>
                                <td colspan="7" class="py-16 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    No pending creditings found
                                </td>
                            </tr>
                        </template>
                        <template x-for="(row, i) in records" :key="row.id">
                            <tr class="hover:bg-orange-50 transition">
                                <td class="px-4 py-3 text-gray-500" x-text="(pagination.current_page - 1) * parseInt(filters.per_page) + i + 1"></td>
                                <td class="px-3 py-3">
                                    <div class="font-semibold text-gray-900" x-text="(row.user?.first_name || '') + ' ' + (row.user?.last_name || '')"></div>
                                    <div class="text-xs text-gray-500" x-text="row.user?.email || '—'"></div>
                                    <div class="text-xs text-blue-600" x-text="row.user?.phone_number || ''"></div>
                                </td>
                                <td class="px-3 py-3 font-mono text-gray-700 text-xs" x-text="row.payment_reference || '—'"></td>
                                <td class="px-3 py-3 text-right font-bold text-gray-900">
                                    ₦<span x-text="parseFloat(row.amount || 0).toLocaleString('en-NG', {minimumFractionDigits: 2})"></span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                        :class="row.status == 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'"
                                        x-text="row.status == 0 ? 'Pending' : 'Approved'">
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-gray-500 whitespace-nowrap" x-text="formatDate(row.created_at)"></td>
                                <td class="px-3 py-3 text-center">
                                    <template x-if="row.status == 0">
                                        <a :href="row.details_url"
                                            class="inline-flex items-center px-3 py-1.5 bg-orange-500 text-white text-xs font-medium rounded-lg hover:bg-orange-600 transition">
                                            Review
                                        </a>
                                    </template>
                                    <template x-if="row.status != 0">
                                        <span class="text-gray-400 text-xs">—</span>
                                    </template>
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

</div>

<script>
function pendingCreditingsPage() {
    return {
        loading: false,
        records: [],
        filters: { search: '', reference: '', date_from: '', date_to: '', per_page: 10, page: 1 },
        pagination: { current_page: 1, last_page: 1, from: 0, to: 0, total: 0 },

        init() { this.fetchRecords(); },

        async fetchRecords() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    search:    this.filters.search,
                    reference: this.filters.reference,
                    date_from: this.filters.date_from,
                    date_to:   this.filters.date_to,
                    per_page:  this.filters.per_page,
                    page:      this.filters.page,
                });
                const res  = await fetch(`/transactions/fetch_pending_creditings_paginated?${params}`);
                const data = await res.json();
                this.records    = data.data || [];
                this.pagination = {
                    current_page: data.current_page,
                    last_page:    data.last_page,
                    from:         data.from,
                    to:           data.to,
                    total:        data.total,
                };
            } catch(e) { console.error(e); }
            finally { this.loading = false; }
        },

        changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.filters.page = page;
                this.fetchRecords();
            }
        },

        getPageNumbers() {
            const pages = [], cur = this.pagination.current_page, last = this.pagination.last_page;
            let start = Math.max(1, cur - 2), end = Math.min(last, start + 4);
            if (end - start < 4) start = Math.max(1, end - 4);
            for (let i = start; i <= end; i++) pages.push(i);
            return pages;
        },

        formatDate(d) {
            if (!d) return '—';
            return new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        }
    }
}
</script>

@endsection
