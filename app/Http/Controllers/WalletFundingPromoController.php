<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Network;
use App\Models\CouponCode;
use Illuminate\Http\Request;
use App\Models\FundingOption;
use Illuminate\Validation\Rule;
use App\Models\WalletFundingPromo;
use App\Models\ProductPlanCategory;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\WalletFundingPromoController;

class WalletFundingPromoController extends Controller
{
    public function index(){
       
        // dd('asdfsdfsssppppp');
        $networks = Network::get();
        // $userrs = User::select('id','username')->get();
        $wallet_funding_promos = WalletFundingPromo::with(['funding_option','beneficiary'])->latest()->get();
        $funding_options = FundingOption::where('slug','crystal_pay')->latest()->get();
        $data['wallet_funding_promos'] = $wallet_funding_promos;
        $data['funding_options'] = $funding_options;
        // $data['userrs'] = $userrs;

        return view('admin.promos.wallet_funding.index')->with($data);
    }

    public function store(Request $request){

          
                      
            $validator = Validator::make($request->all(), [
              'title' => 'required',
              'promo_metric' => 'required',
              'last_transaction_metrics_date' => 'nullable|date',
              'beneficiary' => 'nullable|string|exists:users,username',
              'funding_option_id' => ['required', 'exists:funding_options,id'],
              'promo_discount_category' => ['required', Rule::in(['flat', 'percent'])],
              'promo_discount_percentage_cap' => 'nullable|integer',
              'promo_value' => 'required|string',
              'slots' => 'required|integer',
            ]);

            $validator->stopOnFirstFailure(); // set the behavior before checking

            if ($validator->fails()) {
              Session::flash('failure', $validator->errors()->first());
              return redirect()->back();
            }

            if($request->promo_metric == 'username'){
              if(!isset($request->beneficiary)){
                Session::flash('failure','Beneficiary username is required');
                return redirect()->back();
               }
               $request->last_transaction_metrics_date = NULL;
               $beneficiary_id = User::where('username',$request->beneficiary)->first()->id;

            }

            if($request->promo_metric == 'last_transaction_before' || $request->promo_metric == 'last_transaction_after'){
               if(!isset($request->last_transaction_metrics_date)){
                Session::flash('failure','Last transaction metrics date is required');
                return redirect()->back();
               }
              $request->beneficiary = NULL;
            }

            if($request->promo_discount_category == 'flat'){
              $request->promo_discount_percentage_cap = NULL;
            }

            if($request->promo_discount_category == 'percent'){
              if($request->promo_value > 70){
                Session::flash('failure','Wallet Funding Promo cannot be more than 70%');
                return redirect()->back();
              }
            }


            $data['title'] = $request->title;
            $data['promo_metric'] = $request->promo_metric;
            $data['last_transaction_metrics_date'] = $request->last_transaction_metrics_date;
            $data['beneficiary'] = $beneficiary_id ?? NULL;
            $data['funding_option_id'] = $request->funding_option_id;
            $data['promo_discount_category'] = $request->promo_discount_category;
            $data['promo_discount_percentage_cap'] = $request->promo_discount_percentage_cap;
            $data['promo_value'] = $request->promo_value;
            $data['slots'] = $request->slots;
            $data['slots_remaining'] = $request->slots;
            $data['status'] = 1;
            $newco = WalletFundingPromo::create($data);

            Session::flash('success','Wallet Funding Promo was successfully added');
            return redirect()->back();

    }

}
