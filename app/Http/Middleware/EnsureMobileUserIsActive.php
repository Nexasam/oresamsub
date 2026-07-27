<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMobileUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if ((bool) $request->user()?->is_deactivated) {
            return response()->json([
                'success' => false,
                'message' => 'This account has been deactivated. Please contact support.',
                'data' => null,
                'meta' => null,
                'errors' => null,
            ], 403);
        }

        if ($request->user() && ! $request->user()->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Please verify your email address before signing in.',
                'data' => null,
                'meta' => null,
                'errors' => null,
            ], 403);
        }

        return $next($request);
    }
}
