<!-- Profile + Logout Dropdown -->
<div class="flex justify-end relative z-20 px-4">
  <div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="focus:outline-none">
      <div class="w-9 h-9 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center text-gray-800 dark:text-white font-bold">
        {{ strtoupper(auth()->user()->username) }}
      </div>
    </button>

    <!-- Dropdown Menu -->
    <div
      x-show="open"
      @click.away="open = false"
      x-cloak
      class="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 rounded-md shadow-lg overflow-hidden border dark:border-gray-700"
    >
      <div class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 border-b dark:border-gray-600">
        {{ auth()->user()->name }}
      </div>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button
          type="submit"
          class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-600 dark:hover:text-white transition"
        >
          🚪 Logout
        </button>
      </form>
    </div>
  </div>
</div>
