<?php

namespace App\Http\Controllers;

use App\Models\ProductPlan;
use Illuminate\Http\Request;
use App\Models\UniqueProductPlan;

class UniqueProductPlansController extends Controller
{
    public function index(){
        // $generalproductplans = UniqueProductPlan::all();
        $generalproductplans = UniqueProductPlan::orderByRaw("CASE WHEN data_size_in_mb < 500 THEN 1 ELSE 0 END ASC")
        ->orderBy('data_size_in_mb', 'asc')
        ->orderBy('validity_in_days', 'asc')
        ->orderBy('network_id', 'asc')
        ->get();
    

        
        foreach($generalproductplans as $keyy=>$productplan){
            $size = $productplan->data_size_in_mb;
            $validity = $productplan->validity_in_days;
            $network_id = $productplan->network_id;
            $product_id = $productplan->product_id;
            $cost_price = $productplan->cost_price;
         
           
            $associated_automationplans = ProductPlan::with(['product_plan_category.network','product_plan_category.product','automation'])
            ->where('validity_in_days',$validity)
            ->where('data_size_in_mb',$size)
            ->get(); 

            $data[$keyy]['unique_plan'] = $productplan->product_plan_name;
            //@tlest there should be one...
            if(count($associated_automationplans) > 0){
                foreach($associated_automationplans as $key=>$associated_automationplan){
                    $getnetworkid = $associated_automationplan->product_plan_category->network->id ?? 'nil';
                    $network_namee = $associated_automationplan->product_plan_category->network->network_name ?? 'nil';
                    $productid = $associated_automationplan->product_plan_category->product->id ?? 'nil';
                    $sizee = $associated_automationplan->data_size_in_mb;
                    $vall = $associated_automationplan->validity_in_days;
                    if($getnetworkid == $network_id && $productid == $product_id && $size == $sizee && $validity == $vall){
                        $dataa[$key]['product_plan'] = $associated_automationplan->product_plan_name;
                        $dataa[$key]['size'] = $associated_automationplan->data_size_in_mb;
                        $dataa[$key]['validity'] = $associated_automationplan->validity_in_days;
                        $dataa[$key]['visibility'] = $associated_automationplan->visibility;
                        $dataa[$key]['automation'] = $associated_automationplan->automation->automation_name;
                        $dataa[$key]['network'] = $network_namee;
                    }     
                }
                $data[$keyy]['automations'] = $dataa;
            }
            
            
            $dataa= [];
        }


        // $plans = json_decode($data, true); // decode JSON to array
        $plans = $data; // decode JSON to array
        // $dat['data'] = $data;

        return view('admin.unique_product_plans.index',compact('plans'));

    }
}
