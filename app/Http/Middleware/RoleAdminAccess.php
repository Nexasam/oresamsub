<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleAdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response|Array
    {

    
        //TODO: later
        // $currentUrlWithoutQuery = URL::current();
        // $exceptions_for_admin = ['user.airtime.buy_airtime','user/airtime/buy_data','user.cable_subscription.buy_cable_subscription']
        if(auth()->user()->role->role_name != 'Admin'){
            return redirect()->route('access_denied');
        }
        return $next($request);
    }
}
