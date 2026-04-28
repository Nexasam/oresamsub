@extends('layouts.app')
@section('content')

<div class="main-content bg-gray-50 min-h-screen" x-data="categoriesPage()" x-init="init()">

    @if(Session::has('success'))
    <div class="mx-6 mt-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">✅ {{ Session::get('success') }}</div>
    @endif
    @if(Session::has('failure'))
    <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">❌ {{ Session::get('failure') }}</div>
    @endif
    @if($errors->any())
    <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
        <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <!-- Page Header -->
    <div class="px-6 pt-6 pb-4 flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">🗂 Product Plan Categories</h1>
            <p class="text-gray-500 text-sm mt-1">Manage categories, hot sales and visibility</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="px-6 mb-4">
        <div class="flex space-x-1 bg-white rounded-xl shadow-sm p-1 w-fit border border-gray-100">
            <button @click="activeTab='view'"
                :class="activeTab==='view' ? 'bg-blue-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100'"
                class="px-5 py-2 rounded-lg text-sm font-medium transition">
                👁 View Categories
            </button>
            <button @click="activeTab='create'"
                :class="activeTab==='create' ? 'bg-blue-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100'"
                class="px-5 py-2 rounded-lg text-sm font-medium transition">
                ➕ Create Category
            </button>
        </div>
    </div>

    <!-- VIEW TAB -->
    <div x-show="activeTab==='view'" class="px-6">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">

            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">All Categories</h2>
                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                    <span x-text="pagination.total || 0"></span> Total
                </span>
            </div>

            <!-- Filters -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                <div class="flex flex-wrap gap-3">
                    <div class="flex-1 min-w-[220px]">
                        <input type="text" x-model="search" @input.debounce.500ms="fetchCategories()"
                            placeholder="🔍 Search category, product, network, automation..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <select x-model="per_page" @change="fetchCategories()"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <button @click="fetchCategories()" :disabled="loading"
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
                <p class="mt-3 text-gray-500 text-sm">Loading categories...</p>
            </div>

            <!-- Table -->
            <div x-show="!loading" class="overflow-x-auto">
                <table class="w-full" style="font-size:12px">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase">#</th>
                            <th class="px-3 py-3 text-left font-semibold text-gray-600 uppercase">Category Name</th>
                            <th class="px-3 py-3 text-left font-semibold text-gray-600 uppercase">Product</th>
                            <th class="px-3 py-3 text-left font-semibold text-gray-600 uppercase">Network</th>
                            <th class="px-3 py-3 text-left font-semibold text-gray-600 uppercase">Automation</th>
                            <th class="px-3 py-3 text-center font-semibold text-gray-600 uppercase">Hot Sales</th>
                            <th class="px-3 py-3 text-center font-semibold text-gray-600 uppercase">Visible</th>
                            <th class="px-3 py-3 text-left font-semibold text-gray-600 uppercase">Created</th>
                            <th class="px-3 py-3 text-center font-semibold text-gray-600 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-if="categories.length === 0">
                            <tr>
                                <td colspan="9" class="py-16 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    No categories found
                                </td>
                            </tr>
                        </template>
                        <template x-for="(cat, i) in categories" :key="cat.id">
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-4 py-3 text-gray-500" x-text="(pagination.current_page - 1) * parseInt(per_page) + i + 1"></td>
                                <td class="px-3 py-3 font-semibold text-gray-900" x-text="cat.product_plan_category_name"></td>
                                <td class="px-3 py-3">
                                    <span class="inline-flex px-2 py-0.5 font-semibold rounded text-xs"
                                        :class="{
                                            'bg-blue-100 text-blue-800':   cat.product_name === 'DATA',
                                            'bg-orange-100 text-orange-800': cat.product_name === 'AIRTIME',
                                            'bg-purple-100 text-purple-800': cat.product_name === 'CABLE SUBSCRIPTION',
                                            'bg-yellow-100 text-yellow-800': cat.product_name === 'UTILITY BILLS',
                                            'bg-gray-100 text-gray-700':   !['DATA','AIRTIME','CABLE SUBSCRIPTION','UTILITY BILLS'].includes(cat.product_name)
                                        }"
                                        x-text="cat.product_name"></span>
                                </td>
                                <td class="px-3 py-3 text-gray-700" x-text="cat.network_name"></td>
                                <td class="px-3 py-3">
                                    <span class="inline-flex px-2 py-0.5 font-semibold rounded text-xs bg-indigo-100 text-indigo-800" x-text="cat.automation_name"></span>
                                </td>
                                <!-- Hot Sales toggle -->
                                <td class="px-3 py-3 text-center">
                                    <button @click="toggleHotSales(cat)"
                                        :class="cat.is_hot_sales ? 'bg-orange-500' : 'bg-gray-300'"
                                        class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none"
                                        :title="cat.is_hot_sales ? 'Hot — click to remove' : 'Not hot — click to mark'">
                                        <span :class="cat.is_hot_sales ? 'translate-x-5' : 'translate-x-1'"
                                            class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform shadow"></span>
                                    </button>
                                </td>
                                <!-- Visibility toggle -->
                                <td class="px-3 py-3 text-center">
                                    <button @click="toggleVisibility(cat)"
                                        :class="cat.visibility == 1 ? 'bg-green-500' : 'bg-gray-300'"
                                        class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none"
                                        :title="cat.visibility == 1 ? 'Visible — click to hide' : 'Hidden — click to show'">
                                        <span :class="cat.visibility == 1 ? 'translate-x-5' : 'translate-x-1'"
                                            class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform shadow"></span>
                                    </button>
                                </td>
                                <td class="px-3 py-3 text-gray-500 whitespace-nowrap" x-text="formatDate(cat.created_at)"></td>
                                <td class="px-3 py-3 text-center">
                                    <button @click="viewDetails(cat.id)"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition">
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

    <!-- CREATE TAB -->
    <div x-show="activeTab==='create'" class="px-6 pb-6">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                <h2 class="text-lg font-bold text-gray-900">➕ Create New Category</h2>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('admin.product_plan_categories.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category Name <span class="text-red-500">*</span></label>
                            <input type="text" name="product_plan_category_name" required value="{{ old('product_plan_category_name') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                                placeholder="e.g. MTN SME DATA">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Product <span class="text-red-500">*</span></label>
                            <select name="product_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="">Select product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->product_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Network</label>
                            <select name="network_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="">None</option>
                                @foreach($networks as $network)
                                    <option value="{{ $network->id }}" {{ old('network_id') == $network->id ? 'selected' : '' }}>
                                        {{ $network->network_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Automation <span class="text-red-500">*</span></label>
                            <select name="automation_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="">Select automation</option>
                                @foreach($automations as $automation)
                                    <option value="{{ $automation->id }}" {{ old('automation_id') == $automation->id ? 'selected' : '' }}>
                                        {{ $automation->automation_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition">
                            Create Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div x-show="showModal" x-cloak @click.self="closeModal()"
        class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4"
        style="display:none">
        <div @click.stop class="bg-white rounded-xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between rounded-t-xl">
                <h3 class="text-lg font-bold text-white">🗂 Category Details</h3>
                <button @click="closeModal()" class="text-white hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div x-show="loadingModal" class="py-16 text-center">
                <div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
            </div>
            <div x-show="!loadingModal && catDetails" class="p-6 space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2 bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Category Name</p>
                        <p class="text-sm font-bold text-gray-900" x-text="catDetails?.product_plan_category_name"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Product</p>
                        <p class="text-sm font-semibold text-gray-900" x-text="catDetails?.product_name"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Network</p>
                        <p class="text-sm font-semibold text-gray-900" x-text="catDetails?.network_name"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Automation</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded bg-indigo-100 text-indigo-800" x-text="catDetails?.automation_name"></span>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Product Plans</p>
                        <p class="text-sm font-bold text-blue-700" x-text="catDetails?.product_plans_count + ' plans'"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Hot Sales</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                            :class="catDetails?.is_hot_sales ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-600'"
                            x-text="catDetails?.is_hot_sales ? '🔥 Yes' : 'No'"></span>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Visibility</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                            :class="catDetails?.visibility == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                            x-text="catDetails?.visibility == 1 ? 'Visible' : 'Hidden'"></span>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Referral Commission</p>
                        <p class="text-sm font-semibold text-gray-900">
                            <span x-text="catDetails?.referral_commission_value"></span>
                            <span x-text="catDetails?.referral_commission_method === 'percent' ? '%' : ' NGN flat'"></span>
                        </p>
                    </div>
                    <div class="col-span-2 bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Created</p>
                        <p class="text-sm text-gray-700" x-text="formatDate(catDetails?.created_at)"></p>
                    </div>
                </div>
            </div>
            <div class="sticky bottom-0 bg-gray-50 px-6 py-4 flex justify-between items-center rounded-b-xl border-t">
                <a :href="catDetails?.details_url"
                    class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                    Full Details & Plans →
                </a>
                <button @click="closeModal()" class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium text-sm">Close</button>
            </div>
        </div>
    </div>

</div>

<script>
function categoriesPage() {
    return {
        activeTab: 'view',
        loading: false,
        categories: [],
        search: '',
        per_page: 10,
        pagination: { current_page: 1, last_page: 1, from: 0, to: 0, total: 0 },
        currentPage: 1,
        showModal: false,
        loadingModal: false,
        catDetails: null,

        init() { this.fetchCategories(); },

        async fetchCategories() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    search:   this.search,
                    per_page: this.per_page,
                    page:     this.currentPage,
                });
                const res  = await fetch(`/admin/product_plan_categories/admin_fetch_product_plan_categories_paginated?${params}`);
                const data = await res.json();
                this.categories = data.data || [];
                this.pagination = { current_page: data.current_page, last_page: data.last_page, from: data.from, to: data.to, total: data.total };
            } catch(e) { console.error(e); }
            finally { this.loading = false; }
        },

        async viewDetails(id) {
            this.showModal = true;
            this.loadingModal = true;
            this.catDetails = null;
            try {
                const res = await fetch(`/admin/product_plan_categories/details/${id}/json`);
                this.catDetails = await res.json();
            } catch(e) { console.error(e); this.closeModal(); }
            finally { this.loadingModal = false; }
        },

        closeModal() { this.showModal = false; this.catDetails = null; },

        async toggleHotSales(cat) {
            const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
            try {
                const res = await fetch(`/admin/toggle_hot_sales?planCategoryId=${cat.id}&token=${token}`);
                const data = await res.json();
                if (data.status == 1) cat.is_hot_sales = !cat.is_hot_sales;
            } catch(e) { console.error(e); }
        },

        async toggleVisibility(cat) {
            const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
            try {
                const res = await fetch(`/admin/toggle_plan_category_visibility?productPlanCategoryId=${cat.id}&token=${token}`);
                const data = await res.json();
                if (data.status == 1) cat.visibility = cat.visibility == 1 ? 0 : 1;
            } catch(e) { console.error(e); }
        },

        changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.currentPage = page;
                this.fetchCategories();
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
