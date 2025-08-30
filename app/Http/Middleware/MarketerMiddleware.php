<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MarketerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Assuming 'role' or 'is_marketer' determines if user is a marketer
        if (auth()->check() && auth()->user()->is_marketer == 1 || auth()->user()->role->role_name == "Admin") {
            return $next($request);
        }

        // Redirect or abort if not a marketer
        // return redirect('/')->with('error', 'Access denied. You must be a marketer.');
        return redirect()->route('access_denied');
    }
}
