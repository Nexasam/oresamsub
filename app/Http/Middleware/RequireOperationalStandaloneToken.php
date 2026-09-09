<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireOperationalStandaloneToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $site = $request->attributes->get('standaloneWebsite');
        if ($site?->api_token_must_rotate || $site?->api_token_type === 'bootstrap') {
            return response()->json(['success' => false, 'message' => 'API token rotation is required before using this endpoint.'], 403);
        }

        return $next($request);
    }
}
