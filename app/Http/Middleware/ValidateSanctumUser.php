<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Setting;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use App\Traits\JsonResponseWrapperMobile;
use Symfony\Component\HttpFoundation\Response;

class ValidateSanctumUser
{
    use JsonResponseWrapperMobile;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
    
        $token_id = request()->bearerToken(); // Get the token from the Authorization header
        
        $token = PersonalAccessToken::findToken($token_id);

        if ($token->tokenable_id !== $request->user_id ) {
            return $this->error('You are not authorized');
        }

        return $next($request);
    }
}
