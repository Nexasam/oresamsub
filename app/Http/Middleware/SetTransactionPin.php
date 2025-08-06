<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\LandingPagesSetting;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetTransactionPin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response|Array
    {


        if(auth()->user()->pin == NULL || auth()->user()->pin == '1234'){
            // dd('pin creation required');
            if(env('APP_NAME') == 'OresamSub'){
            return redirect()->route('ore.set_pin');
            }
            return redirect()->route('user.settings.set_pin');
        }

        return $next($request);
    }
}
