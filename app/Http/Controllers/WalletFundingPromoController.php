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
use App\Models\UserWalletFundingPromo;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class WalletFundingPromoController extends Controller
{
    public function index(){
       
        // dd('asdfsdfsssppppp');
        $networks = Network::get();
        // $userrs = User::select('id','username')->get();
        $wallet_funding_promos = WalletFundingPromo::with(['funding_option','beneficiary'])->latest()->get();
        $funding_options_arr= ['crystal_pay','xixapay'];
        $funding_options = FundingOption::whereIn('slug',$funding_options_arr)->latest()->get();
        $data['wallet_funding_promos'] = $wallet_funding_promos;
        $data['funding_options'] = $funding_options;
        // $data['userrs'] = $userrs;

        return view('admin.promos.wallet_funding.index')->with($data);
    }

    public function index_user(){
       
      // dd('asdfsdfsssppppp');
      $networks = Network::get();
      // $userrs = User::select('id','username')->get();
      $wallet_funding_promos = UserWalletFundingPromo::with(['funding_option','user'])->latest()->get();
      $funding_options_arr= ['crystal_pay','xixapay'];
      $funding_options = FundingOption::whereIn('slug',$funding_options_arr)->latest()->get();
      
      $data['wallet_funding_promos'] = $wallet_funding_promos;
      $data['funding_options'] = $funding_options;

      return view('admin.promos.user_wallet_funding.index')->with($data);
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

    public function store_user(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usernames' => 'required|string',
    
            'funding_option_ids' => 'required|array|min:1',
            'funding_option_ids.*' => 'exists:funding_options,id',
    
            'rate_category' => ['required', Rule::in(['flat', 'percent'])],
            'status' => ['required', Rule::in(['0', '1'])],
            'capped_at' => 'nullable|integer',
            'value' => 'required|numeric',
            'min_funding_amount' => 'nullable|numeric|min:0',
        ]);
    
        $validator->stopOnFirstFailure();
    
        if ($validator->fails()) {
            Session::flash('failure', $validator->errors()->first());
            return redirect()->back();
        }
    
        /** Percent validation */
        if ($request->rate_category === 'percent' && $request->value > 20) {
            Session::flash('failure', 'Wallet Funding Promo cannot be more than 20%');
            return redirect()->back();
        }
    
        /** Normalize usernames */
        $usernames = collect(explode(',', $request->usernames))
            ->map(fn ($u) => trim($u))
            ->filter()
            ->unique()
            ->values();
    
        if ($usernames->isEmpty()) {
            Session::flash('failure', 'At least one valid username is required');
            return redirect()->back();
        }
    
        /** Normalize funding options */
        $fundingOptionIds = collect($request->funding_option_ids)
            ->map(fn ($id) => (string) $id)
            ->unique()
            ->values()
            ->toArray();
    
        $created = 0;
        $updated = 0;
    
        foreach ($usernames as $username) {
    
            $user = User::where('username', $username)->first();
            if (! $user) {
                continue; // skip invalid usernames
            }
    
            $data = [
                'user_id' => $user->id,
                'funding_option_ids' => $fundingOptionIds,
                'rate_category' => $request->rate_category,
                'value' => $request->value,
                'capped_at' => $request->rate_category === 'percent'
                    ? $request->capped_at
                    : $request->value,
                'status' => $request->status,
                'min_funding_amount' => $request->min_funding_amount ?? 3000,
            ];
    
            /** Overwrite per user */
            $promo = UserWalletFundingPromo::where('user_id', $user->id)->first();
    
            if ($promo) {
                $promo->update($data);
                $updated++;
            } else {
                UserWalletFundingPromo::create($data);
                $created++;
            }
        }
    
        Session::flash(
            'success',
            "{$created} promo(s) created, {$updated} promo(s) updated successfully"
        );
    
        return redirect()->back();
    }
    

    public function update_user($id, Request $request){
              $validator = Validator::make($request->all(), [
                  'username' => 'required|string|exists:users,username',

                  'funding_option_ids' => 'required|array|min:1',
                  'funding_option_ids.*' => 'exists:funding_options,id',

                  'status' => ['required', Rule::in(['0', '1'])],
                  'rate_category' => ['required', Rule::in(['flat', 'percent'])],
                  'capped_at' => 'nullable',
                  'value' => 'required|numeric',
                  'min_funding_amount' => 'nullable|numeric|min:0',
              ]);

              $validator->stopOnFirstFailure();

              if ($validator->fails()) {
                  Session::flash('failure', $validator->errors()->first());
                  return redirect()->back();
              }

              /** Fetch promo */
              $promo = UserWalletFundingPromo::find($id);

              if (! $promo) {
                  Session::flash('failure', 'This record was not found');
                  return redirect()->back();
              }

              /** Percent validation */
              if ($request->rate_category === 'percent' && $request->value > 70) {
                  Session::flash('failure', 'Wallet Funding Promo cannot be more than 70%');
                  return redirect()->back();
              }

              /** Resolve user */
              $user = User::where('username', $request->username)->first();
              if (! $user) {
                  Session::flash('failure', 'Customer not found');
                  return redirect()->back();
              }

              /** Normalize funding options */
              $fundingOptionIds = collect($request->funding_option_ids)
                  ->map(fn ($id) => (string) $id)
                  ->unique()
                  ->values()
                  ->toArray();

              /** Update */
              $promo->update([
                  'user_id' => $user->id,
                  'funding_option_ids' => $fundingOptionIds,
                  'rate_category' => $request->rate_category,
                  'value' => $request->value,
                  'capped_at' => $request->rate_category === 'percent'
                      ? $request->capped_at
                      : $request->value,
                  'status' => $request->status,
                  'min_funding_amount' => $request->min_funding_amount ?? 3000,
              ]);

              Session::flash('success', 'User Wallet Funding Promo was successfully updated');
              return redirect()->back();
    }


 

    // public function update_user($id,Request $request){
         
    //   // dd($request->all());

    //   $validator = Validator::make($request->all(), [
    //     'username' => 'nullable|string|exists:users,username',
    //     'funding_option_id' => ['required', 'exists:funding_options,id'],
    //     'status' => ['required', Rule::in(['0', '1'])],
    //     'rate_category' => ['required', Rule::in(['flat', 'percent'])],
    //     'capped_at' => 'nullable|integer',
    //     'value' => 'required|string'
    //   ]);

    //   $validator->stopOnFirstFailure(); // set the behavior before checking

    //   if ($validator->fails()) {
    //     Session::flash('failure', $validator->errors()->first());
    //     return redirect()->back();
    //   }

     

    //   if(! UserWalletFundingPromo::where('id',$id)->first()){
    //     Session::flash('failure','This record was not found');
    //     return redirect()->back();
    //   }

    //   if(! isset($request->username)){  
    //       Session::flash('failure','Beneficiary username is required');
    //       return redirect()->back();
    //   }

     
    //   if($request->rate_category == 'percent'){
    //     if($request->value > 70){
    //       Session::flash('failure','Wallet Funding Promo cannot be more than 70%');
    //       return redirect()->back();
    //     }
    //   }

    //   $get_user = User::where('username',$request->username)->first();
    //   if(! $get_user){
    //     Session::flash('failure','Customer not found');
    //     return redirect()->back();
    //   }

    //   $user_id = $get_user->id;

    //   $data['user_id'] = $user_id ?? NULL;
    //   $data['funding_option_id'] = $request->funding_option_id;
    //   $data['rate_category'] = $request->rate_category;
    //   $data['value'] = $request->value;
    //   $data['capped_at'] = $request->capped_at;
    //   $data['status'] = $request->status;
    //   $newco = UserWalletFundingPromo::where('id',$id)->update($data);

    //   Session::flash('success','User Wallet Funding Promo was successfully updated');
    //   return redirect()->back();

    // }

}
