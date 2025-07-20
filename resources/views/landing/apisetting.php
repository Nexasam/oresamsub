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
    {{-- <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"> --}}

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
      body { font-family: 'Outfit', sans-serif; }
        .wallet-bg {
            background: radial-gradient(circle at top left, rgba(99, 102, 241, 0.2), transparent 70%),
                        radial-gradient(circle at bottom right, rgba(16, 185, 129, 0.2), transparent 70%);
        }
        .wallet-svg-bg {
            position: relative;
            overflow: hidden;
        }

        .wallet-svg-bg::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 600 600'%3E%3Cdefs%3E%3CradialGradient id='grad' cx='50%25' cy='50%25' r='50%25'%3E%3Cstop offset='0%25' stop-color='%23bfdbfe'/%3E%3Cstop offset='100%25' stop-color='%230e7490'/%3E%3C/radialGradient%3E%3C/defs%3E%3Ccircle cx='300' cy='300' r='300' fill='url(%23grad)' fill-opacity='0.15'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            opacity: 0.3;
            z-index: 0;
        }

        .wallet-svg-bg > * {
            position: relative;
            z-index: 1;
        }
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
            {{-- <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full py-2 px-4 bg-red-600 hover:bg-red-700 text-white rounded text-center">Logout</button>
            </form> --}}

            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
                    <img class="h-8 w-8 rounded-full object-cover" src="https://ui-avatars.com/api/?name=Admin&background=random" alt="Admin">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" class="absolute right-0 bottom-full mb-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg py-1 z-50">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">Profile</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">Settings</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-600">Logout</button>
                    </form>
                </div>
            </div>

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

            <div class="relative mb-8 rounded-lg overflow-hidden shadow-md border border-blue-100 dark:border-gray-700">
                <img src="https://images.unsplash.com/photo-1508780709619-79562169bc64?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80" alt="API Illustration" class="absolute inset-0 w-full h-full object-cover opacity-15 dark:opacity-10">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-blue-100 to-blue-200 dark:from-gray-800 dark:via-gray-900 dark:to-black opacity-30"></div>
                <div class="relative z-10 p-6 md:p-8">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2">API Credentials</h1>
                    <nav class="text-sm text-gray-700 dark:text-gray-300" aria-label="Breadcrumb">
                        <ol class="list-reset flex space-x-2">
                            <li><a href="#" class="hover:underline">Home</a></li>
                            <li><span class="mx-2">/</span></li>
                            <li class="text-gray-500 dark:text-gray-400">API Settings</li>
                        </ol>
                    </nav>
                </div>
            </div>
            

          <!-- API Settings Forms -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Test Environment -->
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Test Environment</h2>
                <form class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">API Key</label>
                        <input type="text" placeholder="Enter Test API Key" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Secret Key</label>
                        <input type="text" placeholder="Enter Test Secret Key" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-white">
                    </div>

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
                </form>
            </div>

            <!-- Live Environment -->
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Live Environment</h2>
                <form class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">API Key</label>
                        <input type="text" placeholder="Enter Live API Key" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Secret Key</label>
                        <input type="text" placeholder="Enter Live Secret Key" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-white">
                    </div>

                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save</button>
                </form>
            </div>
        </div>





            <!-- Recent Transactions Table -->
            <div class="mt-10 bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                    <p>Empty  div</p>
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
