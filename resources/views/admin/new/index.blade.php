<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — {{ config('app.name') }}</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 text-gray-800 font-sans">
    <!-- HEADER -->
    <header class="bg-slate-800 text-white px-6 py-4 flex justify-between items-center shadow">
        <h1 class="text-lg font-semibold">Admin Dashboard</h1>
        <nav class="space-x-4 text-sm">
            <a href="{{ url('/') }}" class="hover:underline">View Site</a>
            <a href="#" class="hover:underline">Change Password</a>
            <a href="#" class="hover:underline">Logout</a>
        </nav>
    </header>

    <!-- MAIN -->
    <main class="flex flex-col md:flex-row">
        <!-- SIDEBAR -->
        <aside class="w-full md:w-1/4 bg-white border-r border-gray-200 p-5 md:h-screen overflow-y-auto">
            <h2 class="uppercase text-xs text-gray-500 font-semibold mb-3 tracking-wider">Site Administration</h2>

            @php
                $modules = [
                    'Accounts',
                    'Administration',
                    'Auth Token',
                    'Authentication & Authorization',
                    'Notifications',
                    'Sites',
                    'Social Accounts',
                ];
            @endphp

            <div class="space-y-5">
                @foreach($modules as $module)
                    <div>
                        <p class="bg-sky-50 px-3 py-2 rounded-md font-medium text-gray-700 text-sm">{{ $module }}</p>
                        <div class="pl-4 mt-1 flex gap-3 text-sm">
                            <a href="#" class="text-teal-700 hover:underline">Add</a>
                            <a href="#" class="text-yellow-700 hover:underline">Change</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </aside>

        <!-- CONTENT -->
        <section class="flex-1 p-6">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Recent Actions</h2>

                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5">
                    <p class="text-gray-700 text-sm font-medium mb-3">My Actions</p>

                    <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                        <li>Olaademobile — Fund_user</li>
                        <li>Deetel — Fund_user</li>
                        <li>1.0GB — ₦520 (Plan)</li>
                        <li>Custom User — Created</li>
                        <li>Isiaq — Fund_user</li>
                    </ul>
                </div>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="bg-white border-t text-gray-500 text-xs text-center py-3 mt-6">
        © {{ date('Y') }} {{ config('app.name') }} Admin Panel. All rights reserved.
    </footer>
</body>
</html>
