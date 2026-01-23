<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Commissions;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ComputeReferralCommission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compute-referral-commission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compute Referral Commission';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //start adding commission from this date:
        //ONLY FOR DATA TRANSACTIONS FOR NOW
     
                // $start_from_this_date = date('2025-07-08');
                $today = date('Y-m-d');
                // $yesterday = date('Y-m-d', strtotime('-1 day'));
                // $twodaysback = date('Y-m-d', strtotime('-2 day'));

                $fetch_successful_txns = Transaction::with(['user.user_plan','product_plan'])
                                        // ->whereDate('created_at','>=',$start_from_this_date)
                                        ->whereDate('created_at','like','%'.$today.'%') #use yesterday after testing
                                        ->where('transaction_category', 'data')
                                        ->whereStatus(1)
                                        ->get();

                if(count($fetch_successful_txns) > 0){
                    foreach($fetch_successful_txns as $successful_txn){
                        $txn_id = $successful_txn->id;
                        $user = $successful_txn->user;
                        $user_id = $successful_txn->user_id;
                        $upline_id = $successful_txn->user->upline_id;
                        // $plan_level = $successful_txn->user->user_plan->plan_level;
                        // $commission_field = 'user_level_'.$plan_level.'_commission';
                        // $expected_commission = $successful_txn->product_plan->$commission_field;//continue here
                        $expected_commission = $successful_txn->upline_commission;

                        if( $upline_id != NULL){
                            //insert
                            //check if the user has an upline:
                            $user_upline_check = User::where('id',$upline_id)->first();
                            if($user_upline_check && $user_upline_check->id != $user_id){
                                    //upline exists
                                    if(! Commissions::where('transaction_id',$txn_id)->first() ){
                                        $commissionssss = Commissions::create([
                                            'status' => 0,
                                            'payout_status' => 0,
                                            'transaction_id' => $txn_id,
                                            'commission' => $expected_commission,
                                            'beneficiary' => $user_upline_check->id,
                                            'transaction_by' => $user_id,
                                        ]);
                                    }
                                    

                                    // logger('Commission added for:'.json_encode([
                                    //     'transaction_id' => $successful_txn->id,
                                    //     'commission' => $expected_commission,
                                    //     'beneficiary' => $user_upline_check->id,
                                    //     'transaction_by' => $successful_txn->user_id,
                                    // ]));
                            }else{
                                logger('upline not found for user or user is found as upline of self: '.$user->username.' with txn id: '.$txn_id);
                            }
                            
                        }else{
                            logger('commission is likely 0 or no upline for user: '.$user->username.' with txn id: '.$txn_id);
                        }
                    }
                }else{
                    logger('no commissions recorded');
                }
       

    }
}
