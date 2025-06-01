<?php

namespace App\Services\Automation;

use App\Models\Automation;
use App\Models\ProductPlan;
use App\Services\Utils\UtilService;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubVendData;
use App\Services\Automation\MsOrgGroupAutomation\MsOrgGroupAutomation;

class AutomationLogic{

    public static function initiateDataPurchase($data){
        $phone_number = $data['phone_number'];
        $validate_phone = (new UtilService())->phoneNumberValidation($phone_number);
        $validated_phone_number = $validate_phone['validated_phone_number'];
        $product_plan_id = $data['plan_id'];
        $network_id = $data['network_id'];
        $token = $data['token'];
        $automation_details = $data['automation_details'];
        $url = $data['url']  ?? '';
        $validatephonenetwork = $data['validatephonenetwork'] ?? '';
   
        if($validate_phone['status'] != 1){
            //something when wrong
            $sell_data['status'] = -1;
            $sell_data['user_message'] = 'This number is not a valid number: '.$phone_number;
            $sell_data['admin_message'] = 'This number is not a valid number: '.$phone_number;
        }
        else if($automation_details->slug == 'megasubplug'){
            $sell_data = (new MegaSubVendData($validated_phone_number,$product_plan_id,$validatephonenetwork))->buyData();
            return $sell_data;
            // logger('MG: '.$sell_data);
        }
        else if($automation_details->automation_group == 'msorg'){
            $data['mobile_number'] = $validated_phone_number;
            $sell_data = (new MsOrgGroupAutomation($data))->buyData();
            return $sell_data;
            // logger('MSORG: '.$sell_data);
        }
        else{
            //this will be like this until other automations are processed
            $sell_data['status'] = -1;
            $sell_data['user_message'] = 'Data processing failed.';
            $sell_data['admin_message'] = 'Data processing failed.';
        }
    }

}
