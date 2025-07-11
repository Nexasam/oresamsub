<?php

namespace App\Services\Automation;

use App\Models\ProductPlan;
use App\Services\Utils\UtilService;
use App\Http\Services\CouponCodeService;
use App\Services\Automation\VtpassAutomation;
use App\Services\Automation\Twins10Automation;
use App\Services\Automation\PaultechsAutomation;
use App\Services\Automation\DirectCouponAutomation;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubCableTV;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubVendData;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubElectricity;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubVendAirtime;
use App\Services\Automation\MsOrgGroupAutomation\MsOrgGroupAutomation;

class AutomationLogic{

    public static function initiateDataPurchase($data){

        $phone_number = $data['phone_number'];
        $validate_phone = (new UtilService())->phoneNumberValidation($phone_number);
        $validated_phone_number = $validate_phone['validated_phone_number'];
        $product_plan_id = $data['plan_id'];
        $network_id = $data['network_id'];
        $automation_details = $data['automation_details'];
        $token = $automation_details->api_public_key;
        $url = $automation_details->data_url;
        $automation_id = $automation_details->id;
        $validatephonenetwork = $data['validatephonenetwork'] ?? '';

        $data['phone_number'] = $validated_phone_number;
        $data['token'] = $token;
        $data['url'] = $url;
        $data['automation_id'] = $automation_id;
        $coupon = $data['coupon'];
        $data['coupon'] = $data['coupon'] ?? NULL;

   
        if($validate_phone['status'] != 1){
            //something when wrong
            $buy_data['status'] = -1;
            $buy_data['user_message'] = 'This number is not a valid number: '.$phone_number;
            $buy_data['admin_message'] = 'This number is not a valid number: '.$phone_number;
        }
        else if($automation_details->slug == 'megasubplug'){
            $buy_data = (new MegaSubVendData($validated_phone_number,$product_plan_id,$validatephonenetwork))->buyData();
        }
        else if($automation_details->automation_group == 'msorg'){  
            $buy_data = (new MsOrgGroupAutomation($data))->buyData();
        } 
        else if($automation_details->slug == 'directcoupon'){
            //logic stays here...
            $buy_data = (new DirectCouponAutomation($data))->buyData();   
        }
        else if($automation_details->slug == 'twins10'){
            //logic stays here...
            $buy_data = (new Twins10Automation($data))->buyData();    
        }
        else if($automation_details->slug == 'paultechs'){
            //logic stays here...
            $buy_data = (new PaultechsAutomation($data))->buyData();    
        }
        else{
            //this will be like this until other automations are processed
            $buy_data['status'] = -1;
            $buy_data['user_message'] = 'Data processing failed.';
            $buy_data['admin_message'] = 'Data processing failed.';
        }


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
        }else{
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
        $plan_id = $data['plan_id'];
        $total_amount = $data['total_amount'];
        $slots = $data['slots'];
        $validation_extra_info = $data['validation_extra_info'];
        $product_plan_category_name = $data['product_plan_category_name'];
        $phone_number = $data['phone_number'];
        $user_id = $data['user_id'];
        $data['amount'] = $total_amount;

    
        if($automation_details->slug == 'megasubplug'){
            $duplication_check = 1;
            $buy_electricity_subscription = (new MegaSubElectricity($metre_number,$plan_id,$total_amount,$validation_extra_info,$slots,$product_plan_category_name,$phone_number,user_id: $user_id))->buyElectricity();
        }else if($automation_details->slug == 'vtpass'){
            $buy_electricity_subscription = (new VtpassAutomation($data))->buyElectricity();
        }else{
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
   
        if($validate_phone['status'] != 1){
            //something when wrong
            $buy_airtime['status'] = -1;
            $buy_airtime['user_message'] = 'This number is not a valid number: '.$phone_number;
            $buy_airtime['admin_message'] = 'This number is not a valid number: '.$phone_number;
        }
        else if($automation_details->slug == 'megasubplug'){
            $buy_airtime = (new MegaSubVendAirtime($validated_phone_number,$product_plan_id,$amount,$validatephonenetwork))->buyAirtime();
        }
        else if($automation_details->automation_group == 'msorg'){
            $data['mobile_number'] = $validated_phone_number;
            $buy_airtime = (new MsOrgGroupAutomation($data))->buyAirtime();
        }
        else{
            //this will be like this until other automations are processed
            $buy_airtime['status'] = -1;
            $buy_airtime['user_message'] = 'Airtime processing failed.';
            $buy_airtime['admin_message'] = 'Airtime processing failed.';
        }


        return $buy_airtime;
    }




}
