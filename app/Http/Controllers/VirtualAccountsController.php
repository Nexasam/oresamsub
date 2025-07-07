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
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,id',
          ]);
    
        if ($validator->stopOnFirstFailure()->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user_id = $request->user_id ?? '';
        
        if($user_id == ''){
            $data['user'] = auth()->user();
        }else{
            $data['user'] = User::where('id',$user_id)->first();
        }
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
