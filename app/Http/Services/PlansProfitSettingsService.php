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
        $plans = ProductPlan::with('product','automation')->get();
        $dataa = [];

        foreach($plans as $key=>$plan){

            $profit_setting = PlanProfitSetting::where('data_size_in_mb',$plan->data_size_in_mb)
            ->where('product_id',$plan->product->id)
            ->where('validity_in_days',$plan->validity_in_days)
            ->where('data_size_in_mb',$plan->data_size_in_mb)
            ->where('is_social',$plan->is_social)
            ->first(); 

            $dataa[$key]['cost_price'] = $plan->cost_price;
            $userplan_level = auth()->user()->user_plan->plan_level;
            $profit_level = "profit_".$userplan_level;
            $dataa[$key]['profit_level'] = $userplan_level;
            $dataa[$key]['profit'] = abs($profit_setting->$profit_level);
            $dataa[$key]['selling_price'] = $profit_setting->$profit_level + 50;
        }

        return [
            'status' =>1,
            'message' =>$dataa,
        ];
    }
}