<?php

namespace App\Http\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Transaction;
use App\Traits\CheckIfJson;
use App\Models\SiteTemplate;
use App\Models\FundingOption;
use App\Models\UserVirtualAccount;
use App\Models\LandingPagesSetting;
use App\Models\FundingOptionBankCodes;

class BizProfitCalculationService{
    
    use CheckIfJson;

    public function generate_accounts($data){
        $user = $data['user'];
        $user_id = $user->id;
        $first_name = $user->first_name;
        $last_name = $user->last_name;
        $email = $user->email;
        $phone_number = $user->phone_number;
        $bvn = $user->bvn;

        //FOR NOW, RELAX ON THIS
        // if($bvn == NULL || $user->verification_status != 1){
        //     return [
        //         'status' => -1,
        //         'message' => 'BVN is not yet verified'
        //     ];
        // }
    
        $funding_option = FundingOption::where('slug','xixapay')->first();
        if(! $funding_option){
            // logger('na here oh');
            return [
                'status' => -1,
                'message' => 'Funding Option not found'
            ];
           
        }
        $api_secret_key = $funding_option->api_secret_key;
        $api_biz_id = $funding_option->contract_code;
        $api_public_key = $funding_option->api_public_key;
        $biz_bvn = $funding_option->biz_bvn ?? $bvn;

        $bank_codes = FundingOptionBankCodes::where('funding_option_id',$funding_option->id)->get();
        if(count($bank_codes) <= 0 ){
            // logger('xixa1');
            // exit;
            return [
                'status' => -1,
                'message' => 'Sorry you cannot generate virtual accounts at the moment'
            ];
        }

        //continue check from hhere
        $user_virtual_accts_count = UserVirtualAccount::select('id')->where('user_id',$user_id)->where('funding_option_id',$funding_option->id)->count();
        if($user_virtual_accts_count >= count($bank_codes)){
            //do nothing: implication is user has all the complete vas
            // logger('xixa2');
            return [
                'status' => -1,
                'message' => 'Seems you have already generated the accounts'
            ];
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
                        "email"=>$email,
                        "name"=>$first_name.' '.$last_name,
                        "phoneNumber"=>$phone_number,
                        "bankCode"=>["$bank_codee"],
                        "businessId"=>$api_biz_id,
                        "accountType"=>"static",
                        "id_type"=>"bvn",
                        "id_number"=>$biz_bvn
                    ];
                    $arrjson = json_encode($arrr);
                    
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
                        'Authorization: Bearer '.$api_secret_key,
                        'api-key: '.$api_public_key,
                        'Content-Type: application/json'
                    ),
                    ));
                    $response = curl_exec($curl);
                    curl_close($curl);
                    $response_dec = json_decode($response,true);
            
                    if( isset($response_dec['status']) && $response_dec['status'] == 'success' ){
                        //success //bankAccounts
                        $bankAccounts = $response_dec['bankAccounts'];
                        foreach($bankAccounts as $bankAccount){
                            UserVirtualAccount::create([
                                'user_id' => $user_id,
                                'funding_option_id' => $funding_option->id,
                                'funding_slug' => $funding_option->slug,
                                'response_status' =>$response_dec['status'],
                                'bank_name' =>$bankAccount['bankName'],
                                'bank_code' =>$bankAccount['bankCode'],
                                'account_name' =>$bankAccount['accountName'],
                                'account_email' =>$email,
                                'account_number' =>$bankAccount['accountNumber'],
                                'account_reference' => $bankAccount['Reserved_Account_Id'],
                                'bvn' => $biz_bvn
                            ]);
                        }

                        // logger("XIXA VAs GENERATED INDEED FOR $first_name | $user_id | bank code: $bank_codee");

                    }else{
                        // logger("XIXA VA COULD NOT BE GENERATED FOR $first_name | $user_id | bank code: $bank_codee | $response");

                    }
                    sleep(2);
                    //it means its been generated already
                }
            }

            // logger('xixa3');
            return [
                'status' => 1,
                'message' => 'Virtual accounts were generated'
            ];

        }else{
            //this should not run
            // logger('xixa4');
            return [
                'status' => -1,
                'message' => 'Sorry the Virtual Accounts could not be generated'
            ];

        }
  
    }

    public function calculate_profit($data_filter = null){
     
        $start = Carbon::now()->startOfMonth()->toDateString(); // e.g., 2025-08-01
        $end   = Carbon::now()->endOfMonth()->toDateString();   // e.g., 2025-08-31  
        $transactions = Transaction::whereBetween('updated_at', [$start, $end])->where('status',1)->where('set_for_manual',0)->get();

        return [
            'status' => 1,
            'message' => $transactions,
        ];        
    }


    // public function update_actual_plan_cost_price($transaction){
    //     $start = Carbon::now()->startOfMonth()->toDateString(); // e.g., 2025-08-01
    //     $end   = Carbon::now()->endOfMonth()->toDateString();   // e.g., 2025-08-31  
    //     $transactions = Transaction::whereBetween('updated_at', [$start, $end])->where('status',1)->where('set_for_manual',0)->get();
    //     // if( in_array())
    // }


    public function update_transaction_plan_cost_price($date=null,$data_filter = null): string{
        $start = Carbon::now()->startOfMonth()->toDateString(); // e.g., 2025-08-01
        $end   = Carbon::now()->endOfMonth()->toDateString();   // e.g., 2025-08-31  
        $transactions = Transaction::whereBetween('updated_at', [$start, $end])->where('status',1)->where('set_for_manual',0)->get();
       foreach($transactions as $transaction){
           $jsonstatus = $this->isObjectOrArrayJson($transaction->admin_screen_message) ? 'TRUE':'FALSE';
           echo $transaction->admin_screen_message. '========'.$jsonstatus.'<br>'; 
       }
    }



    

}