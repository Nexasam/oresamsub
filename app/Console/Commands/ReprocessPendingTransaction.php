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

class ReprocessPendingTransaction extends Command
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
    protected $description = 'Reprocess Pending Transactions, transactions requiring reprocessing';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        if( env('APP_NAME') == 'OresamSub' ){


            $affected_txns = Transaction::with([
                'user',
                'product_plan.product_plan_category.product',
                'product_plan.product_plan_category.network',
                'manual_processing_locker'
            ])
            ->where('set_for_manual', 1)
            ->where('retry_count','<', 5)
            // ->whereRaw('CAST(retry_count AS UNSIGNED) < ?', [5])
            ->limit(5)
            ->get();


            try {
                foreach ($affected_txns as $fetch_transaction) {

                    // Lock transaction to avoid double processing
                    $fetch_transaction->update(['set_for_manual' => 2]); // 2 = processing
    
    
                    // Safety check (in case DB values changed)
                    if ($fetch_transaction->retry_count >= 5) {
                        logger('Max retries reached for txn ID: '.$fetch_transaction->id);
                        $fetch_transaction->update(['set_for_manual' => 0]); // Remove from queue
                        continue;
                    }
    
                    $network_plan_categories_arr = ProductPlanCategory::where('network_id', $fetch_transaction->product_plan->product_plan_category->network->id)
                        ->where('product_id', $fetch_transaction->product_plan->product_plan_category->product->id)
                        ->pluck('id')
                        ->toArray();
    
                    $product_plansss = ProductPlan::with([
                        'automation',
                        'product_plan_category.product',
                        'product_plan_category.network'
                    ])
                    ->where('data_size_in_mb', $fetch_transaction->product_plan->data_size_in_mb)
                    ->where('validity_in_days', $fetch_transaction->product_plan->validity_in_days)
                    ->whereIn('product_plan_category_id', $network_plan_categories_arr)
                    ->where('visibility', 1)
                    ->get();
    
    
                    // $success = false;
    
                    // foreach ($product_plansss as $product_plannn) {
    
                    //     $product_slug = $product_plannn->product_plan_category->product->slug;
    
                    //     if (($fetch_transaction->status == 1 && $fetch_transaction->set_for_manual == 0) || $fetch_transaction->status == 2) {
                    //         logger('Already in good state: '.$fetch_transaction->id);
                    //         $success = true;
                    //         break; // move to next transaction
                    //     }
    
                    //     if ($product_slug != 'data') {
                    //         logger('Applicable on DATA only for now: current slug: '.$product_slug);
                    //         continue; // skip to next plan
                    //     }
    
                    //     $dataa = [
                    //         'phone_number' => $fetch_transaction->phone_number,
                    //         'automation_details' => $product_plannn->automation,
                    //         'automation_id' => $product_plannn->automation->automation_id,
                    //         'network_id' => $product_plannn->product_plan_category->network->id,
                    //         'plan_id' => $product_plannn->id,
                    //         'validatephonenetwork' => 0,
                    //     ];
    
                    //     logger('ee'.json_encode($dataa));
    
                    //     $sell_data = AutomationLogic::initiateDataPurchase($dataa);
    
                    //     $admin_message = $sell_data['admin_message'] ?? 'message';
                    //     $set_for_manual = $sell_data['set_for_manual'] ?? 0;
    
                    //     if ($sell_data['status'] != 1 || $set_for_manual == 1) {
                    //         // Still failed, increment retry count
                    //         $fetch_transaction->update([
                    //             'retry_count' => $fetch_transaction->retry_count + 1,
                    //             'admin_screen_message' => 'cron: automation:'.$product_plannn->automation->automation_name.' '.$admin_message,
                    //             'manually_processed_by' => NULL,
                    //         ]);
                    //         // logger('Still failed: '.$admin_message);
                    //         continue; // try next plan
                    //     }
    
                    //     // Success: Update transaction
                    //     $fetch_transaction->update([
                    //         'status' => 1,
                    //         'retry_count' => $fetch_transaction->retry_count + 1,
                    //         'user_screen_message' => 'Transaction successfully processed',
                    //         'admin_screen_message' => 'MANUAL: automation: '.$product_plannn->automation->automation_name.' by cron, message: '.$admin_message,
                    //         'set_for_manual' => 0, // means reprocessed
                    //         'manually_processed_by' => NULL,
                    //     ]);
    
                    //     $success = true;
                    //     break; // Stop trying more plans for this txn
                    // }





                    $success = false;

                    foreach ($product_plansss as $product_plannn) {
                        $product_slug = $product_plannn->product_plan_category->product->slug;

                        if ($product_slug !== 'data') {
                            logger('Applicable on DATA only for now: current slug: '.$product_slug);
                            continue; // Skip if not data
                        }

                        $dataa = [
                            'phone_number' => $fetch_transaction->phone_number,
                            'automation_details' => $product_plannn->automation,
                            'automation_id' => $product_plannn->automation->automation_id,
                            'network_id' => $product_plannn->product_plan_category->network->id,
                            'plan_id' => $product_plannn->id,
                            'validatephonenetwork' => 0,
                        ];

                        logger('Trying plan: '.json_encode($dataa));

                        $sell_data = AutomationLogic::initiateDataPurchase($dataa);

                        $admin_message = $sell_data['admin_message'] ?? 'message';
                        $set_for_manual = $sell_data['set_for_manual'] ?? 0;

                        if ($sell_data['status'] == 1 && $set_for_manual != 1) {
                            // ✅ Success
                            $fetch_transaction->update([
                                'status' => 1,
                                'retry_count' => $fetch_transaction->retry_count + 1,
                                'user_screen_message' => 'Transaction successfully processed',
                                'admin_screen_message' => 'MANUAL: automation: '.$product_plannn->automation->automation_name.' by cron, message: '.$admin_message,
                                'set_for_manual' => 0,
                                'manually_processed_by' => NULL,
                            ]);

                            $success = true;
                            break; // Stop trying more plans for this txn
                        }

                        // ❌ Failed: Increment retry_count and try next plan
                        $fetch_transaction->update([
                            'retry_count' => $fetch_transaction->retry_count + 1,
                            'admin_screen_message' => 'cron: automation:'.$product_plannn->automation->automation_name.' '.$admin_message,
                            'manually_processed_by' => NULL,
                        ]);

                        logger('Plan failed: '.$admin_message.' | Moving to next plan...');
                    }

                    // After loop
                    if (!$success) {
                        logger('All plans failed for transaction: '.$fetch_transaction->id);
                    }
    
    
    
                    // After checking all alternative plans:
                    if (!$success) {
                        if ($fetch_transaction->retry_count >= 5) {
                            // Max retries reached, remove from queue
                            // $fetch_transaction->update(['set_for_manual' => 0]);
                            // logger('Removed txn '.$fetch_transaction->id.' after max retries.');
                        } else {
                            // Put back in queue for next cron run
                            $fetch_transaction->update(['set_for_manual' => 1]);
                        }
                    }
    
    
                }
            } catch (\Exception $th) {
                logger('Except:'. $th->getMessage().' on page '. $th->getFile().' on line '. $th->getLine());
            }
           


        } 

    }

}
