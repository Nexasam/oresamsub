<?php

namespace App\Http\Middleware;

use App\Models\StandaloneWebsite;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateStandalone
{
    public function handle(Request $request, Closure $next): Response
    {
        $site = StandaloneWebsite::findByApiToken((string) $request->bearerToken());
        if (! $site) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        if ($site->status !== 'active') {
            return response()->json(['message' => 'Standalone access is suspended.'], 403);
        }
        if ($site->api_token_type === 'bootstrap' && $site->api_token_expires_at?->isPast()) {
            return response()->json(['success' => false, 'message' => 'This bootstrap API token has expired. Request a new one.'], 401);
        }
        $request->attributes->set('standaloneWebsite', $site);

        return $next($request);
    }
}
