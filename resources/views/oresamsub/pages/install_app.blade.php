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
            <li>✅ No need to open browser every time</li>
        </ul>

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
    
    // Check if app is already installed
    function isAppInstalled() {
        return (
            window.matchMedia('(display-mode: standalone)').matches ||
            window.navigator.standalone === true
        );
    }
    
    // Update button state
    function updateInstallButton() {
        const btn = document.getElementById('manualInstallBtn');
    
        if (!btn) return;
    
        if (isAppInstalled()) {
            btn.innerHTML = '✅ App Already Installed';
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }
    
    // Capture install prompt when available
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
    
        const btn = document.getElementById('manualInstallBtn');
    
        if (btn && !isAppInstalled()) {
            btn.innerHTML = '📱 Install App';
            btn.disabled = false;
        }
    });
    
    // Detect successful install
    window.addEventListener('appinstalled', () => {
        console.log('PWA installed');
    
        deferredPrompt = null;
    
        const btn = document.getElementById('manualInstallBtn');
    
        if (btn) {
            btn.innerHTML = '✅ App Installed';
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    });
    
    // Initial check
    window.addEventListener('load', () => {
        updateInstallButton();
    });
    
    // Install button click
    document.getElementById('manualInstallBtn').addEventListener('click', async () => {
    
        // Already installed
        if (isAppInstalled()) {
            alert('OresamSub is already installed on this device.');
            return;
        }
    
        // Install prompt available
        if (deferredPrompt) {
    
            deferredPrompt.prompt();
    
            const { outcome } = await deferredPrompt.userChoice;
    
            console.log('Install result:', outcome);
    
            if (outcome === 'accepted') {
                console.log('User accepted install');
            } else {
                console.log('User dismissed install');
            }
    
            deferredPrompt = null;
    
            return;
        }
    
        // Fallback instructions
        const isIOS = /iphone|ipad|ipod/i.test(navigator.userAgent);
    
        if (isIOS) {
            alert(
                'To install OresamSub on iPhone:\n\n' +
                '1. Tap the Share button\n' +
                '2. Select "Add to Home Screen"\n' +
                '3. Tap "Add"'
            );
        } else {
            alert(
                'Installation prompt is not available.\n\n' +
                'If you are using Chrome:\n' +
                'Tap the browser menu (⋮)\n' +
                'Then tap "Install App" or "Add to Home Screen".'
            );
        }
    });
    </script>