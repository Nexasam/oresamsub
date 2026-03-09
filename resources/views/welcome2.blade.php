<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Dashboard</title>

<script src="//unpkg.com/alpinejs" defer></script>
<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100">

<div x-data="{sidebar:true}" class="flex h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r flex flex-col">

        <div class="p-6 border-b">
            <h1 class="text-xl font-bold">Tando</h1>
            <p class="text-sm text-gray-500">Admin Console</p>
        </div>

        <nav class="flex-1 p-4 space-y-2 text-sm">

            <a href="#" class="block px-4 py-2 rounded-lg bg-gray-100 font-medium">
                Dashboard
            </a>

            <a href="#" class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                System Monitor
            </a>

            <p class="text-xs text-gray-400 mt-6 mb-2">USER MANAGEMENT</p>

            <a href="#" class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                All Users
            </a>

            <a href="#" class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                User Activity
            </a>

            <a href="#" class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                Matches & Likes
            </a>

            <a href="#" class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                Messages
            </a>

            <p class="text-xs text-gray-400 mt-6 mb-2">ANALYTICS</p>

            <a href="#" class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                App Analytics
            </a>

            <a href="#" class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                Revenue Analytics
            </a>

        </nav>

        <div class="p-4 border-t text-sm">
            <p class="font-medium">Super Admin</p>
            <p class="text-gray-500 text-xs">admin@email.com</p>
        </div>

    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto">

        <!-- Topbar -->
        <div class="bg-white border-b px-8 py-4 flex justify-between items-center">

            <input
                type="text"
                placeholder="Search users, matches, messages..."
                class="w-96 px-4 py-2 border rounded-lg focus:outline-none focus:ring"
            >

            <div class="flex items-center gap-4">

                <button class="text-gray-500 hover:text-gray-700">
                    🔔
                </button>

                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-yellow-400 rounded-full"></div>
                    <span class="text-sm font-medium">SU</span>
                </div>

            </div>

        </div>

        <!-- Dashboard Content -->
        <div class="p-8">

            <h2 class="text-2xl font-bold mb-6">Dashboard</h2>

            <!-- Stats -->
            <div class="grid grid-cols-4 gap-6 mb-8">

                <div class="bg-white p-6 rounded-xl border">
                    <p class="text-gray-500 text-sm">Total Users</p>
                    <h3 class="text-3xl font-bold mt-2">0</h3>
                </div>

                <div class="bg-white p-6 rounded-xl border">
                    <p class="text-gray-500 text-sm">Daily Active Users</p>
                    <h3 class="text-3xl font-bold mt-2">0</h3>
                </div>

                <div class="bg-white p-6 rounded-xl border">
                    <p class="text-gray-500 text-sm">Total Matches</p>
                    <h3 class="text-3xl font-bold mt-2">0</h3>
                </div>

                <div class="bg-white p-6 rounded-xl border">
                    <p class="text-gray-500 text-sm">Total Revenue</p>
                    <h3 class="text-3xl font-bold mt-2">$0</h3>
                </div>

            </div>

            <!-- Second Stats -->
            <div class="grid grid-cols-4 gap-6 mb-8">

                <div class="bg-white p-6 rounded-xl border">
                    <p class="text-gray-500 text-sm">New Signups</p>
                    <h3 class="text-3xl font-bold mt-2">0</h3>
                </div>

                <div class="bg-white p-6 rounded-xl border">
                    <p class="text-gray-500 text-sm">Premium Users</p>
                    <h3 class="text-3xl font-bold mt-2">0</h3>
                </div>

                <div class="bg-white p-6 rounded-xl border">
                    <p class="text-gray-500 text-sm">Avg Session</p>
                    <h3 class="text-3xl font-bold mt-2">0m</h3>
                </div>

                <div class="bg-white p-6 rounded-xl border">
                    <p class="text-gray-500 text-sm">Match Rate</p>
                    <h3 class="text-3xl font-bold mt-2">0%</h3>
                </div>

            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-2 gap-6">

                <div class="bg-white p-6 rounded-xl border">

                    <div class="flex justify-between mb-4">
                        <h3 class="font-semibold">Rate of Return</h3>

                        <select class="border rounded px-2 py-1 text-sm">
                            <option>Last 1 month</option>
                            <option>Last 3 months</option>
                        </select>
                    </div>

                    <div class="h-40 flex items-center justify-center text-gray-400">
                        Chart Placeholder
                    </div>

                </div>

                <div class="bg-white p-6 rounded-xl border">

                    <h3 class="font-semibold mb-4">
                        User Demographics
                    </h3>

                    <div class="h-40 flex items-center justify-center text-gray-400">
                        Chart Placeholder
                    </div>

                </div>

            </div>

        </div>

    </main>

</div>

</body>
</html>