<?php

namespace App\Http\Controllers\Api\V1\Standalone;

use App\Http\Controllers\Controller;
use App\Services\Standalone\PublicCallbackUrlValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CallbackController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return $this->response($request);
    }

    public function update(Request $request, PublicCallbackUrlValidator $validator): JsonResponse
    {
        $data = $request->validate(['callback_url' => ['required', 'url', 'starts_with:https://']]);
        if (! $validator->isSafe($data['callback_url'])) {
            return response()->json(['message' => 'The callback URL must resolve to a public HTTPS address.', 'errors' => ['callback_url' => ['Unsafe callback URL.']]], 422);
        }
        $request->attributes->get('standaloneWebsite')->update(['callback_url' => rtrim($data['callback_url'], '/')]);

        return $this->response($request);
    }

    private function response(Request $request): JsonResponse
    {
        $site = $request->attributes->get('standaloneWebsite')->fresh();

        return response()->json(['data' => ['callback_url' => $site->callback_url, 'configured' => filled($site->callback_url), 'webhook_secret_hint' => $site->webhook_secret_hint]]);
    }
}
