<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = str_replace('Token ', '', $request->header('Authorization'));

        $user = User::with('user_plan')->where('api_token', $token)->first();

        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized. Invalid API token.'
            ], 401);
        }

        // Optionally attach user to request
        $request->merge(['api_user' => $user]);

        return $next($request);
    }
}
