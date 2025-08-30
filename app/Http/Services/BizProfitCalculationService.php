<?php

namespace App\Http\Services;

use App\Models\FundingWebhookPayload;
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

    // public function calculate_profitddd($data_filter = null){
     
    //     $start = Carbon::now()->startOfMonth()->toDateString(); // e.g., 2025-08-01
    //     $end   = Carbon::now()->endOfMonth()->toDateString();   // e.g., 2025-08-31  
    //     $transactions = Transaction::whereBetween('updated_at', [$start, $end])->where('status',1)->where('set_for_manual',0)->get();

    //     return [
    //         'status' => 1,
    //         'message' => $transactions,
    //     ];        
    // }


    // public function update_actual_plan_cost_price($transaction){
    //     $start = Carbon::now()->startOfMonth()->toDateString(); // e.g., 2025-08-01
    //     $end   = Carbon::now()->endOfMonth()->toDateString();   // e.g., 2025-08-31  
    //     $transactions = Transaction::whereBetween('updated_at', [$start, $end])->where('status',1)->where('set_for_manual',0)->get();
    //     // if( in_array())
    // }


    public function calculate_profit(): array{
        $start = Carbon::now()->startOfMonth()->toDateString(); // e.g., 2025-08-01
        $end   = Carbon::now()->endOfMonth()->toDateString();   // e.g., 2025-08-31  
     
        $total_txns_count = Transaction::with('product_plan.automation')->whereBetween('updated_at', [$start, $end])
        ->where('transaction_category','data') #data for now
        ->where('status',1)
        ->count();

    
        $transactions = Transaction::with('product_plan.automation')->whereBetween('updated_at', [$start, $end])
        ->where('status',1)
        // ->where('retry_count',0) #was reprocessed once and normally
        ->where('set_for_manual',0)
        ->where('transaction_category','data') #data for now
        ->whereNull('automation_plan_amount') #if not null, we will overwrite what we have
        ->latest('updated_at')
        ->get();

        $total_checked_txns = count($transactions);

        $total_unchecked_txns = $total_txns_count - $total_checked_txns;

        $total_txn_profit = 0;

       foreach($transactions as $key=>$transaction){
           $jsonstatus = $this->isObjectOrArrayJson($transaction->admin_screen_message) ? 'TRUE':'FALSE';
           $automation_details = $transaction->product_plan->automation;
           $non_msorgs = ['samicsub','9javtu','smeplug','directcoupon','megasubplug'];
           $decode_admin_message = json_decode($transaction->admin_screen_message, true);

           
           if($jsonstatus == 'TRUE'){
                if($automation_details->automation_group == 'msorg'){
                    $actual_cost_price = $decode_admin_message['plan_amount'] ?? $transaction->product_plan->cost_price;
                    $balance_before = $decode_admin_message['balance_before'] ?? NULL;
                    $balance_after = $decode_admin_message['balance_after'] ?? NULL;
                }else if ($automation_details->slug == 'paultechs'){
                    $actual_cost_price = $decode_admin_message['cost'] ?? NULL;
                    $balance_before = $decode_admin_message['balance_before'] ?? NULL;
                    $balance_after = $decode_admin_message['balance_after'] ?? NULL;
                }else if( in_array($automation_details->slug ,$non_msorgs)){
                    //fetchh only the costprice on that plan: but you can go further if need be...
                    $balance_before = NULL;
                    $balance_after = NULL;
                    $actual_cost_price = $transaction->product_plan->cost_price ?? NULL;
                }  else{
                    $balance_before = NULL;
                    $balance_after = NULL;
                    $actual_cost_price = $transaction->product_plan->cost_price ?? NULL;
                }
           }else{
              //update has to be done manually, even if not json, you can update plan price
                if( in_array($automation_details->slug ,$non_msorgs)){
                    //fetchh only the costprice on that plan:
                    $balance_before = NULL;
                    $balance_after = NULL;
                    $actual_cost_price = $transaction->product_plan->cost_price ?? NULL;
                }  else{
                    $balance_before = NULL;
                    $balance_after = NULL;
                    $actual_cost_price = $transaction->product_plan->cost_price ?? NULL;
                }
           }


        $purchase_amount = $transaction->discounted_amount ?? $transaction->amount;
        $profit = $purchase_amount - $actual_cost_price;
        $total_txn_profit += $profit;
       
        $data[$key]['automation'] = $automation_details->automation_name;
        $data[$key]['plan'] = $transaction->product_plan->product_plan_name;
        $data[$key]['admin_screen_message'] = $transaction->admin_screen_message;
        $data[$key]['first_automation_balance_before'] = $balance_before;
        $data[$key]['first_automation_balance_after'] = $balance_after;
        $data[$key]['automation_plan_amount'] = $actual_cost_price;
        $data[$key]['purchase_amount'] = $purchase_amount;
        $data[$key]['profit'] = $profit;
        $data[$key]['accumulative_profit'] = $total_txn_profit;
        $data[$key]['profit_status'] = $profit < 0 ? 'NEGATIVE':'POSITIVE';
        $data[$key]['created_at'] = $transaction->updated_at;
        
        //    return $transaction->admin_screen_message. '========'.$balance_after.'======'.$balance_after.'======='.$actual_cost_price.'<br><br><br><hr><hr><hr>'; 
       
      }


      $total_funding_profit = $this->calculate_funding_profit()['total_profit'];
      $total_profit = $total_txn_profit + $total_funding_profit;
      return  [
        'status' => 1,
        'start_date' => $start,
        'end_date' => $end,
        'txn_profit'=>$total_txn_profit,
        'total_funding_profit'=>$total_funding_profit,
        'total_profit'=>$total_profit,
        'total_checked_txns' => $total_checked_txns,
        'total_unchecked_txns' => $total_unchecked_txns,
        'total_txns' => $total_txns_count,
        'data'=> $data,
      ];
    }


    public function calculate_funding_profit(){

        $start = Carbon::now()->startOfMonth()->toDateString(); // e.g., 2025-08-01
        $end   = Carbon::now()->endOfMonth()->toDateString();   // e.g., 2025-08-31      

        $funding_payloads = FundingWebhookPayload::where('funding_status','success')
        ->whereBetween('updated_at', [$start, $end])
        ->latest('updated_at')
        ->get();

        $total_profit = 0;

        foreach($funding_payloads as $funding_payload){
             $decode_payload = json_decode($funding_payload->payload_content, true);
        
            if($funding_payload->funding_slug == 'crystal_pay'){
                $actual_settled = $decode_payload['event_data']['data']['settled'];
                $actual_charged = $decode_payload['event_data']['data']['charged'];
                $actual_paid = $decode_payload['event_data']['data']['paid'];
                $settled = $funding_payload->amount_settled; //what was credited. 
                $profit = $actual_settled - $settled;
                $total_profit += $profit;
            
            }else if($funding_payload->funding_slug == 'xixapay'){
                $actual_settled = $decode_payload['settlement_amount'];
                $actual_charged = $decode_payload['settlement_fee'];
                $actual_paid = $decode_payload['amount_paid'];
                $settled = $funding_payload->amount_settled; //what was credited to user wallet.
                $profit = $actual_settled - $settled;
                $total_profit += $profit;
            
            }
        }   

        return [
            'status' => 1,
            'total_profit' => $total_profit,
        ];

    }

}