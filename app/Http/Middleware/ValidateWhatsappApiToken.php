<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateWhatsappApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = str_replace('Token ', '', $request->header('Authorization'));

      

        if ($token != '7fK9xQmP2vL8NwR4YtH3cZd6JbS1eUaG5nX9kMfT2qVp8CrW') {
            return response()->json([
                'error' => 'Unauthorized. Invalid API token.....'
            ], 401);
        }

        logger()->info('API Token was great... We are good.');

     

        return $next($request);
    }
}
