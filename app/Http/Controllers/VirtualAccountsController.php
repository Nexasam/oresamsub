<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\XixaPayService;
use Illuminate\Support\Facades\Session;
use App\Http\Services\VirtualAccountService;

class VirtualAccountsController extends Controller
{
    public function generate(Request $request){
        //generate xixa
        $data['user'] = auth()->user();
        $generate_vas = (new VirtualAccountService())->generate_accounts($data);

        if($generate_vas['status'] == 1){
            Session::flash('success',$generate_vas['message']);
            return redirect()->back();
        }

        Session::flash('failure',$generate_vas['message']);
        return redirect()->back();
        
        //generate crystal
    }
}
