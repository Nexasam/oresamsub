@extends('oresamsub.layouts.app')

@section('content')
<div x-data="marketerDashboard()" x-init="fetchStats()" class="space-y-6 pt-2">


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

  <!-- Greeting -->
  <div class="flex items-center justify-between px-3">
    <h1 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
      👋 Hi, {{ auth()->user()->username }}
    </h1>

    <!-- Refresh -->
    <button @click="fetchStats" 
      class="group flex items-center px-3 py-1 rounded-xl bg-white dark:bg-gray-900 
             ring-1 ring-emerald-200 dark:ring-emerald-700 
             shadow-md hover:shadow-xl hover:scale-[1.03] 
             transition transform text-xs font-medium text-gray-700 dark:text-gray-200">
      <span class="flex items-center space-x-1">
        <span class="w-5 h-5 flex items-center justify-center rounded-full 
                     bg-gradient-to-r from-emerald-500 to-green-500 
                     text-white shadow-sm group-hover:scale-110 
                     transition duration-200 ease-in-out">
          <i class="fas fa-sync-alt text-[10px]"></i>
        </span>
        <span class="group-hover:text-green-600">Refresh</span>
      </span>
    </button>
  </div>


  <!-- Filters -->
  <div class="flex flex-wrap gap-2 px-3">
    <input type="date" x-model="filters.start_date"
      class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 
             bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-200">
    <input type="date" x-model="filters.end_date"
      class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 
             bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-200">
    <input type="text" x-model="filters.search" placeholder="🔍 Search referrals..."
      class="flex-1 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 
             bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-200">
    <button @click="fetchStats()" 
      class="bg-gradient-to-r from-emerald-500 to-green-600 
             text-white px-4 py-2 rounded-lg shadow hover:scale-105 transition">
      Apply
    </button>
  </div>


  <!-- Stats Cards -->
   <!-- Stats Cards -->
   <div class="grid grid-cols-2 gap-4 px-3">
    <template x-for="card in [
      { title: 'Total Downlines', value: stats.totalRefs, icon: '👥', color: 'from-indigo-500 to-blue-500' },
      { title: 'Total Txns', value: stats.totalTxns, icon: '💳', color: 'from-emerald-500 to-green-600' },
      { title: 'Downline Target', value: stats.userTarget, icon: '🎯', color: 'from-pink-500 to-rose-600' },
      { title: 'Txn Target', value: stats.txnTarget, icon: '📈', color: 'from-orange-500 to-yellow-500' }
    ]" :key="card.title">
      <div class="p-4 rounded-xl shadow bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-700 hover:shadow-lg transition transform hover:scale-[1.03]">
        <div class="flex items-center space-x-3">
          <div :class="'w-10 h-10 flex items-center justify-center rounded-full text-white bg-gradient-to-r ' + card.color">
            <span x-text="card.icon"></span>
          </div>
          <div>
            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="card.title"></p>
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100" x-text="card.value"></h3>
          </div>
        </div>
      </div>
    </template>
  </div>


  <!-- Referrals Table -->
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden mx-3">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-700 dark:text-gray-200">
      👥 Referred Users
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
          <tr>
            <th class="px-4 py-2 text-left">Name</th>
            <th class="px-4 py-2">Phone</th>
            <th class="px-4 py-2">Txns This Month</th>
            <th class="px-4 py-2">Txns Today</th>
            <th class="px-4 py-2">Joined</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <template x-for="user in stats.users" :key="user.id">
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 transition">
              <td class="px-4 py-2 font-medium text-gray-800 dark:text-gray-100" x-text="user.first_name"></td>
              <td class="px-4 py-2">
                <a 
                  :href="'tel:' + user.phone_number" 
                  class="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  x-text="user.phone_number"
                ></a>
              </td>
              <td class="px-4 py-2 text-center font-semibold text-emerald-600 dark:text-emerald-400" x-text="user.total_txn_month"></td>
              <td class="px-4 py-2 text-center font-semibold text-blue-600 dark:text-blue-400" x-text="user.total_txn_today"></td>
              <td class="px-4 py-2 text-gray-500 dark:text-gray-400" x-text="new Date(user.created_at).toLocaleDateString()"></td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </div>
  

</div>


<script>
function marketerDashboard() {
    return {
        darkMode: document.documentElement.classList.contains('dark'),
        stats: { totalRefs: 0, totalTxns: 0, userTarget: 0, txnTarget: 0, users: [] },
        filters: { start_date: '', end_date: '', search: '' },

        fetchStats() {
            $.ajax({
                url: "{{ route('marketer.stats') }}",
                method: "GET",
                data: this.filters,
                success: (res) => {
                    this.stats = res;
                },
                error: (err) => {
                    console.error("Error fetching stats", err);
                }
            });
        }
    }
}
</script>
@endsection
