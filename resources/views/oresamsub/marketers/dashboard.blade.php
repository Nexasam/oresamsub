@extends('oresamsub.layouts.app')

@section('content')
<div 
    x-data="marketerDashboard()" 
    x-init="fetchStats()" 
    class="p-6"
>
    <h2 class="text-2xl font-bold mb-4">📊 Marketer Dashboard</h2>

    <!-- Filters -->
    <div class="flex gap-2 mb-4">
        <input type="date" x-model="filters.start_date" class="border p-2 rounded">
        <input type="date" x-model="filters.end_date" class="border p-2 rounded">
        <button @click="fetchStats()" class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded shadow">
            <p>Total Referrals</p>
            <h3 class="text-xl font-bold" x-text="stats.totalRefs"></h3>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <p>Total Transactions</p>
            <h3 class="text-xl font-bold" x-text="stats.totalTxns"></h3>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <p>User Target</p>
            <h3 class="text-xl font-bold" x-text="stats.userTarget"></h3>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <p>Txn Target</p>
            <h3 class="text-xl font-bold" x-text="stats.txnTarget"></h3>
        </div>
    </div>

    <!-- Referrals Table -->
    <div class="bg-white p-4 rounded shadow">
        <h3 class="font-bold mb-2">Referred Users</h3>
        <table class="w-full border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2">Name</th>
                    <th class="border p-2">Phone</th>
                    <th class="border p-2">Txn This Month</th>
                    <th class="border p-2">Txn Today</th>
                    <th class="border p-2">Joined</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="user in stats.users" :key="user.id">
                    <tr>
                        <td class="border p-2" x-text="user.name"></td>
                        <td class="border p-2" x-text="user.phone"></td>
                        <td class="border p-2" x-text="user.total_txn_month"></td>
                        <td class="border p-2" x-text="user.total_txn_today"></td>
                        <td class="border p-2" x-text="new Date(user.created_at).toLocaleDateString()"></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>

<script>
function marketerDashboard() {
    return {
        stats: { totalRefs: 0, totalTxns: 0, userTarget: 0, txnTarget: 0, users: [] },
        filters: { start_date: '', end_date: '' },

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
