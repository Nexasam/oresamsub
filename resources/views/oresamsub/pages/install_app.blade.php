@extends('oresamsub.layouts.app')

@section('content')
<div class="pt-6 max-w-full mx-auto">

    {{-- Back Button --}}
    <div class="mb-4">
        <a
            href="{{ route('dashboard') }}"
            @click.prevent="showLoader = true; setTimeout(() => window.location.href = '{{ route('dashboard') }}', 500)"
            class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-emerald-600 to-emerald-500 text-white text-sm font-medium shadow"
        >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Dashboard
        </a>
    </div>

    {{-- Heading --}}
    <h2 class="text-2xl font-bold text-center mb-3">
        📱 Install OresamSub App
    </h2>

    <p class="text-center text-gray-600 dark:text-gray-300 mb-8">
        Install OresamSub on your phone for a faster and smoother experience.
    </p>

    {{-- Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 text-center">

        <div class="text-5xl mb-4">🚀</div>

        <h3 class="font-semibold text-lg mb-3">
            Add OresamSub to Your Home Screen
        </h3>

        <ul class="text-left text-sm text-gray-600 dark:text-gray-300 space-y-2 mb-6">
            <li>✅ Faster access to your dashboard</li>
            <li>✅ Works like a mobile app</li>
            <li>✅ Easy airtime, data, cable and electricity purchases</li>
            <li>✅ No need to open your browser every time</li>
            <li>✅ Quick access directly from your phone</li>
        </ul>
        
        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 rounded-lg p-4 text-sm text-left mb-6">
            <p class="font-semibold text-emerald-700 dark:text-emerald-300 mb-2">
                Important:
            </p>
        
            <p class="text-gray-700 dark:text-gray-300">
                After installation, check your phone's app list/menu for <strong>OresamSub</strong>.
                For easier access, you can add it to your Home Screen, Favorites, or App Shortcuts depending on your device.
            </p>
        </div>

        <button
            id="manualInstallBtn"
            class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold"
        >
            Install App
        </button>

        <div class="mt-6 text-sm text-gray-500">
            If the install prompt doesn't appear:
            <br><br>

            <strong>Android Chrome:</strong><br>
            Tap ⋮ → "Install App"
            <br><br>

            <strong>iPhone Safari:</strong><br>
            Tap Share → "Add to Home Screen"
        </div>

    </div>

</div>

<script>
let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
});

document.getElementById('manualInstallBtn').addEventListener('click', async () => {

    if (deferredPrompt) {
        deferredPrompt.prompt();

        const { outcome } = await deferredPrompt.userChoice;
        console.log('Install result:', outcome);

        deferredPrompt = null;
    } else {
        alert(
            "OresamSub may already be installed on your device. If not, the installation prompt is not available right now.\n\n" +
            "After installation, check your phone's app menu for OresamSub and add it to your Home Screen or Favorites for quick access.\n\n" +
            "Android: Open the Chrome menu and tap 'Install App'.\n\n" +
            "iPhone: Tap Share and select 'Add to Home Screen'."
        );

    }
});
</script>
@endsection