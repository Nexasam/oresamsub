<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrivacyController extends Controller
{
    public function index(Request $request){
       if(env('APP_NAME') == 'Mega-sub' || env('APP_NAME') == 'OresamSub'){
        return view('privacy.megasub');
       }

       return back();
    }
}
