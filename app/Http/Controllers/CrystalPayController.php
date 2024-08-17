<?php

namespace App\Http\Controllers;

use App\Models\FundingOption;
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
   
    
}
