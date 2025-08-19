<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Automation;
use App\Models\ProductPlan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Services\Automation\AutomationLogic;



class ReprocessTransactionController extends Controller
{
    //v1, still needs to be revamped
    public function reprocess_transaction(Request $request){

        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required',
            'transaction_amount' => 'required',
            'plan_id' => 'required|exists:product_plans,id', 
            'automation_id' => 'required|exists:automations,id',
            'automation_name' => 'required',
            'network_id' => 'required'
        ]);
        

        

        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json(['status'=>false, 'message'=>$validator->errors()->first() ]);
        }

        $automation_details = Automation::where('id',$request->automation_id)->first();

        $product_plan = ProductPlan::with('product_plan_category.product')->where('id',$request->plan_id)->first();

        $product_slug = $product_plan->product_plan_category->product->slug;

        $transaction_details = Transaction::where('id',$request->transaction_id)->first();


        if( ($transaction_details  && $transaction_details->status == 1 && $transaction_details->set_for_manual == 0) || $transaction_details->status == 2) {
            return response()->json(['status'=>false,'message'=>'This transaction is already in a good state or its been refunded.' ]);
        }



        if($product_slug  != 'data'){
            return response()->json(['status'=>false,'message'=>'Applicable on DATA only for now' ]);
        }

        $phone_number = $request->phone_number;
        $dataa['phone_number'] = $phone_number;
        $dataa['automation_details'] = $automation_details;
        $dataa['automation_id'] = $request->automation_id;
        $dataa['network_id'] = $request->network_id;
        $dataa['plan_id'] = $request->plan_id;
        $dataa['validatephonenetwork'] = 0;
        $sell_data = AutomationLogic::initiateDataPurchase($dataa);
        $admin_message = $sell_data['admin_message'] ?? 'message';
        $set_for_manual = $sell_data['set_for_manual'] ?? 0;

        if($sell_data['status'] != 1 ||  $set_for_manual == 1){
            //it means it still failed, you can update reprocessing count here
            return response()->json(['status'=>false,'message'=>$sell_data['admin_message'] ]);
        }

        $userinfooo = auth()->user()->username.' '.auth()->user()->email;

        $transaction_details->update([
            'status' => 1,
            'user_screen_message' => 'Transaction successfully processed',
            'admin_screen_message' => 'MANUAL: automation:'.$automation_details->automation_name.' by '.auth()->user()->email.' '.auth()->user()->first_name.'  message:'.$admin_message,
            'set_for_manual' => 0, #means it has been processed
            'manually_processed_by' => $userinfooo,
        ]); 
 
        return response()->json(['status'=>true,'message'=>'success' ]);
    }
}
