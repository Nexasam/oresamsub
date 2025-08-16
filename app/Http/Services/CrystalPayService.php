<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\SiteTemplate;
use App\Models\FundingOption;
use App\Models\UserVirtualAccount;
use App\Models\LandingPagesSetting;
use App\Models\FundingOptionBankCodes;

class CrystalPayService{

    public function generate_accounts($data): void{
        $user = $data['user'];
        $user_id = $user->id;
        $first_name = $user->first_name;
        $last_name = $user->last_name;
        $email = $user->email;
        $phone_number = $user->phone_number;
    
        $funding_option = FundingOption::where('slug','crystal_pay')->first();
        if(! $funding_option){
            // logger('na here oh');
            exit;
        }
        $api_key = $funding_option->api_secret_key;
        $biz_bvn = $funding_option->biz_bvn ?? $phone_number;

        $bank_codes = FundingOptionBankCodes::where('funding_option_id',$funding_option->id)->get();
        if(! $bank_codes){
            // logger('na here oh2');
            exit;
        }

        $user_virtual_accts_count = UserVirtualAccount::select('id')->where('user_id',$user_id)->where('funding_option_id',$funding_option->id)->count();
        if($user_virtual_accts_count >= count($bank_codes)){
            //do nothing: implication is user has all the complete vas
        }else if($user_virtual_accts_count < count($bank_codes)){
            //means user has none or less than all the vas
            //your logic is here.
            foreach($bank_codes as $bank_code){
                //first check for the user if he has that va
               
                $check_va = UserVirtualAccount::where('user_id',$user_id)->where('bank_code',$bank_code->bank_code)->first();
                if($check_va){
                        //dont generate
                        // logger('seems generated already for '.$first_name.' bankcode: '.$bank_code->bank_code);
                 
                }else{
                    //generate
                    $bank_codee = $bank_code->bank_code;
                    $arrr = [
                        "firstname"=>$first_name,
                        "lastname"=>$last_name,
                        "email"=>$email,
                        "virtual_account_package"=>$bank_codee,  
                        "bvn"=>$biz_bvn
                    ];
                    // return $arrr;
                    $arrjson = json_encode($arrr);
                    // logger("CP data passed: $arrjson");
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
                    CURLOPT_POSTFIELDS =>$arrjson,
                    CURLOPT_HTTPHEADER => array(
                        'secret_key: '.$api_key,
                        'Content-Type: application/json',
                        'Accept: application/json'
                    ),
                    ));

                    $response = curl_exec($curl);
                    // logger("Account generation crystalpay:  $response");
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
                            'user_id' => $user_id,
                            'funding_option_id' => $funding_option->id,
                            'funding_slug' => $funding_option->slug,
                            'response_status' =>$response_dec['status'],
                            'bank_name' =>$response_dec['data']['bank_name'],
                            'bank_code' =>$bank_codee,
                            'account_name' =>$response_dec['data']['account_name'],
                            'account_email' =>$response_dec['data']['account_email'],
                            'account_number' =>$response_dec['data']['account_number'],
                            'account_reference' => $response_dec['data']['account_reference'],
                            'bvn' => $biz_bvn
                        ]);
                        // logger("VA GENERATED INDEED FOR $first_name | $user_id | bank code: $bank_codee");
                    }else{
                        // logger("VA COULD NOT BE GENERATED FOR $first_name | $user_id | bank code: $bank_codee | $response");

                    }
                    sleep(1);

                    //it means its been generated already
                }
            }
        }else{
            //this should not run
            logger('this should not run');

        }

        // for now return nothin
        // return [
        //     'status' => 1,
        //     'message' => 'Virtual accounts was successfully generated'
        // ]; 
  
    }


    

}