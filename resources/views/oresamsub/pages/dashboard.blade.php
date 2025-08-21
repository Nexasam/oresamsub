<x-app-layout>
  <div class="p-4 space-y-6" x-data="{ loading: false, showBalance: true, copied: false }">
    <!-- Loading Overlay -->
    <template x-if="loading">
      <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="w-10 h-10 border-4 border-white border-t-transparent rounded-full animate-spin"></div>
      </div>
    </template>

    <!-- Admin Impersonation Notice -->
    @if (Auth::guard('admin')->check() && session('impersonated_by'))
      <div class="bg-green-600 text-white p-3 rounded-md flex justify-between items-center">
        <span>You are logged in as {{ Auth::user()->username ?? Auth::user()->email }}</span>
        <a href="{{ route('admin.dashboard') }}"
           class="underline font-semibold hover:text-gray-200 transition-colors"
           @click="loading = true">
          Back to Admin
        </a>
      </div>
    @endif

    <!-- Header -->
    <div class="flex justify-between items-center">
      <h2 class="text-xl font-bold">Hi, {{ Auth::user()->username ?? 'User' }}</h2>
      <button @click="window.location.reload(); loading = true"
              class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition">
        ⟳ Refresh
      </button>
    </div>

    <!-- Wallet Balance -->
    <div class="bg-emerald-500 text-white p-4 rounded-lg shadow-md flex justify-between items-center">
      <div>
        <p class="text-sm">Wallet Balance</p>
        <p class="text-2xl font-bold" x-show="showBalance">₦{{ number_format(Auth::user()->wallet->balance, 2) }}</p>
        <p class="text-2xl font-bold" x-show="!showBalance">****</p>
        <button @click="showBalance = !showBalance" class="mt-2 text-sm underline">Show/Hide</button>
      </div>
      <a href="{{ route('wallet.fund') }}"
         class="bg-white text-emerald-600 px-4 py-2 rounded-md shadow hover:bg-gray-100"
         @click="loading = true">
        Fund Wallet
      </a>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
      <a href="{{ route('airtime.index') }}" class="action-card" @click="loading = true">Airtime</a>
      <a href="{{ route('data.index') }}" class="action-card" @click="loading = true">Data</a>
      <a href="{{ route('electricity.index') }}" class="action-card" @click="loading = true">Electricity</a>
      <a href="{{ route('cable.index') }}" class="action-card" @click="loading = true">Cable</a>
      <a href="{{ route('logout') }}" class="action-card bg-red-600 text-white" @click.prevent="document.getElementById('logout-form').submit(); loading = true">
        Logout
      </a>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
      </form>
    </div>

    <!-- Referral Section -->
    <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg shadow-md">
      <h3 class="font-semibold mb-2">Refer & Earn</h3>
      <div class="flex items-center space-x-2">
        <input type="text" value="{{ route('register', ['ref' => Auth::user()->username]) }}"
               class="w-full border rounded-md px-2 py-1 text-sm dark:bg-gray-700 dark:text-gray-200"
               readonly id="ref-link">
        <button @click="navigator.clipboard.writeText(document.getElementById('ref-link').value); copied=true"
                class="px-3 py-1 bg-emerald-500 text-white rounded-md text-sm hover:bg-emerald-600">
          Copy
        </button>
      </div>
      <div class="flex space-x-3 mt-3">
        <a href="https://wa.me/?text={{ urlencode('Join me on OresamSub: '.route('register', ['ref' => Auth::user()->username])) }}"
           target="_blank" class="social-share flex items-center space-x-1" @click="loading = true">
          <i class="lab la-whatsapp text-green-600"></i>
          <span>WhatsApp</span>
        </a>
        <a href="https://t.me/share/url?url={{ urlencode(route('register', ['ref' => Auth::user()->username])) }}"
           target="_blank" class="social-share flex items-center space-x-1" @click="loading = true">
          <i class="lab la-telegram text-blue-600"></i>
          <span>Telegram</span>
        </a>
        <a href="https://twitter.com/intent/tweet?text={{ urlencode('Join me on OresamSub: '.route('register', ['ref' => Auth::user()->username])) }}"
           target="_blank" class="social-share flex items-center space-x-1" @click="loading = true">
          <i class="lab la-twitter text-sky-500"></i>
          <span>Twitter</span>
        </a>
      </div>
    </div>

    <!-- Community Section -->
    <div class="bg-blue-100 dark:bg-blue-900 p-4 rounded-lg shadow-md">
      <h3 class="font-semibold mb-2">Join Our Community</h3>
      <a href="{{ config('services.community.whatsapp') }}"
         class="block w-full bg-green-600 text-white text-center py-2 rounded-md hover:bg-green-700"
         @click="loading = true">
        Join WhatsApp Group
      </a>
    </div>

    <!-- Transactions Section -->
    <div class="bg-white dark:bg-gray-900 p-4 rounded-lg shadow-md">
      <h3 class="font-semibold mb-3">Recent Transactions</h3>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-200 dark:bg-gray-700">
              <th class="p-2 text-left">Date</th>
              <th class="p-2 text-left">Type</th>
              <th class="p-2 text-left">Amount</th>
              <th class="p-2 text-left">Status</th>
              <th class="p-2">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($transactions as $tx)
              <tr class="border-b dark:border-gray-700">
                <td class="p-2">{{ $tx->created_at->format('d M Y') }}</td>
                <td class="p-2">{{ ucfirst($tx->type) }}</td>
                <td class="p-2">₦{{ number_format($tx->amount, 2) }}</td>
                <td class="p-2">
                  <span class="px-2 py-1 rounded text-xs
                    {{ $tx->status == 'success' ? 'bg-green-200 text-green-800' :
                       ($tx->status == 'pending' ? 'bg-yellow-200 text-yellow-800' : 'bg-red-200 text-red-800') }}">
                    {{ ucfirst($tx->status) }}
                  </span>
                </td>
                <td class="p-2 text-center">
                  <button @click="openModal({{ $tx->id }})"
                          class="text-blue-500 hover:underline text-sm">View</button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="p-3 text-center text-gray-500">No transactions yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</x-app-layout>
