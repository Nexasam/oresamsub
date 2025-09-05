<?php

namespace App\Http\Services;

use App\Models\PlanProfitSetting;
use App\Models\User;
use App\Models\ProductPlan;
use App\Models\SiteTemplate;
use App\Models\FundingOption;
use App\Models\UniqueProductPlan;
use App\Models\UserVirtualAccount;
use App\Models\LandingPagesSetting;
use App\Models\FundingOptionBankCodes;

class PlansProfitSettingsService{

    public function getSellingPriceForCustomer(){
        $plans = ProductPlan::with('product_plan_category.network','product_plan_category.product','automation')->get();
        $dataa = [];

        foreach($plans as $key=>$plan){

            if($plan->product_plan_category->product->product_name == 'DATA'){
                $profit_setting = PlanProfitSetting::where('data_size_in_mb',$plan->data_size_in_mb)
                ->where('product_id',$plan->product_plan_category->product->id)
                ->where('validity_in_days',$plan->validity_in_days)
                ->where('network_id',$plan->product_plan_category->network->id)
                ->where('is_social',$plan->is_social)
                ->first(); 

                if($profit_setting){
                    $dataa[$key]['cost_price'] = $plan->cost_price;
                    $userplan_level = auth()->user()->user_plan->plan_level;
                    $profit_level = "profit_$userplan_level";
                    $dataa[$key]['profit_level'] = $userplan_level;
                    $dataa[$key]['profit'] = abs($profit_setting->$profit_level);
                    $dataa[$key]['selling_price'] = $profit_setting->$profit_level +  $plan->cost_price;

                }else{
                    $dataa[$key]['product_name'] = $plan->product_plan_name;
                    $dataa[$key]['profit'] = 'no found';
                    
                }
    
               
            }
          
        }

        return [
            'status' =>1,
            'message' =>$dataa,
        ];
    }
}