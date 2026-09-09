<?php

namespace App\Http\Controllers\Api\v1\Standalone;

use App\Http\Controllers\Controller;
use App\Models\StandaloneWebsite;
use App\Services\Standalone\StandaloneCredentialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CredentialController extends Controller
{
    public function rotate(Request $request, StandaloneCredentialService $credentials): JsonResponse
    {
        /** @var StandaloneWebsite $site */
        $site = $request->attributes->get('standaloneWebsite');
        $issued = $credentials->issueOperationalToken();

        DB::transaction(function () use ($site, $issued): void {
            StandaloneWebsite::whereKey($site->id)->lockForUpdate()->firstOrFail()->update([
                'api_token_digest' => $issued['digest'],
                'api_token_prefix' => $issued['prefix'],
                'api_token_type' => $issued['type'],
                'api_token_must_rotate' => false,
                'api_token_expires_at' => null,
                'api_token_rotated_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'API token rotated successfully.',
            'data' => ['api_token' => $issued['plain_text']],
        ]);
    }
}
