<?php

namespace App\Http\Services;

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
        $plans = ProductPlan::all();
        $dataa = [];

        foreach($plans as $key=>$plan){
            $dataa[$key]['cost_price'] = $plan->cost_price;

            $userplan_level = auth()->user()->user_plan->plan_level;
            $profit_level = "profit_$userplan_level";
            $dataa[$key]['profit_level'] = $userplan_level;
            $dataa[$key]['profit'] = abs($plan->$profit_level);
            $dataa[$key]['selling_price'] = $plan->$profit_level + $plan->cost_price;
        }

        return [
            'status' =>1,
            'message' =>$dataa,
        ];
    }
}