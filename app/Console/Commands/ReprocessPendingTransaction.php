<?php

namespace App\Console\Commands;

use App\Http\Services\TransactionService;
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

        

            $affected_txns = Transaction::with([
                'user',
                'product_plan.product_plan_category.product',
                'product_plan.product_plan_category.network',
                'manual_processing_locker'
            ])
            ->where('transaction_category','=','data')
            ->where('set_for_manual', 1)
            ->where('retry_count','<', value: 8)
            // ->whereRaw('CAST(retry_count AS UNSIGNED) < ?', [5])
            ->limit(5)
            ->get();


            try {
                foreach ($affected_txns as $fetch_transaction) {

                    $amount_deducted = $fetch_transaction->discounted_amount ?? $fetch_transaction->amount;
                    $refund_reason = NULL;
                   

                    // Lock transaction to avoid double processing
                    $fetch_transaction->update(['set_for_manual' => 2]); // 2 = processing
    
    
                    // Safety check (in case DB values changed)
                    if ($fetch_transaction->retry_count >= 8) {
                        logger('Max retries reached for txn ID: '.$fetch_transaction->id);
                        $fetch_transaction->update(['set_for_manual' => 1]); // Remove from queue
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
                    ->orderByRaw('CAST(cost_price AS UNSIGNED) ASC') // ✅ Sort numerically
                    ->get();
    
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
                            'automation_id' => $product_plannn->automation->id,
                            'network_id' => $product_plannn->product_plan_category->network->id,
                            'plan_id' => $product_plannn->id,
                            'validatephonenetwork' => 0,
                        ];

                        logger('Trying plan: '. $product_plannn->product_plan_name.'  automation: '.$product_plannn->automation->automation_name);

                        $sell_data = AutomationLogic::initiateDataPurchase($dataa);

                        $admin_message = $sell_data['admin_message'] ?? 'message';
                        $set_for_manual = $sell_data['set_for_manual'] ?? 0;

                        if ($sell_data['status'] == 1 && $set_for_manual != 1) {
                            // ✅ Success
                            $fetch_transaction->update([
                                'status' => 1,
                                'retry_count' => $fetch_transaction->retry_count + 1,
                                'user_screen_message' => 'Transaction successfully processed',
                                'extra_info' => 'MANUAL: automation: '.$product_plannn->automation->automation_name.' by cron, message: '.$admin_message,
                                'set_for_manual' => 0,
                                'manually_processed_by' => NULL,
                                'reprocess_automation_id' => $product_plannn->automation->id
                            ]);
                            $success = true;
                            break; // Stop trying more plans for this txn
                        }

                        // ❌ Failed: Increment retry_count and try next plan
                        $fetch_transaction->update([
                            'retry_count' => $fetch_transaction->retry_count + 1,
                            'extra_info' => 'reprocessed by automation:'.$product_plannn->automation->automation_name.' '.$admin_message,
                            'manually_processed_by' => NULL,
                        ]);

                        logger('Plan failed with '.$product_plannn->automation->automation_name.': '.$admin_message.' | Moving to next plan...');
                    }

                    // After loop
                    // if (!$success) {
                    //     logger('All plans failed for transaction: '.$fetch_transaction->id);
                    // }
    
    
    
                    // After checking all alternative plans: LETS DO THIS FOR NOW... PEACE OF MIND IS KEY
                    if (!$success) {
                        // if ($fetch_transaction->retry_count >= 8) {
                        //     // Max retries reached, remove from queue
                        //     // $fetch_transaction->update(['set_for_manual' => 0]);
                        //     // logger('Removed txn '.$fetch_transaction->id.' after max retries.');
                        //     $fetch_transaction->update(['set_for_manual' => 1]);

                        // } else {
                        // All plans processed, refund now...
                        // $fetch_transaction->update(['set_for_manual' => 0, 'status' => 2]);
                            logger('THIS RAAAAAN FOR '. $fetch_transaction->id);
                            $refund_reason = 'All vendor options have been exhausted...';
                            (new TransactionService())->transaction_refund($fetch_transaction,$amount_deducted,$refund_reason,'cron');
                        // }
                    }
    
    
                }
            } catch (\Exception $th) {
                logger('Except:'. $th->getMessage().' on page '. $th->getFile().' on line '. $th->getLine());
            }
           

    }

}
