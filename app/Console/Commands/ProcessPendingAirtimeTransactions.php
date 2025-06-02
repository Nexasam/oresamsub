<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Automation;
use App\Models\ProductPlan;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Services\Automation\AutomationLogic;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubVendAirtime;
use App\Services\Automation\MsOrgGroupAutomation\MsOrgGroupAutomation;

class ProcessPendingAirtimeTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-pending-airtime-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending airtime transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
         
            // echo 'e dey work o';
            // logger('e dey work o');
            // exit;
            //only transactions without ref goes through this route
            $pending_transactions = Transaction::with('user','product_plan.product_plan_category.network')
                                    ->where('admin_screen_message','pending_airtime_transaction')
                                    ->where('transaction_category','airtime')
                                    ->where('status',0) 
                                    ->get();

            $blacklisted_array = ['08146181516'];

            if(count($pending_transactions) > 0){
                foreach($pending_transactions as $pending_transaction){
                    $user_balance = $pending_transaction->user->main_wallet;
                    $email = $pending_transaction->user->email;
                    $user_id = $pending_transaction->user_id;
                    $created_at = $pending_transaction->created_at;
                    $phone_number = $pending_transaction->phone_number;
                    $product_plan_id = $pending_transaction->product_plan_id;
                    $amount = $pending_transaction->amount;
                    $discounted_amount = $pending_transaction->discounted_amount ?? $amount;
                    $balance_before = $pending_transaction->balance_before;                    
                    $fetch_duplicate_timestamp = Transaction::where('user_id',$user_id)->where('created_at',$created_at)->count();
                   
                    if( in_array($phone_number,$blacklisted_array) ){
                        $email_sub = substr($email,9).'fraud.com';
                        User::where('id',$user_id)->update([
                            'email' => "fraud_".$email_sub.rand(111111,999999),
                            'password' => Hash::make('passworddy'.rand(11111,99999)),
                            'main_wallet' => 0
                        ]);

                        Transaction::where('user_id',$user_id)
                                    ->where('created_at',$created_at)
                                    ->update([
                                        'status' => -1,
                                        'user_screen_message' => 'Airtime transaction failed.',
                                        'admin_screen_message' => 'User with email: '.$email.' BLACKLISTED... This number is in the list of blacklist: '. $phone_number,
                                    ]);
                        logger('User with email: '.$email.' BLOCKED... Transactions with same timestamps detected for txn: '. $pending_transaction->id);
                                    
                    }else if($fetch_duplicate_timestamp > 1){
                        $email_sub = substr($email,9).'fraud.com';
                        User::where('id',$user_id)->update([
                            'email' => "fraud_".$email_sub.rand(111111,999999),
                            'password' => Hash::make('passworddy'.rand(11111,99999)),
                            'main_wallet' => 0
                        ]);
                        
                        Transaction::where('user_id',$user_id)
                                    ->where('created_at',$created_at)
                                    ->update([
                                        'status' => -1,
                                        'user_screen_message' => 'Airtime transaction failed.',
                                        'admin_screen_message' => 'User with email: '.$email.' BLOCKED... Transactions with same timestamps detected for txn: '. $pending_transaction->id,
                                    ]);
                        logger('User with email: '.$email.' BLOCKED... Transactions with same timestamps detected for txn: '. $pending_transaction->id);
                                    
                    }else if($user_balance < 0){
                        $email_sub = substr($email,9).'fraud.com';
                        User::where('id',$user_id)->update([
                            'email' => "fraud_".$email_sub.rand(111111,999999),
                            'password' => Hash::make('passworddy'.rand(11111,99999)),
                            'main_wallet' => 0
                        ]);
                        $pending_transaction->update([
                            'status' => -1,
                            'user_screen_message' => 'Airtime transaction failed.',
                            'admin_screen_message' => 'User with email: '.$email.' BLOCKED... User has a negative balance for txn: '. $pending_transaction->id
                        ]);
                        logger('User with email: '.$email.' BLOCKED... User has a negative balance for txn: '. $pending_transaction->id);

                   } 
                   
                   else{
                        //carry out the transaction flow now
                        $plan_details = ProductPlan::where('id',$product_plan_id)->first();
                        $automation_id = $plan_details->automation_id ?? NULL;
                        $automation_details = Automation::where('id',$automation_id)->first();   
                                
                        if($plan_details == NULL || $automation_id == NULL || $automation_details == NULL){
                            logger('This should never run actually... something is wrong with plan and or automation setting on txn: '. $pending_transaction->id);
                        }else{
                            $dataa['phone_number'] = $pending_transaction->phone_number;
                            $dataa['automation_details'] = $automation_details;
                            $dataa['network_id'] = $pending_transaction->product_plan->product_plan_category->network->id;
                            $dataa['plan_id'] = $pending_transaction->product_plan_id;
                            $dataa['validatephonenetwork'] = 0;
                            $dataa['amount'] = $amount;
                            $buy_airtime = AutomationLogic::initiateAirtimePurchase($dataa);
                            // logger('AIRTIME SERVICE: '.json_encode($buy_airtime));
                        }
                    }

                }
            }else{
                // echo 'No pending airtime transactions at the moment';
                // logger('No pending airtime transactions at the moment');
            }
    
    }
}
