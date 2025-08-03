<!DOCTYPE html>
<html lang="en" class="font-sans bg-gray-100 text-gray-800">
<head>
  <meta charset="UTF-8">
  <title>{{ $title ?? 'OresamSub' }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- DARK MODE PREVENT FLASH -->
  <script>
    if (localStorage.getItem('theme') === 'dark' ||
        (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.classList.add('dark');
      document.documentElement.classList.add('bg-gray-900');
    } else {
      document.documentElement.classList.remove('dark');
      document.documentElement.classList.add('bg-gray-100');
    }
  </script>

  <!-- Tailwind CSS + Alpine.js -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { darkMode: 'class' };
  </script>
  <script src="https://unpkg.com/alpinejs@3.x.x" defer></script>

  <style>
    [x-cloak] { display: none !important; }
  </style>
</head>

<body
  x-data="themeToggle()"
  x-init="init(); $watch('showLoader', val => document.body.classList.toggle('overflow-hidden', val))"
  class="min-h-screen text-gray-800 dark:text-gray-100"
>

  <!-- App Container -->
  <div class="max-w-md mx-auto border border-gray-300 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden bg-white dark:bg-gray-900">

    <!-- Header -->
    <div class="p-4 flex justify-between items-center border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
      <h1 class="text-xl font-bold">OresamSub</h1>
      <button @click="toggle()" class="text-xl">
        <span x-text="darkMode ? '☀️' : '🌙'"></span>
      </button>
    </div>

    <!-- Main Content -->
    <main class="px-4 pt-4 pb-28 min-h-[calc(100vh-96px)]">
      @yield('content')
    </main>

    <!-- Bottom Navigation -->
    <nav class="fixed bottom-0 inset-x-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-lg">
      <div class="max-w-md mx-auto flex justify-around py-2 text-xs font-medium text-gray-700 dark:text-gray-200">
        @foreach ([
          ['icon' => '🏠', 'label' => 'Dashboard'],
          ['icon' => '📞', 'label' => 'Airtime'],
          ['icon' => '📶', 'label' => 'Data'],
          ['icon' => '⚡', 'label' => 'Electricity']
        ] as $item)
          <a 
            href="{{ route('ore.dashboard') }}"
            @click.prevent="showLoader = true; setTimeout(() => window.location.href = '{{ route('ore.dashboard') }}', 5000)"
            class="flex flex-col items-center hover:text-blue-600 dark:hover:text-blue-400"
          >
            <div class="text-xl">{{ $item['icon'] }}</div>
            <span>{{ $item['label'] }}</span>
          </a>
        @endforeach
      </div>
    </nav>

  </div>

  <!-- Global Loader -->
  <div x-show="showLoader" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-white dark:bg-gray-900 bg-opacity-80">
    <div class="animate-spin h-12 w-12 border-4 border-blue-500 border-t-transparent rounded-full"></div>
  </div>

  <!-- Alpine Logic -->
  <script>
    function themeToggle() {
      return {
        darkMode: false,
        showLoader: false,

        init() {
          this.darkMode = document.documentElement.classList.contains('dark');
        },

        toggle() {
          this.darkMode = !this.darkMode;
          localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
          document.documentElement.classList.toggle('dark', this.darkMode);
        }
      }
    }
  </script>

</body>
</html>
