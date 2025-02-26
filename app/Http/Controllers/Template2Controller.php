<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Template2Controller extends Controller
{
    //landing page
    public function index(Request $request){
        return view('template2.index');
    }

    //login
    public function login(Request $request){
        return view('template2.auth.login');
    }

    public function signup(Request $request){
        return view('template2.auth.register');
    }

    public function forgot_password(Request $request){
        return view('template2.auth.forgot_password');
    }

    public function dashboard(Request $request){
        return view('template2.user.dashboard');
    }

    public function buy_data(Request $request){
        return view('template2.user.buy_data');
    }

    public function buy_airtime(Request $request){
        return view('template2.user.buy_airtime');
    }

    public function buy_cable(Request $request){
        return view('template2.user.buy_cable');
    }

    public function api_docs(Request $request){
        return view('template2.user.api_docs',['hideNav' => true]);
    }

    
}
