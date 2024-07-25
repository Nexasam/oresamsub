<?php

namespace App\Http\Controllers;

use App\Models\UserVirtualAccount;
use Illuminate\Http\Request;
use App\Models\DynamicAccount;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CrystalPayController extends Controller
{

    

    public function generate_dynamic_account(){
                    
            $firstname = 'Olusola';
            $lastname = 'Adebunmi';
            $email = 'adebsholey4real@gmail.com';
            $bvn = '22225553718';
            $webhookurl = 'https//webhookurl.com';
            $payload = array(
                    "firstname" => $firstname,
                    "lastname" => $lastname,
                    "email" => $email,
                    "dynamic_account_package" => '101',
                    "bvn" => $bvn,
                    "webhookurl" => $webhookurl,
                    "expiresat"  => "60"
            );
            $encoded_payload = json_encode($payload);
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.crystalpay.finance/business/v1/dynamic-account',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$encoded_payload,
            CURLOPT_HTTPHEADER => array(
                'secret_key: 1417307778664652904fd25',
                'Content-Type: application/json',
            ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $result = json_decode($response);
            if(isset($result->status) && $result->status == 'Success' ){
                //successfully generated
                $user_id = auth()->id();
                $bank_name = $result->data->bank_name;
                $account_name = $result->data->account_name;
                $account_number = $result->data->account_number;
                $account_reference = $result->data->account_reference;
                $logDynamicAcctDetails['user_id'] = $user_id;
                $logDynamicAcctDetails['account_name'] = $account_name;
                $logDynamicAcctDetails['bank_name'] = $bank_name;
                $logDynamicAcctDetails['account_number'] = $account_number;
                $logDynamicAcctDetails['account_reference'] = $account_reference;
                $logDynamicAcctDetails['provider_name'] = 'crystalpay';
                DynamicAccount::create($logDynamicAcctDetails);              
                return response()->json(['status'=>'-1', 'message'=>'Your dynamic account was successfully generated', 'data' => $logDynamicAcctDetails ]);

            }else{
                //something went wrong
                return response()->json(['status'=>'-1', 'message'=>'Something went wrong' ]);
            }
        

    }

    //static/permanent acct.
    public function generate_virtual_account(Request $request){
        $validator = Validator::make($request->all(), [
            'pin' => 'required|max:255|exists:users,pin',
            'bvn' => 'required|max:255',
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

          
        //   $fetch_user_accts = UserVirtualAccount::where('user_id',$user_details->id)->where('bank_name','WEMA BANK')->first();
          $fetch_user_acct = UserVirtualAccount::where('user_id',$user_details->id)->first();
        
          if($fetch_user_acct){
            Session::flash('failure','Sorry you already have an account generated: Account number is '.$fetch_user_acct->account_number);
            return redirect()->back();
          }

          
          //call crystalpay generate endpoint: revamp later
                $crystal_pay_key = '1417307778664652904fd25';
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
                "virtual_account_package": "1",  
                "bvn": "'.$bvn.'"
                }',
                CURLOPT_HTTPHEADER => array(
                    'secret_key: '.$crystal_pay_key,
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Cookie: PHPSESSID=tb6qhjmkbpmqcq5fhqla929se5'
                ),
                ));

                $response = curl_exec($curl);

                $response_dec = json_decode($response,true);

                // {
                //     "success":true,
                //     "status":"Success",
                //     "message":"Good Input",
                //     "data":{
                //        "bank_name":"WEMA BANK",
                //        "account_name":"CrystalPay-OreofeRANDAAFRICAAdebu",
                //        "account_email":"oreofe@gmail.com",
                //        "account_number":"7172086120",
                //        "account_reference":"wema_7172086120",
                //        "virtual_account_bank_id":1
                //     }
                //  }
                
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
                        'response_status' =>$response_dec['status'],
                        'bank_name' =>$response_dec['data']['bank_name'],
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
