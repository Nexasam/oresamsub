<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Network;
use App\Models\Commissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommissionsController extends Controller
{
    public function index(){
    
        // dd('commissions here');
        $userid = auth()->id();
        // $start_date = ;
        $end_date = auth()->id();
        $limit = $request->limit ?? 500;
        $data['commissions'] = Commissions::when(auth()->user()->role->role_name == 'Admin',function($query) use ($userid){
            $query->where('beneficiary',$userid);
        })
        ->whereDate('created_at','>=',$start_date)
        ->whereDate('created_at','<=',$end_date)
        ->paginate(100);
        dd($data);
        return view('user.commissions.index')->with($data);
    }

 
}
