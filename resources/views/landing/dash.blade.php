<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: false }" x-init="darkMode = localStorage.getItem('darkMode') === 'true'" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- AlpineJS CDN -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Google Font: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased">
<div class="h-screen flex overflow-hidden" x-data="{ sidebarOpen: false }">
    <!-- Off-canvas menu for mobile -->
    <div class="md:hidden">
        <div x-show="sidebarOpen" class="fixed inset-0 z-40 flex">
            <div @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-50"></div>
            <div class="relative flex-1 flex flex-col max-w-[12rem] w-full bg-blue-700 dark:bg-gray-800 text-white transform transition duration-200 ease-in-out border-r" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
                <div class="flex items-center justify-between h-16 px-4 border-b border-blue-800 dark:border-gray-700">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/a/a7/React-icon.svg" alt="Logo" class="h-8">
                    <button @click="sidebarOpen = false" class="text-white focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <nav class="mt-10 space-y-4 px-4 text-base font-semibold">
                    <div>
                        <h4 class="text-lg text-white font-extrabold mb-2">Collections</h4>
                        <a href="#" class="block py-2.5 px-4 rounded hover:bg-blue-600 dark:hover:bg-gray-700 transition">Dashboard</a>
                        <a href="#" class="block py-2.5 px-4 rounded hover:bg-blue-600 dark:hover:bg-gray-700 transition">Transactions</a>
                    </div>
                    <div>
                        <h4 class="text-lg text-white font-extrabold mt-6 mb-2">Disbursements</h4>
                        <a href="#" class="block py-2.5 px-4 rounded hover:bg-blue-600 dark:hover:bg-gray-700 transition">Payouts</a>
                        <a href="#" class="block py-2.5 px-4 rounded hover:bg-blue-600 dark:hover:bg-gray-700 transition">Schedules</a>
                    </div>
                    <div>
                        <h4 class="text-lg text-white font-extrabold mt-6 mb-2">Merchants</h4>
                        <a href="#" class="block py-2.5 px-4 rounded hover:bg-blue-600 dark:hover:bg-gray-700 transition">Merchant List</a>
                        <a href="#" class="block py-2.5 px-4 rounded hover:bg-blue-600 dark:hover:bg-gray-700 transition">Add Merchant</a>
                    </div>
                </nav>
                <div class="mt-auto px-4 py-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full py-2 px-4 bg-red-600 hover:bg-red-700 text-white rounded text-center">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar for desktop -->
    <aside class="hidden md:flex md:flex-shrink-0 bg-blue-700 dark:bg-gray-800 text-white w-64 flex-col border-r border-blue-800 dark:border-gray-700">
        <div class="flex items-center justify-center h-16 shadow-md">
            <img src="https://upload.wikimedia.org/wikipedia/commons/a/a7/React-icon.svg" alt="Logo" class="h-8">
        </div>
        <nav class="mt-10 flex-1 space-y-4 px-4 text-base font-semibold">
            <div>
                <h4 class="text-lg text-white font-extrabold mb-2">Collections</h4>
                <a href="#" class="block py-2.5 px-4 rounded hover:bg-blue-600 dark:hover:bg-gray-700 transition">Dashboard</a>
                <a href="#" class="block py-2.5 px-4 rounded hover:bg-blue-600 dark:hover:bg-gray-700 transition">Transactions</a>
            </div>
            <div>
                <h4 class="text-lg text-white font-extrabold mt-6 mb-2">Disbursements</h4>
                <a href="#" class="block py-2.5 px-4 rounded hover:bg-blue-600 dark:hover:bg-gray-700 transition">Payouts</a>
                <a href="#" class="block py-2.5 px-4 rounded hover:bg-blue-600 dark:hover:bg-gray-700 transition">Schedules</a>
            </div>
            <div>
                <h4 class="text-lg text-white font-extrabold mt-6 mb-2">Merchants</h4>
                <a href="#" class="block py-2.5 px-4 rounded hover:bg-blue-600 dark:hover:bg-gray-700 transition">Merchant List</a>
                <a href="#" class="block py-2.5 px-4 rounded hover:bg-blue-600 dark:hover:bg-gray-700 transition">Add Merchant</a>
            </div>
        </nav>
        <div class="px-4 py-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full py-2 px-4 bg-red-600 hover:bg-red-700 text-white rounded text-center">Logout</button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden ">
        <!-- Top Bar -->
        <header class="flex items-center justify-between px-6 py-4 bg-white dark:bg-gray-800 border-b shadow-sm">
            <div class="flex items-center">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 dark:text-gray-200 focus:outline-none md:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="text-2xl font-semibold text-gray-700 dark:text-gray-100 ml-4">Dashboard</h1>
            </div>
           
            <div class="flex items-center gap-4">
                <span class="text-gray-600 dark:text-gray-300">Welcome, Admin</span>
                <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-100 px-3 py-1 rounded">
                    <span x-text="darkMode ? '☀️ Light' : '🌙 Dark'"></span>
                </button>
            </div>

            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
                    <img class="h-8 w-8 rounded-full object-cover" src="https://ui-avatars.com/api/?name=Admin&background=random" alt="Admin">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg py-1 z-50">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">Settings</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-600">Logout</button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Dashboard Cards -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-10">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-gradient-to-r from-blue-500 to-blue-700 text-white rounded-lg shadow-lg p-4 hover:shadow-2xl transition duration-300">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold">Total Revenue</h3>
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16v16H4V4z"/>
                        </svg>
                    </div>
                    <p class="mt-3 text-2xl font-bold">₦1,245,000</p>
                    <p class="text-xs mt-1 text-blue-100">Compared to last month</p>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-700 text-white rounded-lg shadow-lg p-4 hover:shadow-2xl transition duration-300">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold">New Users</h3>
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="mt-3 text-2xl font-bold">142</p>
                    <p class="text-xs mt-1 text-green-100">Since last week</p>
                </div>
                <div class="bg-gradient-to-r from-purple-500 to-purple-700 text-white rounded-lg shadow-lg p-4 hover:shadow-2xl transition duration-300">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold">Transactions</h3>
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h11M9 21V3"/>
                        </svg>
                    </div>
                    <p class="mt-3 text-2xl font-bold">3,920</p>
                    <p class="text-xs mt-1 text-purple-100">Across all channels</p>
                </div>
            </div>


          <!-- Chart Filter + Placeholder -->
          <div class="mt-10 bg-white dark:bg-gray-800 shadow-md rounded-lg p-4" x-data="{ chartFilter: 'Today', startDate: '', endDate: '' }">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4 md:mb-0">Transaction Trends</h2>
                <div class="flex gap-2 flex-wrap">
                    <template x-for="option in ['Today', 'Yesterday', 'Last 7 Days', 'Last 30 Days', 'Custom']" :key="option">
                        <button
                            @click="chartFilter = option"
                            class="px-3 py-1 rounded text-sm"
                            :class="chartFilter === option ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-700 hover:bg-blue-200'"
                        >
                            <span x-text="option"></span>
                        </button>
                    </template>
                </div>
            </div>
            <div class="mt-4" x-show="chartFilter === 'Custom'">
                <div class="flex gap-4 flex-wrap">
                    <div>
                        <label for="start" class="text-sm text-gray-700 dark:text-gray-200">Start Date</label>
                        <input type="date" x-model="startDate" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                    </div>
                    <div>
                        <label for="end" class="text-sm text-gray-700 dark:text-gray-200">End Date</label>
                        <input type="date" x-model="endDate" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                    </div>
                </div>
            </div>
            <div class="mt-6 h-64 bg-gray-200 dark:bg-gray-700 flex items-center justify-center rounded">
                <p class="text-gray-500 dark:text-gray-300" x-text="chartFilter === 'Custom' ? 'Showing: ' + startDate + ' to ' + endDate : 'Showing: ' + chartFilter"></p>
            </div>
         </div>


            <!-- Recent Transactions Table -->
            <div class="mt-10 bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Recent Transactions</h2>
                    <div class="mt-2 md:mt-0 flex flex-wrap gap-2">
                        <select class="text-sm px-3 py-1 border rounded-md bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                            <option>All</option>
                            <option>Successful</option>
                            <option>Pending</option>
                            <option>Failed</option>
                        </select>
                        <button class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded">Download CSV</button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">2025-07-15</td>
                                <td class="px-6 py-4 whitespace-nowrap">Samuel A.</td>
                                <td class="px-6 py-4 whitespace-nowrap">₦5,000</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Successful</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="#" class="text-blue-600 hover:text-blue-900">View</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">2025-07-14</td>
                                <td class="px-6 py-4 whitespace-nowrap">Oreofe</td>
                                <td class="px-6 py-4 whitespace-nowrap">₦10,000</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="#" class="text-blue-600 hover:text-blue-900">View</a>
                                </td>
                            </tr>
                            <!-- Add more rows as needed -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Copyright Footer -->
            <div class="mt-10 text-center text-sm text-gray-500 dark:text-gray-400">
                &copy; 2025 Nexasam Technologies. All rights reserved.
            </div>
            
        </main>
    </div>
</div>
</body>
</html>
