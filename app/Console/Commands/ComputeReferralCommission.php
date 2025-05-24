<?php

namespace App\Console\Commands;

use App\Models\User;
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
        // $start_from_this_date = date('2025-04-01');
        // $today = date('Y-m-d');
        // $yesterday = date('Y-m-d', strtotime('-1 day'));
        // $fetch_yest_successful_txns = Transaction::with('product_plan')->whereDate('created_at','>=',$start_from_this_date)
        //                         ->whereDate('created_at','like','%'.$yesterday.'%')
        //                         ->whereStatus(1)
        //                         ->get();
        // if(count($fetch_yest_successful_txns) > 0){
        //     foreach($fetch_yest_successful_txns as $yest_successful_txn){
        //         $product_plan_comm_feature = //continue here
        //     }
        // }else{
        //     logger('no commissions');
        // }
    }
}
