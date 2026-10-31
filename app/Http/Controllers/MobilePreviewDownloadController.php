<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MobilePreviewDownloadController extends Controller
{
    public function __invoke(Request $request): BinaryFileResponse|RedirectResponse
    {
        abort_unless($request->user()?->canTestMobilePreview(), 403);

        $relativePath = trim((string) config('mobile.preview.android_apk_path'));
        if ($relativePath !== '') {
            $privateRoot = realpath(storage_path('app/private'));
            $apkPath = realpath(storage_path('app/private/'.ltrim($relativePath, '/')));

            abort_unless(
                $privateRoot !== false
                && $apkPath !== false
                && str_starts_with($apkPath, $privateRoot.DIRECTORY_SEPARATOR)
                && is_file($apkPath),
                404,
                'The mobile preview build is not available yet.'
            );

            return response()->download($apkPath, 'OresamSub-preview.apk', [
                'Content-Type' => 'application/vnd.android.package-archive',
                'Cache-Control' => 'private, no-store, max-age=0',
            ]);
        }

        $downloadUrl = trim((string) config('mobile.preview.android_download_url'));
        abort_unless(
            $downloadUrl !== '' && filter_var($downloadUrl, FILTER_VALIDATE_URL) && str_starts_with($downloadUrl, 'https://'),
            404,
            'The mobile preview build is not available yet.'
        );

        return redirect()->away($downloadUrl);
    }
}
