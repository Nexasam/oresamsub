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

class UniqueProductPlansService{

    public function updateUniqueIdsInProductPlan(){
        $unique_plans = UniqueProductPlan::all();
        $dataa = [];

        foreach($unique_plans as $key=>$unique_plan){
            //we want to update with the unique plan id
            $network_id = $unique_plan->network_id;
            $product_id = $unique_plan->product_id;
            $datasize = $unique_plan->data_size_in_mb;
            $validity = $unique_plan->validity_in_days;

            $associated_automationplans = ProductPlan::with(['product_plan_category.network','product_plan_category.product','automation'])
            ->where('validity_in_days', $validity)
            ->where('data_size_in_mb', $datasize)
            ->get();


            foreach ($associated_automationplans as $key => $associated_automationplan) {
                $getnetworkid = $associated_automationplan->product_plan_category->network->id ?? null;
                $network_namee = $associated_automationplan->product_plan_category->network->network_name ?? 'nil';
                $productid = $associated_automationplan->product_plan_category->product->id ?? null;
                $sizee = $associated_automationplan->data_size_in_mb;
                $vall = $associated_automationplan->validity_in_days;

                if ($getnetworkid == $network_id && $productid == $product_id && $datasize == $sizee && $validity == $vall) {
                    $dataa[] = [
                        'product_plan' => $associated_automationplan->product_plan_name,
                        'size' => $sizee,
                        'validity' => $vall,
                        'visibility' => $associated_automationplan->visibility,
                        'automation' => $associated_automationplan->automation->automation_name,
                        'network' => $network_namee,
                    ];
                }
            }


        }

        return [
            'status' =>1,
            'message' =>$dataa,
        ];
    }
}