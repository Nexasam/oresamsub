<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TemplateSetting
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response|Array
    {

    
        //TODO: later
        if(auth()->user()->role->role_name != 'Admin'){
            return redirect()->route('access_denied');
        }
        return $next($request);
    }
}
