@extends('layouts.app')
@section('content')

<div class="main-content bg-gray-50 min-h-screen" x-data="usersPage()" x-init="init()">

    @if(Session::has('success'))
    <div class="mx-6 mt-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">✅ {{ Session::get('success') }}</div>
    @endif
    @if(Session::has('failure'))
    <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">❌ {{ Session::get('failure') }}</div>
    @endif

    <!-- Page Header -->
    <div class="px-6 pt-6 pb-4 flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"> Users Management</h1>
            <p class="text-gray-500 text-sm mt-1">Manage all registered users</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="px-6 mb-4">
        <div class="flex space-x-1 bg-white rounded-xl shadow-sm p-1 w-fit border border-gray-100">
            <button @click="activeTab='view'" :class="activeTab==='view' ? 'bg-blue-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100'" class="px-5 py-2 rounded-lg text-sm font-medium transition">
                👁 View Users
            </button>
            <button @click="activeTab='create'" :class="activeTab==='create' ? 'bg-blue-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100'" class="px-5 py-2 rounded-lg text-sm font-medium transition">
                ➕ Create User
            </button>
        </div>
    </div>

    <!-- VIEW USERS TAB -->
    <div x-show="activeTab==='view'" class="px-6">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">All Users</h2>
                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                    <span x-text="pagination.total || 0"></span> Total
                </span>
            </div>
            <!-- Filters -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                <div class="flex flex-wrap gap-3">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" x-model="filters.search" @input.debounce.500ms="fetchUsers()"
                            placeholder="🔍 Search name, email, phone, username..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <input type="date" x-model="filters.date_from" @change="fetchUsers()"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <input type="date" x-model="filters.date_to" @change="fetchUsers()"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <select x-model="filters.per_page" @change="fetchUsers()"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <button @click="fetchUsers()" :disabled="loading"
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
                <p class="mt-3 text-gray-500 text-sm">Loading users...</p>
            </div>
            <!-- Table -->
            <div x-show="!loading" class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">#</th>
                            <th class="px-1 py-3 text-left text-xs font-semibold text-gray-600 uppercase">User</th>
                            <th class="px-1 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Contact</th>
                            <th class="px-1 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Wallet (₦)</th>
                            <th class="px-1 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-1 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Upline</th>
                            <th class="px-1 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Joined</th>
                            <th class="px-1 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Last Login</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-if="users.length === 0">
                            <tr><td colspan="9" class="py-16 text-center text-gray-500">No users found</td></tr>
                        </template>
                        <template x-for="(u, i) in users" :key="u.id">
                            <tr class="hover:bg-blue-50 transition">
                                <td class="px-4 py-3 text-xs text-gray-500" x-text="(pagination.current_page - 1) * parseInt(filters.per_page) + i + 1"></td>
                                <td class="px-1 py-3">
                                    <div class="font-semibold text-gray-900 text-sm" x-text="u.first_name + ' ' + u.last_name"></div>
                                    <div class="text-xs text-gray-500" x-text="'@' + u.username"></div>
                                </td>
                                <td class="px-1 py-3">
                                    <div class="text-xs text-gray-700" x-text="u.email"></div>
                                    <a :href="'tel:' + u.phone_number" class="text-xs text-blue-600 hover:underline" x-text="u.phone_number"></a>
                                </td>
                                <td class="px-1 py-3 text-right font-bold text-gray-900 text-sm">
                                    ₦<span x-text="parseFloat(u.main_wallet || 0).toLocaleString('en-NG', {minimumFractionDigits: 2})"></span>
                                </td>
                                <td class="px-1 py-3 text-center">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                        :class="u.email_verified_at ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                        x-text="u.email_verified_at ? 'Verified' : 'Unverified'">
                                    </span>
                                </td>
                                <td class="px-1 py-3 text-xs text-gray-600" x-text="u.upline_username ? '@' + u.upline_username : '—'"></td>
                                <td class="px-1 py-3 text-xs text-gray-600 whitespace-nowrap" x-text="formatDate(u.created_at)"></td>
                                <td class="px-1 py-3 text-xs text-gray-600 whitespace-nowrap" x-text="formatDate(u.updated_at)"></td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a :href="u.manage_url" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition whitespace-nowrap">Manage</a>
                                        <a x-show="u.impersonate_url" :href="u.impersonate_url" class="inline-flex items-center px-3 py-1.5 bg-purple-600 text-white text-xs font-medium rounded-lg hover:bg-purple-700 transition whitespace-nowrap">Access</a>
                                    </div>
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

    <!-- CREATE USER TAB -->
    <div x-show="activeTab==='create'" class="px-6 pb-6">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                <h2 class="text-lg font-bold text-gray-900">➕ Create New User</h2>
            </div>
            <div class="p-6">
                @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
                    <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                            <input type="text" name="username" required value="{{ old('username') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" placeholder="johndoe">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" placeholder="John">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" placeholder="Doe">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Other Names</label>
                            <input type="text" name="other_names" value="{{ old('other_names') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" placeholder="Middle name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" placeholder="john@example.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="text" name="phone_number" value="{{ old('phone_number') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" placeholder="08012345678">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">PIN</label>
                            <input type="number" name="pin" value="{{ old('pin') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" placeholder="4-digit PIN">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">User Plan <span class="text-red-500">*</span></label>
                            <select name="user_plan_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="">Select plan</option>
                                @foreach($user_plans as $plan)
                                    <option value="{{ $plan->id }}" {{ old('user_plan_id') == $plan->id ? 'selected' : '' }}>
                                        {{ $plan->user_plan_name ?? $plan->default_user_plan_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input type="password" name="password" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" placeholder="Password">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" placeholder="Confirm password">
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition">
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
function usersPage() {
    return {
        activeTab: 'view',
        loading: false,
        users: [],
        filters: { search: '', phone: '', email: '', date_from: '', date_to: '', per_page: 10, page: 1 },
        pagination: { current_page: 1, last_page: 1, from: 0, to: 0, total: 0 },

        init() { this.fetchUsers(); },

        async fetchUsers() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    search: this.filters.search,
                    phone: this.filters.phone,
                    email: this.filters.email,
                    date_from: this.filters.date_from,
                    date_to: this.filters.date_to,
                    per_page: this.filters.per_page,
                    page: this.filters.page,
                });
                const res = await fetch(`/admin/users/fetch_users_paginated?${params}`);
                const data = await res.json();
                this.users = data.data || [];
                this.pagination = { current_page: data.current_page, last_page: data.last_page, from: data.from, to: data.to, total: data.total };
            } catch(e) { console.error(e); }
            finally { this.loading = false; }
        },

        changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.filters.page = page;
                this.fetchUsers();
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
