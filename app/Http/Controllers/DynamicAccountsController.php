<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FundingOption;
use App\Http\Services\XixaPayService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Services\DynamicAccountService;


class DynamicAccountsController extends Controller
{
    public function index(Request $request){
        //generate dynamic xixa for now
        if(config('app.name') == 'OresamSub'){
            $funding_option = FundingOption::where('slug','xixapay')->first();
            $data['funding_option'] = $funding_option;
            return view('user.dynamic_accounts.index')->with($data);   
        }

        return redirect()->back();
    }

    public function generate_dynamic_account(Request $request){
          $validator = Validator::make($request->all(), [
            'amount_to_fund' => 'required|integer',
            'funding_option_id' => 'required|exists:funding_options,id',
          ]);
          
          if ($validator->stopOnFirstFailure()->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
          }

         

          $data['user'] = auth()->user();
          $data['funding_option_id'] = $request->funding_option_id;
          $data['amount'] = $request->amount_to_fund;
          
          $dynamic_account_info = (new DynamicAccountService())->generatedynamic($data);
          
          return $dynamic_account_info;

          // if ($dynamic_account_info['status'] == 1) {
          //     // Flash message
          //     session()->flash('dynamic_acct_alert', 'Account was succesfully fetched');
              
          //     // Optionally send other info to the view
          //     return redirect()->back()->with([
          //         'dynamicinfo' => $dynamic_account_info['message']
          //     ]);
          // }
          
          return redirect()->back()->withErrors(['Something went wrong.']);
          // Session::flash('failure',$dynamic_account_info['message']);
          // return redirect()->back();
          
    }

   
}
