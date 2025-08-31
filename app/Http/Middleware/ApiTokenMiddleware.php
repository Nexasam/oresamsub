<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class ApiTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = str_replace('Token ', '', $request->header('Authorization'));

        $user = User::where('api_token', $token)->first();

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
