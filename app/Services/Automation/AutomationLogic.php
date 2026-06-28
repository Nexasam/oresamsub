<?php

namespace App\Services\Automation;

use App\Models\ProductPlan;
use App\Models\VendorAutomationSetting;
use App\Services\Automation\AirtimeAutomation;
use App\Services\Automation\DataAutomation;
use App\Services\Automation\DirectCouponAutomation;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubCableTV;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubElectricity;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubVendAirtime;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubVendData;
use App\Services\Automation\MsOrgGroupAutomation\MsOrgGroupAutomation;
use App\Services\Automation\MsOrgGroupAutomation\SimserverAutomation;
use App\Services\Automation\Nine9javtuAutomation;
use App\Services\Automation\PaultechsAutomation;
use App\Services\Automation\SmeplugAutomation;
use App\Services\Automation\Twins10Automation;
use App\Services\Automation\VtpassAutomation;
use App\Services\Utils\UtilService;

class AutomationLogic{

    // public static function productplanwrapper(){
    //     $network_plan_categories_arr = ProductPlanCategory::where('network_id', $fetch_transaction->product_plan->product_plan_category->network->id)
    //     ->where('product_id', $fetch_transaction->product_plan->product_plan_category->product->id)
    //     ->pluck('id')
    //     ->toArray();

    // $product_plansss = ProductPlan::with([
    //     'automation',
    //     'product_plan_category.product',
    //     'product_plan_category.network'
    // ])
    // ->where('data_size_in_mb', $fetch_transaction->product_plan->data_size_in_mb)
    // ->where('validity_in_days', $fetch_transaction->product_plan->validity_in_days)
    // ->whereIn('product_plan_category_id', $network_plan_categories_arr)
    // ->where('visibility', 1)
    // ->orderByRaw('CAST(cost_price AS UNSIGNED) ASC') // ✅ Sort numerically
    // ->get();


    // // $success = false;

    // // foreach ($product_plansss as $product_plannn) {

    // //     $product_slug = $product_plannn->product_plan_category->product->slug;

    // //     if (($fetch_transaction->status == 1 && $fetch_transaction->set_for_manual == 0) || $fetch_transaction->status == 2) {
    // //         logger('Already in good state: '.$fetch_transaction->id);
    // //         $success = true;
    // //         break; // move to next transaction
    // //     }

    // //     if ($product_slug != 'data') {
    // //         logger('Applicable on DATA only for now: current slug: '.$product_slug);
    // //         continue; // skip to next plan
    // //     }

    // //     $dataa = [
    // //         'phone_number' => $fetch_transaction->phone_number,
    // //         'automation_details' => $product_plannn->automation,
    // //         'automation_id' => $product_plannn->automation->automation_id,
    // //         'network_id' => $product_plannn->product_plan_category->network->id,
    // //         'plan_id' => $product_plannn->id,
    // //         'validatephonenetwork' => 0,
    // //     ];

    // //     logger('ee'.json_encode($dataa));

    // //     $sell_data = AutomationLogic::initiateDataPurchase($dataa);

    // //     $admin_message = $sell_data['admin_message'] ?? 'message';
    // //     $set_for_manual = $sell_data['set_for_manual'] ?? 0;

    // //     if ($sell_data['status'] != 1 || $set_for_manual == 1) {
    // //         // Still failed, increment retry count
    // //         $fetch_transaction->update([
    // //             'retry_count' => $fetch_transaction->retry_count + 1,
    // //             'admin_screen_message' => 'cron: automation:'.$product_plannn->automation->automation_name.' '.$admin_message,
    // //             'manually_processed_by' => NULL,
    // //         ]);
    // //         // logger('Still failed: '.$admin_message);
    // //         continue; // try next plan
    // //     }

    // //     // Success: Update transaction
    // //     $fetch_transaction->update([
    // //         'status' => 1,
    // //         'retry_count' => $fetch_transaction->retry_count + 1,
    // //         'user_screen_message' => 'Transaction successfully processed',
    // //         'admin_screen_message' => 'MANUAL: automation: '.$product_plannn->automation->automation_name.' by cron, message: '.$admin_message,
    // //         'set_for_manual' => 0, // means reprocessed
    // //         'manually_processed_by' => NULL,
    // //     ]);

    // //     $success = true;
    // //     break; // Stop trying more plans for this txn
    // // }





    // $success = false;

    // foreach ($product_plansss as $product_plannn) {
    //     $product_slug = $product_plannn->product_plan_category->product->slug;

    //     if ($product_slug !== 'data') {
    //         logger('Applicable on DATA only for now: current slug: '.$product_slug);
    //         continue; // Skip if not data
    //     }

    //     $dataa = [
    //         'phone_number' => $fetch_transaction->phone_number,
    //         'automation_details' => $product_plannn->automation,
    //         'automation_id' => $product_plannn->automation->id,
    //         'network_id' => $product_plannn->product_plan_category->network->id,
    //         'plan_id' => $product_plannn->id,
    //         'validatephonenetwork' => 0,
    //     ];

    //     logger('Trying plan: '. $product_plannn->product_plan_name.'  automation: '.$product_plannn->automation->automation_name);

    //     $sell_data = AutomationLogic::initiateDataPurchase($dataa);

    //     $admin_message = $sell_data['admin_message'] ?? 'message';
    //     $set_for_manual = $sell_data['set_for_manual'] ?? 0;

    //     if ($sell_data['status'] == 1 && $set_for_manual != 1) {
    //         // ✅ Success
    //         $fetch_transaction->update([
    //             'status' => 1,
    //             'retry_count' => $fetch_transaction->retry_count + 1,
    //             'user_screen_message' => 'Transaction successfully processed',
    //             'admin_screen_message' => 'MANUAL: automation: '.$product_plannn->automation->automation_name.' by cron, message: '.$admin_message,
    //             'set_for_manual' => 0,
    //             'manually_processed_by' => NULL,
    //         ]);

    //         $success = true;
    //         break; // Stop trying more plans for this txn
    //     }

    //     // ❌ Failed: Increment retry_count and try next plan
    //     $fetch_transaction->update([
    //         'retry_count' => $fetch_transaction->retry_count + 1,
    //         'admin_screen_message' => 'cron: automation:'.$product_plannn->automation->automation_name.' '.$admin_message,
    //         'manually_processed_by' => NULL,
    //     ]);

    //     logger('Plan failed with '.$product_plannn->automation->automation_name.': '.$admin_message.' | Moving to next plan...');
    // }
    // }

    public static function initiateDataPurchase($data){

        $phone_number = $data['phone_number'];
        $validate_phone = (new UtilService())->phoneNumberValidation($phone_number);
        $validated_phone_number = $validate_phone['validated_phone_number'];
        $product_plan_id = $data['plan_id'];
        $network_id = $data['network_id'];
        $automation_details = $data['automation_details'];
        $apikey = $automation_details->api_public_key ?? $automation_details->userAutomation->api_key;
        $secret = $automation_details->api_secret_key ?? $automation_details->userAutomation->api_secret;
        $secret = $secret == null ? 'nil':$secret;
        $url = $automation_details->data_url ?? $automation_details->userAutomation->automation->data_url;
        $automation_plan_id = $automation_details->automation_product_plan_id ?? 'nil';
        $automation_id = $automation_details->id ?? $automation_details->userAutomation_id;
        $slug = $automation_details->slug ?? $automation_details->userAutomation->automation->slug;
        $automation_group = $automation_details->automation_group ?? $automation_details->userAutomation->automation->automation_group;
        $validatephonenetwork = $data['validatephonenetwork'] ?? '';

        $data['phone_number'] = $validated_phone_number;
        $data['token'] = $apikey;
        $data['api_key'] = $apikey;
        $data['api_secret'] = $secret;
        $data['url'] = $url;
        $data['automation_id'] = $automation_id;
        $data['automation_plan_id'] = $automation_plan_id;
        $data['coupon'] = $data['coupon'] ?? NULL;
        // logger('automation details in logic: '.json_encode($data));

      
        //dont forget to remove after testing.
        //NEW
        // $test = '1';
        // if($test == '1'){
     
        //     $reference = substr(uniqid(rand(), true), 0, 15);
        //     $plan_details = ProductPlan::with('product_plan_category.network')
        //     ->where('visibility',1)
        //     ->where('id',$product_plan_id)->first();
        //     if(! $plan_details){
        //         return [
        //             'status' => -1,
        //             'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
        //             'admin_message' => 'Wrong plan Id',
        //         ];
        //     }
        //     $vendor_record = VendorAutomationSetting::where('slug','newone')->first();
        //     $dataaa['vendor_record'] = $vendor_record;
        //     $input_phone_number = $validated_phone_number;
        //     $vendor_plan_id = $plan_details->automation_product_plan_id ?? '';
        //     $ported_number = true; //lets make this a default for now
        //     $input_network = $plan_details->product_plan_category->network->network_name; //lets get the network
        //     $buy_data = (new DataAutomation())->buyData($vendor_record,$input_phone_number,$vendor_plan_id,$ported_number,$input_network,$reference);    

        // } else 
        
        if($validate_phone['status'] != 1){
            //something when wrong
            $buy_data['status'] = -1;
            $buy_data['user_message'] = 'This number is not a valid number: '.$phone_number;
            $buy_data['admin_message'] = 'This number is not a valid number: '.$phone_number;
        }
        
        // else if($automation_details->slug == 'megasubplug'){
        //     $buy_data = (new MegaSubVendData($validated_phone_number,$product_plan_id,$validatephonenetwork))->buyData();
        // }

        else if($slug == '9javtu'){
            $buy_data = (new Nine9javtuAutomation($data))->buyData();
        }
        else if($automation_group == 'msorg'){  
            $buy_data = (new MsOrgGroupAutomation($data))->buyData();
        } 
        else if($slug == 'directcoupon'){
            //logic stays here...
            $buy_data = (new DirectCouponAutomation($data))->buyData();   
        }
        else if($slug == 'twins10'){
            //logic stays here...
            $buy_data = (new Twins10Automation($data))->buyData();    
        }
        else if($automation_details->slug == 'foxdatahub'){
            $buy_data = (new FoxdataHubAutomation($data))->buyData();
            logger('foxdatahub ran for data subscription: '.json_encode($buy_data));
        }
        else if($slug == 'paultechs'){
            //logic stays here...
            $buy_data = (new PaultechsAutomation($data))->buyData();    
        } else if($automation_group == 'v2'){
              
                $reference = substr(uniqid(rand(), true), 0, 15);
                
                $plan_details = ProductPlan::with('product_plan_category.network')
                ->where('visibility',1)
                ->where('id',$product_plan_id)->first();
                if(! $plan_details){
                    return [
                    'status' => -1,
                    'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
                    'admin_message' => 'Wrong plan Id',
                    ];
                }
                
                $vendor_record = $automation_details;
                $input_phone_number = $validated_phone_number;
                $vendor_plan_id = $data['provider_plan_id'] ?? $plan_details->automation_product_plan_id ?? '';
                $ported_number = true; //lets make this a default for now
                $input_network = $plan_details->product_plan_category->network->network_name; //lets get the network
                $buy_data = (new DataAutomation())->buyData($vendor_record,$input_phone_number,$vendor_plan_id,$ported_number,$input_network,$reference);    

                // logger('req111: '.json_encode([
                //     'vendor_record' =>$vendor_record,
                //     'input_phone_number' => $input_phone_number,
                //     'input_network' => $input_network,
                //     'vendor_plan_id' => $vendor_plan_id,
                // ]));
                // logger('result111: '.json_encode($buy_data));

        }
        else{
            //this will be like this until other automations are processed
            $buy_data['status'] = env('APP_NAME') == 'OresamSub' ? 1 : -1;
            $buy_data['user_message'] = env('APP_NAME') == 'OresamSub' ? 'Transaction is being processed.' : 'Data processing failed.';
            $buy_data['admin_message'] = 'Data processing failed.';
            $buy_data['set_for_manual'] = env('APP_NAME') == 'OresamSub' ? 1 : 0; // 1 means need to process manually
        }

        //oresamsub for now: the FIX to ensure customers dont see failed transaction... its annnoying and discouraging actually for POS agents/resellers:::: i.e it failed internally
        if($buy_data['status'] == -1 && env('APP_NAME') == 'OresamSub'){
            $buy_data['status'] = 1; //make it successful
            $buy_data['user_message'] = $buy_data['user_message'] ?? 'Transaction is being processed.'; //make it successful for the customer
            $buy_data['set_for_manual'] = 1; // 1 means need to process manually
        }


        //get balance before, balance after, plan amount

        return $buy_data;
    }


    public static function initiateCablePurchase($data){
        
        $automation_details = $data['automation_details'];
        $smart_card_number = $data['smart_card_number'];
        $cable_product_plan_id = $data['plan_id'];
        $total_amount = $data['total_amount'];
        $slots = $data['slots'];
        $validation_customer_name = $data['validation_customer_name'];
        $product_plan_category_name = $data['product_plan_category_name'];
        $user_id = $data['user_id'];
        
        

        if($automation_details->slug == 'megasubplug'){
            $duplication_check = 1;
            $buy_cable_subscription = (new MegaSubCableTV($smart_card_number,$cable_product_plan_id,$total_amount,$validation_customer_name,$slots,$product_plan_category_name, user_id: $user_id))->buyCable();
        }else if($automation_details->slug == 'vtpass'){
            $buy_cable_subscription = (new VtpassAutomation($data))->buyCable();
        }else if($automation_details->slug == 'paultechs'){
            //logic stays here...
            $buy_cable_subscription = (new PaultechsAutomation($data))->buyCable();    
        }else if($automation_details->slug == 'foxdatahub'){
            $buy_cable_subscription = (new FoxdataHubAutomation($data))->buyCableTv();
            logger('foxdatahub ran for cable subscription: '.json_encode($buy_cable_subscription));
        }
        else{
            //this will be like this until other automations are processed
            $buy_cable_subscription['status'] = -1;
            $buy_cable_subscription['user_message'] = 'Cable subscription failed.';
            $buy_cable_subscription['admin_message'] = 'Cable subscription failed.';
        }
        return $buy_cable_subscription;

    }



    public static function initiateElectricityPurchase($data){
        $automation_details = $data['automation_details'];
        $metre_number = $data['metre_number'];
        $data['automation_details'] = $automation_details;
        $plan_id = $data['plan_id'];
        $total_amount = $data['total_amount'];
        $slots = $data['slots'];
        $validation_extra_info = $data['validation_extra_info'];
        $product_plan_category_name = $data['product_plan_category_name'];
        $phone_number = $data['phone_number'];
        $user_id = $data['user_id'];
        $data['token'] = $automation_details->api_public_key;

    
        if($automation_details->slug == 'megasubplug'){
            $duplication_check = 1;
            $buy_electricity_subscription = (new MegaSubElectricity($metre_number,$plan_id,$total_amount,$validation_extra_info,$slots,$product_plan_category_name,$phone_number,user_id: $user_id))->buyElectricity();
        }else if($automation_details->slug == 'vtpass'){
            $buy_electricity_subscription = (new VtpassAutomation($data))->buyElectricity();
        } 
        else{
            //this will be like this until other automations are processed
            $buy_electricity_subscription['status'] = -1;
            $buy_electricity_subscription['user_message'] = 'Electricity subscription failed.';
            $buy_electricity_subscription['admin_message'] = 'Electricity subscription failed.';
            $buy_electricity_subscription['extra_info'] = 'nil';
            $buy_electricity_subscription['token'] = 'nil';
        }
        return $buy_electricity_subscription;
    }


    public static function initiateAirtimePurchase($data){
        $phone_number = $data['phone_number'];
        $validate_phone = (new UtilService())->phoneNumberValidation($phone_number);
        $validated_phone_number = $validate_phone['validated_phone_number'];
        $product_plan_id = $data['plan_id'];
        $amount = $data['amount'];
        $network_id = $data['network_id'];
        $automation_details = $data['automation_details'];
        $token = $automation_details->api_public_key;
        $url = $automation_details->airtime_url;
        $automation_id = $automation_details->id;
        $validatephonenetwork = $data['validatephonenetwork'] ?? '';
        $data['url'] = $url;
        $data['network_id'] = $network_id;

        
   
        if($validate_phone['status'] != 1){
            //something when wrong
            $buy_airtime['status'] = -1;
            $buy_airtime['user_message'] = 'This number is not a valid number: '.$phone_number;
            $buy_airtime['admin_message'] = 'This number is not a valid number: '.$phone_number;
        }
        else if($automation_details->slug == 'megasubplug'){
            $buy_airtime = (new MegaSubVendAirtime($validated_phone_number,$product_plan_id,$amount,$validatephonenetwork))->buyAirtime();
        }
        // else if($automation_details->slug == '9javtu'){
        //     $buy_airtime = (new Nine9javtuAutomation($validated_phone_number,$product_plan_id,$validatephonenetwork,$amount))->buyAirtime();

        // }
        else if($automation_details->automation_group == 'msorg'){
            $data['mobile_number'] = $validated_phone_number;
            $buy_airtime = (new MsOrgGroupAutomation($data))->buyAirtime();
        }
        else if($automation_details->slug == 'simserver'){
            $data['mobile_number'] = $validated_phone_number;
            $reference = uniqid('simserver_');
            $buy_airtime = (new AirtimeAutomation())->buySimServerAirtime($automation_details,$validated_phone_number,$amount,$product_plan_id, true, $reference);
             logger('simserverrr ran: '.json_encode($buy_airtime));
        }
        else if($automation_details->slug == 'smeplug'){
            // logger('smeplug ran: '.json_encode($data));
            $buy_airtime = (new SmeplugAutomation($data))->buyAirtime();
        }
        else{
            //this will be like this until other automations are processed
            $buy_airtime['status'] = env('APP_NAME') == 'OresamSub' ? 1 : -1;
            $buy_airtime['user_message'] = env('APP_NAME') == 'OresamSub' ? 'Transaction is being processed.' : 'Data processing failed.';
            $buy_airtime['admin_message'] = 'Airtime processing failed.';
            $buy_airtime['set_for_manual'] = env('APP_NAME') == 'OresamSub' ? 1 : 0; // 1 means need to process manually
        }

        //oresamsub for now: the FIX to ensure customers dont see failed transaction... its annnoying and discouraging actually for POS agents/resellers:::: i.e it failed internally
        if($buy_airtime['status'] == -1 && env('APP_NAME') == 'OresamSub'){
            $buy_airtime['status'] = 1; //make it successful
            $buy_airtime['user_message'] = 'Transaction is being processed.'; //make it successful for the customer
            $buy_airtime['set_for_manual'] = 1; // 1 means need to process manually
        }


        return $buy_airtime;
    }

    public static function validateCableSubscription($data){
        $automation_details = $data['automation_details'];
        $data['url'] = $automation_details->cable_url;
        $data['token'] = $automation_details->api_public_key;
        if($automation_details->slug == 'foxdatahub'){
            $validate_smartcard_number = (new FoxdataHubAutomation($data))->validateCableTv();
        }else{
            //this will be like this until other automations are processed
            $validate_smartcard_number['status'] = -1;
            $validate_smartcard_number['name'] = 'Name not found.';
            $validate_smartcard_number['address'] = 'Address not found';
            $validate_smartcard_number['data'] = $validate_smartcard_number;
        }
        return $validate_smartcard_number;
    }

    public static function validateElectricitySubscrption($data){
        $automation_details = $data['automation_details'];
        $data['url'] = $automation_details->cable_url;
        $data['token'] = $automation_details->api_public_key;
        if($automation_details->slug == 'foxdatahub'){
            $validate_metre_number = (new FoxdataHubAutomation($data))->validateMetreNumber();
        }else{
            //this will be like this until other automations are processed
            $validate_metre_number['status'] = -1;
            $validate_metre_number['name'] = 'Name not found.';
            $validate_metre_number['address'] = 'Address not found';
            $validate_metre_number['data'] = $validate_metre_number;
        }
        return $validate_metre_number;
    }




}
