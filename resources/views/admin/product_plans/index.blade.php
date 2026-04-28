@extends('layouts.app')
@section('content')

<div class="main-content bg-gray-50 min-h-screen" x-data="productPlansPage()" x-init="init()">

    @if(Session::has('success'))
    <div class="mx-6 mt-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">✅ {{ Session::get('success') }}</div>
    @endif
    @if(Session::has('failure'))
    <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">❌ {{ Session::get('failure') }}</div>
    @endif

    <!-- Page Header -->
    <div class="px-6 pt-6 pb-4 flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📦 Product Plans</h1>
            <p class="text-gray-500 text-sm mt-1">Manage all product plans and pricing</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="px-6 pb-6">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">

            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">All Plans</h2>
                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                    <span x-text="pagination.total || 0"></span> Total
                </span>
            </div>

            <!-- Filters -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                <div class="flex flex-wrap gap-3">
                    <div class="flex-1 min-w-[220px]">
                        <input type="text" x-model="search" @input.debounce.500ms="fetchPlans()"
                            placeholder="🔍 Search plan name, network, category..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <select x-model="per_page" @change="fetchPlans()"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <button @click="fetchPlans()" :disabled="loading"
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
                <p class="mt-3 text-gray-500 text-sm">Loading plans...</p>
            </div>

            <!-- Table -->
            <div x-show="!loading" class="overflow-x-auto">
                <table class="w-full" style="font-size:11px">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-3 text-left font-semibold text-gray-600 uppercase">#</th>
                            <th class="px-2 py-3 text-left font-semibold text-gray-600 uppercase">Product</th>
                            <th class="px-2 py-3 text-left font-semibold text-gray-600 uppercase">Network</th>
                            <th class="px-2 py-3 text-left font-semibold text-gray-600 uppercase">Plan Name</th>
                            <th class="px-2 py-3 text-left font-semibold text-gray-600 uppercase">Category</th>
                            <th class="px-2 py-3 text-left font-semibold text-gray-600 uppercase">Automation</th>
                            <th class="px-2 py-3 text-right font-semibold text-gray-600 uppercase">MB</th>
                            <th class="px-2 py-3 text-right font-semibold text-gray-600 uppercase">Days</th>
                            <th class="px-2 py-3 text-right font-semibold text-gray-600 uppercase">Cost</th>
                            <th class="px-2 py-3 text-right font-semibold text-gray-600 uppercase">L1</th>
                            <th class="px-2 py-3 text-right font-semibold text-gray-600 uppercase">L2</th>
                            <th class="px-2 py-3 text-right font-semibold text-gray-600 uppercase">L3</th>
                            <th class="px-2 py-3 text-right font-semibold text-gray-600 uppercase">L4</th>
                            <th class="px-2 py-3 text-center font-semibold text-gray-600 uppercase">Visible</th>
                            <th class="px-2 py-3 text-center font-semibold text-gray-600 uppercase">Public</th>
                            <th class="px-2 py-3 text-left font-semibold text-gray-600 uppercase">Created</th>
                            <th class="px-2 py-3 text-center font-semibold text-gray-600 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-if="plans.length === 0">
                            <tr>
                                <td colspan="17" class="py-16 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    No product plans found
                                </td>
                            </tr>
                        </template>
                        <template x-for="(plan, i) in plans" :key="plan.id">
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-3 py-2 text-gray-500" x-text="(pagination.current_page - 1) * parseInt(per_page) + i + 1"></td>
                                <td class="px-2 py-2">
                                    <span class="inline-flex px-1.5 py-0.5 font-semibold rounded"
                                        :class="{
                                            'bg-blue-100 text-blue-800': plan.product_name === 'DATA',
                                            'bg-orange-100 text-orange-800': plan.product_name === 'AIRTIME',
                                            'bg-purple-100 text-purple-800': plan.product_name === 'CABLE SUBSCRIPTION',
                                            'bg-yellow-100 text-yellow-800': plan.product_name === 'UTILITY BILLS',
                                            'bg-gray-100 text-gray-800': !['DATA','AIRTIME','CABLE SUBSCRIPTION','UTILITY BILLS'].includes(plan.product_name)
                                        }"
                                        x-text="plan.product_name"></span>
                                </td>
                                <td class="px-2 py-2 font-semibold text-gray-800" x-text="plan.network_name"></td>
                                <td class="px-2 py-2 font-medium text-gray-900 whitespace-nowrap" x-text="plan.product_plan_name"></td>
                                <td class="px-2 py-2 text-gray-600 max-w-[120px] truncate" x-text="plan.category_name"></td>
                                <td class="px-2 py-2">
                                    <span class="inline-flex px-1.5 py-0.5 font-semibold rounded bg-indigo-100 text-indigo-800" x-text="plan.automation_name"></span>
                                </td>
                                <td class="px-2 py-2 text-right text-gray-700" x-text="plan.data_size_in_mb ?? '—'"></td>
                                <td class="px-2 py-2 text-right text-gray-700" x-text="plan.validity_in_days ?? '—'"></td>
                                <td class="px-2 py-2 text-right font-semibold text-gray-900" x-text="plan.cost_price ? '₦' + parseFloat(plan.cost_price).toLocaleString() : '—'"></td>
                                <td class="px-2 py-2 text-right text-green-700 font-semibold" x-text="plan.user_level_1_selling_price ? '₦' + parseFloat(plan.user_level_1_selling_price).toLocaleString() : '—'"></td>
                                <td class="px-2 py-2 text-right text-green-700" x-text="plan.user_level_2_selling_price ? '₦' + parseFloat(plan.user_level_2_selling_price).toLocaleString() : '—'"></td>
                                <td class="px-2 py-2 text-right text-green-700" x-text="plan.user_level_3_selling_price ? '₦' + parseFloat(plan.user_level_3_selling_price).toLocaleString() : '—'"></td>
                                <td class="px-2 py-2 text-right text-green-700" x-text="plan.user_level_4_selling_price ? '₦' + parseFloat(plan.user_level_4_selling_price).toLocaleString() : '—'"></td>
                                <td class="px-2 py-2 text-center">
                                    <button
                                        @click="toggleVisibility(plan, 'visibility')"
                                        :class="plan.visibility == 1 ? 'bg-green-500' : 'bg-gray-300'"
                                        class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none"
                                        :title="plan.visibility == 1 ? 'Visible - click to hide' : 'Hidden - click to show'">
                                        <span
                                            :class="plan.visibility == 1 ? 'translate-x-5' : 'translate-x-1'"
                                            class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform shadow"></span>
                                    </button>
                                </td>
                                <td class="px-2 py-2 text-center">
                                    <button
                                        @click="toggleVisibility(plan, 'public_visibility')"
                                        :class="plan.public_visibility == 1 ? 'bg-green-500' : 'bg-gray-300'"
                                        class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none"
                                        :title="plan.public_visibility == 1 ? 'Public - click to hide' : 'Private - click to show'">
                                        <span
                                            :class="plan.public_visibility == 1 ? 'translate-x-5' : 'translate-x-1'"
                                            class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform shadow"></span>
                                    </button>
                                </td>
                                <td class="px-2 py-2 text-gray-500 whitespace-nowrap" x-text="formatDate(plan.created_at)"></td>
                                <td class="px-2 py-2 text-center">
                                    <button @click="viewDetails(plan.id)"
                                        class="inline-flex items-center px-2 py-1 bg-blue-600 text-white text-[10px] font-medium rounded hover:bg-blue-700 transition">
                                        Details
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div x-show="!loading && pagination.last_page > 1" class="px-6 py-4 bg-gray-50 border-t flex items-center justify-between">
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

<!-- Product Plan Details Modal -->
<div x-show="showModal" x-cloak @click.self="closeModal()"
    class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4"
    style="display:none">
    <div @click.stop class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between rounded-t-xl">
            <h3 class="text-lg font-bold text-white">📦 Plan Details</h3>
            <button @click="closeModal()" class="text-white hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div x-show="loadingModal" class="py-16 text-center">
            <div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
        </div>
        <div x-show="!loadingModal && planDetails" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="bg-gray-50 rounded-lg p-4 md:col-span-2">
                    <p class="text-xs text-gray-500 mb-1">Plan Name</p>
                    <p class="text-sm font-bold text-gray-900" x-text="planDetails?.product_plan_name"></p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-500 mb-1">Product</p>
                    <p class="text-sm font-semibold text-gray-900" x-text="planDetails?.product_name"></p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-500 mb-1">Network</p>
                    <p class="text-sm font-semibold text-gray-900" x-text="planDetails?.network_name"></p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-500 mb-1">Category</p>
                    <p class="text-sm text-gray-700" x-text="planDetails?.category_name"></p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-500 mb-1">Automation</p>
                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded bg-indigo-100 text-indigo-800" x-text="planDetails?.automation_name"></span>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-500 mb-1">Automation Plan ID</p>
                    <p class="text-sm font-mono text-gray-700" x-text="planDetails?.automation_product_plan_id ?? '—'"></p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-500 mb-1">Data Size (MB)</p>
                    <p class="text-sm font-semibold text-gray-900" x-text="planDetails?.data_size_in_mb ?? '—'"></p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-500 mb-1">Validity (Days)</p>
                    <p class="text-sm font-semibold text-gray-900" x-text="planDetails?.validity_in_days ?? '—'"></p>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                    <p class="text-xs text-green-700 mb-1">Cost Price</p>
                    <p class="text-lg font-bold text-green-900">₦<span x-text="planDetails?.cost_price ? parseFloat(planDetails.cost_price).toLocaleString('en-NG', {minimumFractionDigits: 2}) : '—'"></span></p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-500 mb-1">Default Selling Price</p>
                    <p class="text-sm font-semibold text-gray-900">₦<span x-text="planDetails?.default_selling_price ? parseFloat(planDetails.default_selling_price).toLocaleString('en-NG', {minimumFractionDigits: 2}) : '—'"></span></p>
                </div>
            </div>
            <!-- Selling Prices -->
            <div class="bg-blue-50 rounded-lg p-4 mb-4">
                <p class="text-xs font-semibold text-blue-700 mb-3">Selling Prices by Level</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <template x-for="(lvl, idx) in [1,2,3,4]" :key="idx">
                        <div class="bg-white rounded p-3 text-center shadow-sm">
                            <p class="text-xs text-gray-500 mb-1" x-text="'Level ' + lvl"></p>
                            <p class="text-sm font-bold text-gray-900">₦<span x-text="planDetails?.['user_level_' + lvl + '_selling_price'] ? parseFloat(planDetails['user_level_' + lvl + '_selling_price']).toLocaleString('en-NG', {minimumFractionDigits: 2}) : '—'"></span></p>
                        </div>
                    </template>
                </div>
            </div>
            <!-- Commission -->
            <div class="bg-purple-50 rounded-lg p-4 mb-4">
                <p class="text-xs font-semibold text-purple-700 mb-3">Commission Settings</p>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div class="bg-white rounded p-3 shadow-sm">
                        <p class="text-xs text-gray-500 mb-1">Option</p>
                        <p class="text-sm font-semibold text-gray-900 uppercase" x-text="planDetails?.upline_commission_option ?? '—'"></p>
                    </div>
                    <div class="bg-white rounded p-3 shadow-sm">
                        <p class="text-xs text-gray-500 mb-1">% Commission</p>
                        <p class="text-sm font-semibold text-gray-900" x-text="planDetails?.upline_percentage_commission ?? '—'"></p>
                    </div>
                    <div class="bg-white rounded p-3 shadow-sm">
                        <p class="text-xs text-gray-500 mb-1">Flat Commission</p>
                        <p class="text-sm font-semibold text-gray-900" x-text="planDetails?.upline_flat_commission ?? '—'"></p>
                    </div>
                    <div class="bg-white rounded p-3 shadow-sm">
                        <p class="text-xs text-gray-500 mb-1">Commission Cap</p>
                        <p class="text-sm font-semibold text-gray-900" x-text="planDetails?.upline_commission_cap ?? '—'"></p>
                    </div>
                    <div class="bg-white rounded p-3 shadow-sm">
                        <p class="text-xs text-gray-500 mb-1">Visible</p>
                        <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full"
                            :class="planDetails?.visibility == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                            x-text="planDetails?.visibility == 1 ? 'Yes' : 'No'"></span>
                    </div>
                    <div class="bg-white rounded p-3 shadow-sm">
                        <p class="text-xs text-gray-500 mb-1">Public</p>
                        <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full"
                            :class="planDetails?.public_visibility == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                            x-text="planDetails?.public_visibility == 1 ? 'Yes' : 'No'"></span>
                    </div>
                </div>
            </div>
            <div class="text-xs text-gray-400 text-right">Last updated: <span x-text="planDetails?.updated_at ? new Date(planDetails.updated_at).toLocaleString('en-GB') : '—'"></span></div>
        </div>
        <div class="sticky bottom-0 bg-gray-50 px-6 py-4 flex justify-between items-center rounded-b-xl border-t">
            <a :href="planDetails?.details_url" target="_blank"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                Open Full Edit Page
            </a>
            <button @click="closeModal()" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-medium text-sm">Close</button>
        </div>
    </div>
</div>

</div>

<script>
function productPlansPage() {
    return {
        loading: false,
        plans: [],
        search: '',
        per_page: 10,
        page: 1,
        pagination: { current_page: 1, last_page: 1, from: 0, to: 0, total: 0 },
        showModal: false,
        loadingModal: false,
        planDetails: null,

        init() { this.fetchPlans(); },

        async viewDetails(id) {
            this.showModal = true;
            this.loadingModal = true;
            this.planDetails = null;
            try {
                const res = await fetch(`/admin/product_plans/details/${id}/json`);
                this.planDetails = await res.json();
            } catch(e) { console.error(e); this.closeModal(); }
            finally { this.loadingModal = false; }
        },

        closeModal() { this.showModal = false; this.planDetails = null; },

        async toggleVisibility(plan, field) {
            const url = field === 'visibility'
                ? '/admin/toggle_product_visibility'
                : '/admin/toggle_product_public_visibility';
            try {
                const params = new URLSearchParams({
                    productPlanId: plan.id,
                    token: '{{ csrf_token() }}'
                });
                const res = await fetch(`${url}?${params}`);
                const data = await res.json();
                if (data.status === '1') {
                    plan[field] = plan[field] == 1 ? 0 : 1;
                }
            } catch(e) { console.error(e); }
        },

        async fetchPlans() {
            this.loading = true;
            try {
                const params = new URLSearchParams({ search: this.search, per_page: this.per_page, page: this.page });
                const res = await fetch(`/admin/product_plans/fetch_product_plans_paginated?${params}`);
                const data = await res.json();
                this.plans = data.data || [];
                this.pagination = { current_page: data.current_page, last_page: data.last_page, from: data.from, to: data.to, total: data.total };
            } catch(e) { console.error(e); }
            finally { this.loading = false; }
        },

        changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.page = page;
                this.fetchPlans();
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
            return new Date(d).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        }
    }
}
</script>

@endsection
