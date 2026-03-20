<?php

namespace App\Http\Services;

use App\Models\FundingOption;
use App\Models\UserVirtualAccount;
use App\Models\FundingOptionBankCodes;

class SecurewavengService{

    public function generate_accounts($data): void{
        $user = $data['user'];
        $user_id = $user->id;
        $first_name = $user->first_name;
        $last_name = $user->last_name;
        $email = $user->email;
        $phone_number = $user->phone_number;
        $phone_number = preg_replace('/[^0-9]/', '', $phone_number);

        $api_bank_codes = [];
    
        $funding_option = FundingOption::where('slug','securewaveng')->first();
        if(! $funding_option){
            // logger('na here oh');
            exit;
        }
        $api_secret_key = $funding_option->api_secret_key;
        $api_public_key = $funding_option->api_public_key;
        $api_contract = $funding_option->contract_code;
        $biz_bvn = $funding_option->biz_bvn ?? $phone_number;
        // $biz_bvn = $funding_option->biz_bvn ?? $phone_number;

        $bank_codes = FundingOptionBankCodes::select('bank_code')
        ->where('visibility_status',1)
        ->where('funding_option_id',$funding_option->id)
        ->pluck('bank_code')->toArray();
        if(! $bank_codes){
            // logger('na here oh2');
            exit;
        }
    

        $user_virtual_accts_count = UserVirtualAccount::select('id')->where('user_id',$user_id)->where('funding_option_id',$funding_option->id)->count();
        if($user_virtual_accts_count >= count($bank_codes)){
            //do nothing: implication is user has all the complete vas
         

        }else if($user_virtual_accts_count < count($bank_codes)){
                
            
          
                foreach($bank_codes as $key=>$bank_code){
                    $bankcode = $bank_code;

                    $check_va = UserVirtualAccount::where('user_id',$user_id)
                    ->where('funding_option_id',$funding_option->id)
                    ->where('bank_code',$bankcode)
                    ->first();

                    if(! $check_va){
                        //retain bank code
                        $api_bank_codes[] = $bank_code;
                    }

                }  
              

                if(count($api_bank_codes) <= 0){
                        //dont generate, it means the customer likely already has all the generated securewaveng accounts.
                        // logger('seems generated already for '.$first_name.' bankcode: '.$bank_code->bank_code);
                 
                }else{

                  
                    //generate
                    $postrequest = [
                        "email"=>$email,
                        "first_name"=>$first_name,
                        "last_name"=>$last_name,
                        "phone_number"=>$phone_number,
                        "bank_code"=>$bank_codes,
                        "business_id"=>$api_contract,
                        "account_type"=>"static",
                        "id_type"=>"bvn",
                        "id_number"=>$biz_bvn
                    ];

                    $encoded_arr = json_encode($postrequest);

                    // logger('test securewaveng: need va generation for this: '.$api_public_key.'--'.$api_secret_key.'---'.json_encode($api_bank_codes).'----'.$encoded_arr);
                    // exit;

                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://securewaveng.com/api/virtual_accounts/generate',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>$encoded_arr,
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: Bearer '.$api_secret_key,
                        'x-api-key: '.$api_public_key
                    ),
                    ));
                    $response = curl_exec($curl);

                    curl_close($curl);

                    $response = curl_exec($curl);
                    // logger("Account generation securewaveng:  $response");exit;
                    $response_dec = json_decode($response,true);
                    
                
                    //NOTE TESTED YET: Always return 200 to caller, but only persist when successful
                    if (
                        isset($response_dec['status']) &&
                        $response_dec['status'] === true &&
                        !empty($response_dec['data']) &&
                        is_array($response_dec['data'])
                    ) {

                        foreach ($response_dec['data'] as $va) {

                            if($va['status'] == 1){
                                UserVirtualAccount::updateOrCreate(
                                    [
                                        // Unique key
                                        'account_reference' => $va['account_reference'],
                                    ],
                                    [
                                        'user_id'           => $user_id,
                                        'funding_option_id' => $funding_option->id,
                                        'funding_slug'      => $funding_option->slug,
                                        'bank_name'         => $va['account_bank'] ?? null,
                                        'bank_code'         => $va['bank_code'] ?? null,
                                        'account_name'      => $va['account_name'] ?? null,
                                        'account_email'     => $va['account_email'] ?? null,
                                        'account_number'    => $va['account_number'] ?? null,
                                        'response_status'   => $va['status'] ?? 1,
                                        'bvn'               => $biz_bvn,
                                    ]
                                );
                            }
                        }

                        logger('Securewave VA Generated Indeed', [
                            'response' => $response_dec['data'],
                            'user_id'  => $user_id,
                            'fname'  => $first_name,
                            'email'  => $email,
                        ]);

                    } else {
                        // Optional: log failure, but DO NOT break the flow
                        logger('VA generation failed or returned invalid payload', [
                            'response' => $response_dec,
                            'user_id'  => $user_id,
                        ]);
                    }

                    sleep(1);
                    //it means its been generated already
                    
                }
            //}
        }else{
            //this should not run
            logger('this should not run');

        }

  
  
    }


    

}