<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Network;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class NetworkController extends Controller
{
    public function index(){
        // Gate::authorize('viewAny', Network::class);
        $data = Network::get();
        return view('admin.networks.index')->with([
            'data' => $data
        ]);
    }

   
}
