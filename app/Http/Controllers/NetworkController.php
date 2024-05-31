<?php

namespace App\Http\Controllers;

use App\Models\Network;
use Illuminate\Http\Request;

class NetworkController extends Controller
{
    public function index(){
        $data = Network::get();
        return view('admin.networks.index')->with([
            'data' => $data
        ]);
    }

}
