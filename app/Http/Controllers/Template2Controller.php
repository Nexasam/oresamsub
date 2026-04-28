<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminColorSetting;
use App\Models\LandingPagesSetting;

class Template2Controller extends Controller
{
    private function colorVars(): array
    {
        $primary   = AdminColorSetting::where('color_name', 'site_primary_color')->first();
        $secondary = AdminColorSetting::where('color_name', 'site_secondary_color')->first();
        return [
            'site_primary_color'   => $primary->color_value   ?? '#5a66f2',
            'site_secondary_color' => $secondary->color_value ?? '#f97316',
        ];
    }

    //landing page
    public function index(Request $request){
        return view('template2.index', $this->colorVars());
    }

    //login
    public function login(Request $request){
        return view('template2.auth.login', $this->colorVars());
    }

    public function signup(Request $request){
        return view('template2.auth.register', $this->colorVars());
    }

    public function forgot_password(Request $request){
        return view('template2.auth.forgot_password', $this->colorVars());
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
