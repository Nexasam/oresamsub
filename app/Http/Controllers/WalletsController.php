<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FundingOption;
use App\Models\UserVirtualAccount;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class WalletsController extends Controller
{
    public function webhook(Request $request){
        logger('correct');
        // logger($request->all());
    }

    public function index(Request $request){
        // dd('good');
        $user_id = auth()->id();
        $funding_option = FundingOption::with('bank_codes.virtual_user_account_with_bank_code')->where('activation_status',1)->first();
        $data['funding_option'] = $funding_option;

        // $generated_user_virtual_accts_funding_option_id = UserVirtualAccount::where('user_id',auth()->id())->pluck('funding_option_id')->first();
        // $generated_user_virtual_accts_bank_code = UserVirtualAccount::where('user_id',auth()->id())->pluck('bank_code')->first();
        // $data['generated_user_virtual_accts_funding_option_id'] = $generated_user_virtual_accts_funding_option_id;
        // $data['generated_user_virtual_accts_bank_code'] = $generated_user_virtual_accts_bank_code;
        // return $data;
        
        return view('user.wallet.crystal_pay.index')->with($data);
    }
   
    public function fund_wallet(Request $request){
        // dd('good');
        $user_id = auth()->id();
        $virtual_account = UserVirtualAccount::where('user_id',$user_id)->first();
        $data['virtual_account'] = $virtual_account;
        return view('user.wallet.fund_wallet')->with($data);
    }

    public function generate_virtual_account(Request $request){
        $validator = Validator::make($request->all(), [
            'pin' => 'required|digits:4|exists:users,pin',
            'bvn' => 'required|max:255',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email_address' => 'required|max:255',
            'bank_code' => 'required|max:255',
            'funding_option' => 'required|exists:funding_options,id|max:255',
          ]);
          
    
          if ($validator->stopOnFirstFailure()->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
          }
    
          $user_details = auth()->user();
          $pin = $request->pin;


          
          if(! $user_details){
            Session::flash('failure','Record not found');
            return redirect()->back();
          }

          if($user_details->pin != $pin){
            Session::flash('failure','Wrong PIN entered');
            return redirect()->back();
          }


          $first_name = $user_details->first_name;
          $last_name = $user_details->last_name;
          $email = $user_details->email;
          $bvn = $request->bvn;
          $bank_code = $request->bank_code;
          $funding_option_id = $request->funding_option;

         

          
          //   $fetch_user_accts = UserVirtualAccount::where('user_id',$user_details->id)->where('bank_name','WEMA BANK')->first();
          $fetch_user_acct = UserVirtualAccount::where('user_id',$user_details->id)
          ->where('funding_option_id',$funding_option_id)
          ->where('bank_code',$bank_code)
          ->first();
        
          if($fetch_user_acct){
            Session::flash('failure','Sorry you already have an account generated: Account number is '.$fetch_user_acct->account_number);
            return redirect()->back();
          }

          
          //call crystalpay generate endpoint: revamp later
                $wallet_funding = FundingOption::where('id',$funding_option_id)->first();
                $api_key = $wallet_funding->api_secret_key;
                // $api_key = '1417307778664652904fd25';
            

                if($wallet_funding->slug != 'crystal_pay'){
                    Session::flash('failure','Only Crystal pay is currently being activated');
                    return redirect()->back();
                }

                $curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.crystalpay.finance/business/v1/virtual-account',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{
                "firstname": "'.$first_name.'",
                "lastname": "'.$last_name.'",
                "email": "'.$email.'",
                "virtual_account_package": "'.$bank_code.'",  
                "bvn": "'.$bvn.'"
                }',
                CURLOPT_HTTPHEADER => array(
                    'secret_key: '.$api_key,
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Cookie: PHPSESSID=tb6qhjmkbpmqcq5fhqla929se5'
                ),
                ));

                $response = curl_exec($curl);

                $response_dec = json_decode($response,true);

               
                
                if(  isset($response_dec['success']) 
                     && $response_dec['success'] == true
                     &&  isset($response_dec['status']) 
                     &&  $response_dec['status'] == 'Success' 
                     && isset($response_dec['data']['account_number'])
                     &&  $response_dec['data']['account_number'] != ''
                       ){
                    //success
                   
                    UserVirtualAccount::create([
                        'user_id' => $user_details->id,
                        'funding_option_id' => $funding_option_id,
                        'funding_slug' => $wallet_funding->slug,
                        'response_status' =>$response_dec['status'],
                        'bank_name' =>$response_dec['data']['bank_name'],
                        'bank_code' =>$bank_code,
                        'account_name' =>$response_dec['data']['account_name'],
                        'account_email' =>$response_dec['data']['account_email'],
                        'account_number' =>$response_dec['data']['account_number'],
                        'account_reference' => $response_dec['data']['account_reference'],
                        'bvn' => $bvn
                    ]);

                    Session::flash('success','Virtual account was successfully generated');
                    return redirect()->back();    

                }
                
        }

}
