<?php

namespace App\Http\Controllers;

use App\Models\UserVirtualAccount;
use Illuminate\Http\Request;

class WalletsController extends Controller
{
    public function webhook(Request $request){
        logger('correct');
        // logger($request->all());
    }

    public function fund_wallet(Request $request){
        // dd('good');
        $user_id = auth()->id();
        $virtual_account = UserVirtualAccount::where('user_id',$user_id)->first();
        $data['virtual_account'] = $virtual_account;
        return view('user.wallet.fund_wallet')->with($data);
    }
}
