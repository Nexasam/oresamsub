<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WalletsController extends Controller
{
    public function webhook(Request $request){
        // dd('correct');
        logger($request->all());
    }
}
