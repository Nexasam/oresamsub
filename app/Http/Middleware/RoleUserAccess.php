<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\LandingPagesSetting;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RoleUserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response|Array
    {
        // dd(auth()->user()->role->role_name);
        return $request->all();

    
        

        if(auth()->user()->role->role_name != 'User'){
            return redirect()->route('access_denied');
        }


        if(! session()->has('whatsapp_support_number')){
            $whatsapp_support = LandingPagesSetting::where('field_name','support_whatsapp_number')->first();
            if($whatsapp_support){
                $whatsapp_support_number = $whatsapp_support->field_details;
            }else{
                $whatsapp_support_number = '08168509044'; //change later
            }
            Session::put('whatsapp_support_number',$whatsapp_support_number);
        }
       

        return $next($request);
    }
}
