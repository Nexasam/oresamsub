<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateBusinessApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        $user = filled($token)
            ? User::with('user_plan')->where('api_token', $token)->first()
            : null;

        if (! $user) {
            logger()->warning('Authentication failed for Business API request.', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_agent' => $request->userAgent(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed. Provide a valid Bearer API token.',
                'data' => null,
                'meta' => null,
                'errors' => ['authentication' => ['The supplied API token is invalid.']],
            ], 401);
        }

        $request->attributes->set('api_user', $user);

        return $next($request);
    }
}
