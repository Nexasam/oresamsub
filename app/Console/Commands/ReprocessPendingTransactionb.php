<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Setting;
use App\Models\Automation;
use App\Models\ProductPlan;
use App\Models\Transaction;
use App\Models\ConfigSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\ProductPlanCategory;
use Illuminate\Support\Facades\Mail;
use App\Mail\PendingTransactionNotification;
use App\Services\Automation\AutomationLogic;

class ReprocessPendingTransactionb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reprocess-pending-transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reprocess Pending Transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        if( env('APP_NAME') == 'OresamSub' ){

            // $affected_txns = Transaction::where('set_for_manual',1)->limit(5)->get();  #5 txns per call
            $affected_txns = Transaction::with(['user','product_plan.product_plan_category.network','manual_processing_locker'])
            ->where('set_for_manual',1)
            ->limit(5)
            ->get();  #5 txns per call

            foreach ($affected_txns as $fetch_transaction) {

                $network_plan_categories_arr = ProductPlanCategory::where('network_id',$fetch_transaction->product_plan->product_plan_category->network->id)
                ->where('product_id',$fetch_transaction->product_plan->product_plan_category->product->id)
                ->pluck('id')
                ->toArray();
                
                $product_plansss = ProductPlan::with('automation','product_plan_category.product')->where('data_size_in_mb',$fetch_transaction->product_plan->data_size_in_mb)
                ->where('validity_in_days',$fetch_transaction->product_plan->validity_in_days)
                ->whereIn('product_plan_category_id',$network_plan_categories_arr)
                ->where('visibility',1)
                ->get();

                //loop through the txn.
                foreach ($product_plansss as $product_plannn) {
                

                                        // $automation_details = Automation::where('id',$product_plannn->automation_id)->first();

                                        $product_slug = $product_plannn->product_plan_category->product->slug;
                                
                                        if( ($fetch_transaction  && $fetch_transaction->status == 1 && $fetch_transaction->set_for_manual == 0) || $fetch_transaction->status == 2) {
                                            // return response()->json(['status'=>false,'message'=>'This transaction is already in a good state or its been refunded.' ]);
                                            logger('already in good state'.$fetch_transaction->id);
                                        }else if($product_slug  != 'data'){
                                            logger('Applicable on DATA only for now: current slug:'.$product_slug);

                                        }else{

                                            $phone_number = $fetch_transaction->phone_number;
                                            $dataa['phone_number'] = $phone_number;
                                            $dataa['automation_details'] = $product_plannn->automation;
                                            $dataa['automation_id'] = $product_plannn->automation->automation_id;
                                            $dataa['network_id'] = $product_plannn->network_id;
                                            $dataa['plan_id'] = $product_plannn->id;
                                            $dataa['validatephonenetwork'] = 0;
                                            $sell_data = AutomationLogic::initiateDataPurchase($dataa);
                                            $admin_message = $sell_data['admin_message'] ?? 'message';
                                            $set_for_manual = $sell_data['set_for_manual'] ?? 0;
                                    
                                            if($sell_data['status'] != 1 ||  $set_for_manual == 1){
                                                //it means it still failed, you can update reprocessing count here
                                                logger('still failed:'.$admin_message);

                                            }else{
                                            
                                                $fetch_transaction->update([
                                                    'status' => 1,
                                                    'retry_count' => DB::raw('retry_count + 1'),
                                                    'user_screen_message' => 'Transaction successfully processed',
                                                    'admin_screen_message' => 'MANUAL: automation:'.$product_plannn->automation->automation_name.' by cron,  message:'.$admin_message,
                                                    'set_for_manual' => 0, #means it has been reprocessed
                                                    'manually_processed_by' => NULL,
                                                ]); 
                                                continue 2; #once you get this, move to next txn
                                            }
                                        }
                }

                
            }

        } 

    }

}
