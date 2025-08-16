<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\SiteTemplate;
use App\Models\FundingOption;
use App\Models\UserVirtualAccount;
use App\Models\LandingPagesSetting;
use App\Http\Services\XixaPayService;
use App\Models\FundingOptionBankCodes;
use App\Http\Services\CrystalPayService;

class DynamicAccountService{

    public function generatedynamic($data){
            $user = $data['user'];
            $amount = $data['amount'];
            $funding_option_id = $data['funding_option_id'];
            $funding_option = FundingOption::where('id',$funding_option_id)->first();
            $user_id = $user->id;
            $email = $user->email;
            $phone_number = $user->phone_number;
            $name = $user->first_name.' '.$user->last_name;
            $biz_id = $funding_option->contract_code;
            $pkey = $funding_option->api_public_key;
            $skey = $funding_option->api_secret_key;
            $bankcode  = 29007; //for now: 20867
            $arr = [
                "email"=>$email,
                "name"=>$name,
                "phoneNumber"=>$phone_number,
                "bankCode"=>["$bankcode"],
                "businessId"=>$biz_id,
                "accountType"=>"dynamic",
                "amount"=>$amount
            ];
            $arrjson = json_encode($arr);


            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.xixapay.com/api/v1/createVirtualAccount',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$arrjson,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$skey,
                'api-key: '.$pkey,
                'Content-Type: application/json'
            ),
            ));
            $response = curl_exec($curl);
            // logger($response);
            curl_close($curl);
            $response_dec = json_decode($response,true);

            if( isset($response_dec['status']) && $response_dec['status'] == 'success' ){
                //success //bankAccounts
                $bankAccounts = $response_dec['bankAccounts'];
                foreach($bankAccounts as $bankAccount){

                    return [
                        'status' => 1,
                        'message' => [
                            'bank_name' =>$bankAccount['bankName'],
                            'bank_code' =>$bankAccount['bankCode'],
                            'account_name' =>$bankAccount['accountName'],
                            'account_email' =>$email,
                            'account_number' =>$bankAccount['accountNumber'],
                            'amount' => $bankAccount['amount'],
                            'expire_at' => $bankAccount['expire_at'],
                            'accountType' => $bankAccount['accountType'],
                        ]
                    ];

                }

            }


            return [
                'status' => -1,
                'message' => 'One or more accounts could not be generated',
            ];
        
    }


    

}