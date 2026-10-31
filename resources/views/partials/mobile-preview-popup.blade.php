@php
    $previewUser = auth()->user();
    $previewApkPath = trim((string) config('mobile.preview.android_apk_path'));
    $privatePreviewExists = $previewApkPath !== ''
        && is_file(storage_path('app/private/'.ltrim($previewApkPath, '/')));
    $remotePreviewExists = filled(config('mobile.preview.android_download_url'));
    $showMobilePreview = $previewUser?->canTestMobilePreview()
        && ($privatePreviewExists || $remotePreviewExists);
    $mobilePreviewVersion = (string) config('mobile.preview.version', '1.0.0');
@endphp

@if ($showMobilePreview)
    <div
        id="mobile-preview-popup"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/70 px-4 py-8 backdrop-blur-sm"
        role="dialog"
        aria-modal="true"
        aria-labelledby="mobile-preview-title"
        data-version="{{ $mobilePreviewVersion }}"
    >
        <div class="w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl dark:bg-gray-900">
            <div class="bg-gradient-to-br from-emerald-800 via-emerald-700 to-green-500 px-6 pb-8 pt-6 text-white">
                <div class="flex items-start justify-between">
                    <div class="grid h-14 w-14 place-items-center rounded-2xl bg-white/15 text-2xl ring-1 ring-white/25">📱</div>
                    <button type="button" data-mobile-preview-close class="rounded-full bg-white/10 px-3 py-2 text-lg hover:bg-white/20" aria-label="Close mobile app invitation">×</button>
                </div>
                <p class="mt-6 text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-100">Private tester access</p>
                <h2 id="mobile-preview-title" class="mt-2 text-2xl font-extrabold leading-tight">Test our new mobile app</h2>
                <p class="mt-2 text-sm leading-6 text-emerald-50">You have early access to the OresamSub Android preview.</p>
            </div>
            <div class="px-6 py-6">
                <div class="rounded-2xl bg-emerald-50 p-4 text-xs leading-5 text-emerald-950 dark:bg-emerald-950 dark:text-emerald-100">
                    Install it on an Android phone and test login, OTP, purchases, wallet and transaction history.
                </div>
                <p class="mt-4 text-xs leading-5 text-gray-500 dark:text-gray-400">Preview version {{ $mobilePreviewVersion }}. Android may ask you to allow installation from your browser.</p>
                <a href="{{ route('mobile.preview.android.download') }}" class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3.5 text-sm font-extrabold text-white hover:bg-emerald-700">
                    ↓ Download Android preview
                </a>
                <button type="button" data-mobile-preview-close class="mt-3 w-full rounded-xl px-4 py-3 text-xs font-bold text-gray-500 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">Maybe later</button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const popup = document.getElementById('mobile-preview-popup');
                if (!popup) return;

                const dismissalKey = 'oresamsub-mobile-preview-dismissed:' + popup.dataset.version;
                if (localStorage.getItem(dismissalKey) === '1') {
                    popup.remove();
                    return;
                }

                popup.querySelectorAll('[data-mobile-preview-close]').forEach(function (button) {
                    button.addEventListener('click', function () {
                        localStorage.setItem(dismissalKey, '1');
                        popup.remove();
                    });
                });
            });
        </script>
    @endpush
@endif
