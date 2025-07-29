<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: false }" x-init="darkMode = localStorage.getItem('darkMode') === 'true'" x-bind:class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel App') }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine JS -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Custom Fonts (Optional) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <!-- Styles -->
    <style>
        html {
            font-family: 'Inter', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    @stack('head')
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 antialiased min-h-screen">

    <!-- Navbar (Optional) -->
    {{-- <header class="w-full px-6 py-4 shadow bg-white dark:bg-gray-800 flex justify-between items-center">
        <a href="{{ url('/') }}" class="text-lg font-semibold text-gray-800 dark:text-white">
            {{ config('app.name', 'Laravel') }}
        </a>
        <div>
            @auth
                <span class="text-sm">{{ auth()->user()->email }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline-block ml-3">
                    @csrf
                    <button class="text-sm text-red-600 hover:underline">Logout</button>
                </form>
            @endauth
        </div>
    </header> --}}

    <!-- Page Content -->
    <main class="py-10 px-4">
        <div class="grid grid-cols-12 gap-6 min-h-screen">
            <!-- Left side image -->
            <div class="lg:col-span-6 col-span-12 hidden lg:block">
                <img src="{{ asset('assets/img/authentication/delacc.jpg') }}" alt="Delete Account"
                     class="w-full h-full object-cover">
            </div>
        
            <!-- Right side form -->
            <div class="lg:col-span-6 col-span-12 flex items-center justify-center">
                <div x-data="{ showModal: false }" class="w-full max-w-md px-6 py-10">
        
                    <!-- Intro -->
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Account Settings</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-300">You can deactivate your account below.</p>
                    </div>
        
                    <!-- Delete Button -->
                    <button @click="showModal = true"
                        class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-md text-sm font-medium">
                        Delete My Account
                    </button>
        
                    <!-- Modal -->
                    <div x-show="showModal"
                         class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
                         x-cloak>
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl w-full max-w-md shadow-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                                Confirm Account Deletion
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                                This will deactivate your account. You can recover it later by contacting support.
                            </p>
        
                            <form id="deactivateForm">
                                @csrf
                                <div class="mb-3">
                                    <input type="email" name="email" placeholder="Email Address"
                                           class="w-full border rounded px-3 py-2 text-sm dark:bg-gray-700 dark:text-white"
                                           required>
                                </div>
                                <div class="mb-4">
                                    <input type="password" name="password" placeholder="Password"
                                           class="w-full border rounded px-3 py-2 text-sm dark:bg-gray-700 dark:text-white"
                                           required>
                                </div>
                                <div class="flex items-center justify-between">
                                    <button type="submit"
                                            class="bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700">
                                        Confirm Deactivation
                                    </button>
                                    <button type="button" @click="showModal = false"
                                            class="text-sm text-gray-500 hover:text-gray-700">
                                        Cancel
                                    </button>
                                </div>
                                <div id="responseMessage" class="text-sm mt-3"></div>
                            </form>
                        </div>
                    </div>
        
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script>
        $('#deactivateForm').on('submit', function(e) {
            e.preventDefault();
    
            $.ajax({
                url: "{{ route('user.delete_user_account.action') }}",
                method: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#responseMessage').text(response.message).css('color', 'green');
                    setTimeout(() => {
                        window.location.href = "/";
                    }, 1500);
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON?.message || 'Something went wrong.';
                    $('#responseMessage').text(msg).css('color', 'red');
                }
            });
        });
    </script>
</body>
</html>
