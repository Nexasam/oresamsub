<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMsorgApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $authorization = (string) $request->header('Authorization', '');
        $token = str_starts_with($authorization, 'Token ')
            ? trim(substr($authorization, 6))
            : '';
        $user = filled($token)
            ? User::with('user_plan')->where('api_token', $token)->where('is_deactivated', false)->first()
            : null;

        if (! $user) {
            return response()->json([
                'Status' => 'failed',
                'apiresponse' => 'Unauthorized. Invalid API token.',
                'api_response' => 'Unauthorized. Invalid API token.',
            ], 401);
        }

        $request->attributes->set('api_user', $user);

        return $next($request);
    }
}
