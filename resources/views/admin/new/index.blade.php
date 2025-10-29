<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - {{ config('app.name') }}</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- Header -->
    <header class="bg-teal-700 text-white py-3 px-6 flex justify-between items-center">
        <h1 class="text-lg font-semibold">Welcome to {{ config('app.name') }} Admin Panel</h1>
        <div class="text-sm space-x-4">
            <a href="{{ url('/') }}" class="hover:underline">View Site</a>
            <a href="#" class="hover:underline">Change Password</a>
            <a href="{{ route('logout') ?? '#' }}" class="hover:underline">Logout</a>
        </div>
    </header>

    <!-- Main Section -->
    <main class="flex">

        <!-- Sidebar -->
        <aside class="w-1/3 md:w-1/4 bg-white border-r border-gray-200 p-4 overflow-y-auto h-screen">
            <h2 class="text-gray-600 uppercase text-xs font-semibold mb-3">Site Administration</h2>

            <!-- Each section -->
            <div class="space-y-4">
                <div>
                    <p class="bg-sky-100 px-2 py-1 text-sm font-medium text-gray-800 rounded">Accounts</p>
                    <div class="pl-3 mt-1 flex gap-3 text-sm">
                        <a href="#" class="text-teal-700 hover:underline">Add</a>
                        <a href="#" class="text-yellow-700 hover:underline">Change</a>
                    </div>
                </div>

                <div>
                    <p class="bg-sky-100 px-2 py-1 text-sm font-medium text-gray-800 rounded">Administration</p>
                    <div class="pl-3 mt-1 flex gap-3 text-sm">
                        <a href="#" class="text-teal-700 hover:underline">Add</a>
                        <a href="#" class="text-yellow-700 hover:underline">Change</a>
                    </div>
                </div>

                <div>
                    <p class="bg-sky-100 px-2 py-1 text-sm font-medium text-gray-800 rounded">Auth Token</p>
                    <div class="pl-3 mt-1 flex gap-3 text-sm">
                        <a href="#" class="text-teal-700 hover:underline">Add</a>
                        <a href="#" class="text-yellow-700 hover:underline">Change</a>
                    </div>
                </div>

                <div>
                    <p class="bg-sky-100 px-2 py-1 text-sm font-medium text-gray-800 rounded">Authentication & Authorization</p>
                    <div class="pl-3 mt-1 flex gap-3 text-sm">
                        <a href="#" class="text-teal-700 hover:underline">Add</a>
                        <a href="#" class="text-yellow-700 hover:underline">Change</a>
                    </div>
                </div>

                <div>
                    <p class="bg-sky-100 px-2 py-1 text-sm font-medium text-gray-800 rounded">Notifications</p>
                    <div class="pl-3 mt-1 flex gap-3 text-sm">
                        <a href="#" class="text-teal-700 hover:underline">Add</a>
                        <a href="#" class="text-yellow-700 hover:underline">Change</a>
                    </div>
                </div>

                <div>
                    <p class="bg-sky-100 px-2 py-1 text-sm font-medium text-gray-800 rounded">Sites</p>
                    <div class="pl-3 mt-1 flex gap-3 text-sm">
                        <a href="#" class="text-teal-700 hover:underline">Add</a>
                        <a href="#" class="text-yellow-700 hover:underline">Change</a>
                    </div>
                </div>

                <div>
                    <p class="bg-sky-100 px-2 py-1 text-sm font-medium text-gray-800 rounded">Social Accounts</p>
                    <div class="pl-3 mt-1 flex gap-3 text-sm">
                        <a href="#" class="text-teal-700 hover:underline">Add</a>
                        <a href="#" class="text-yellow-700 hover:underline">Change</a>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Content Area -->
        <section class="flex-1 p-6">
            <h2 class="text-gray-700 font-semibold text-lg mb-4">Recent Actions</h2>

            <div class="bg-white border border-gray-200 rounded p-4 shadow-sm">
                <p class="text-gray-700 text-sm mb-2">My Actions</p>
                <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                    <li>Olaademobile — Fund_user</li>
                    <li>Deetel — Fund_user</li>
                    <li>1.0GB — ₦520 (Plan)</li>
                    <li>Custom User — Created</li>
                    <li>Isiaq — Fund_user</li>
                </ul>
            </div>
        </section>

    </main>

</body>
</html>
