<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\Network;
use App\Models\Product;
use App\Models\UserPlan;
use App\Models\ProductPlan;
use App\Models\PlanProfitSetting;
use App\Models\ProductPlanCategory;
use Illuminate\Support\Facades\Hash;
use App\Models\ProductPlanCustomPricing;

class ProductPlanService{

    // public function fetch_all_data_plans($data){
    //     $costprice_order = 'cost_price'; //
    //     $is_api = 'yes'; //
 
    //     $product_planss = [];

    //     // if(count($product_plans) >= 1){

    //     foreach($data as $key=>$product_plan){
    //         $cost_price = $product_plan->cost_price;
    //         $data_size_in_mb = $product_plan->data_size_in_mb;
    //         $network_id = $product_plan->product_plan_category->network->id;
    //         $product_id = $product_plan->product_plan_category->product->id;
    //         $slug = Product::select('slug')->where('id',$product_id)->first()->slug;

    //         $product_planss[$key]['plan_id'] =  ((int)$product_plan->api_id) ?? NULL; //api for api calls
    //         $product_planss[$key]['product_plan_id'] = $product_plan->id;
    
    //         for($i=1; $i <=12; $i++){

    //             $user_level = "user_level_{$i}_selling_price";
    //             if($slug == 'data' || $slug == 'cable_subscription'){
    //                 if($slug == 'data'){
    //                     $get_planprofit = PlanProfitSetting::where('network_id',$network_id)
    //                     ->where('product_id',$product_id)
    //                     ->where('data_size_in_mb',$data_size_in_mb)
    //                     ->first();
                
    //                     $profitlevel_for_user = "profit_$i" ?? 'profit_1';
    //                     $profit_value = $get_planprofit->$profitlevel_for_user ?? 50;
    //                     $selling_price = $cost_price + abs( $profit_value);

    //                 }else{
    //                     $selling_price = $product_plan->$user_level ?? 5000;
    //                 }

    //             }else{
    //                 //airtime and electricity for now
    //                 $selling_profit = $product_plan->$user_level ?? 1; //percent
    //                 $selling_price = $selling_profit; //percent
    //             }

    //             $product_planss[$key]['cost_price_aff_'.$i] = $selling_price;

    //         }
            
    //         $product_planss[$key]['is_api'] = 'yes';  
    //         $product_planss[$key]['product_id'] = $product_id;  
    //         $product_planss[$key]['network_id'] = $network_id;  
    //         $product_planss[$key]['product_plan_name'] = $product_plan->product_plan_name;
    //         $product_planss[$key]['data_size_in_mb'] = $product_plan->data_size_in_mb;
    //         $product_planss[$key]['validity_in_days'] = $product_plan->validity_in_days; 
                            
    //     }

    //     // }else{
    //     //     $product_planss[0]['cost_price'] = NULL;
    //     //     $product_planss[0]['product_plan_id'] = NULL;
    //     //     $product_planss[0]['api_id'] = NULL;
    //     //     $product_planss[0]['selling_price'] = NULL;
    //     //     $product_planss[0]['product_plan_name'] = NULL;
    //     //     $product_planss[0]['data_size_in_mb'] = NULL;
    //     //     $product_planss[0]['validity_in_days'] = NULL;    
    //     //     $product_planss[0]['automation_id'] = NULL;  
    //     // }

    //     if($is_api != NULL){
    //         return [
    //             'status' => 1,
    //             'message' => $product_planss,
    //             'plans' => $product_planss,
    //         ];
    //     }
        
    //     $data_sizes = collect($product_planss)
    //     ->pluck('data_size_in_mb')
    //     ->unique()
    //     ->sort()
    //     ->values()
    //     ->toArray();
    //     return [
    //         'status' => 1,
    //         'message' => $product_planss,
    //         'plans' => $product_planss,
    //         'sizes' => $data_sizes,
    //         // 'plan_level' => $plan_level
    //     ];
    // }



    public function fetch_all_data_plans($data){
            $product_planss = [];

            foreach ($data as $key => $product_plan) {
                $cost_price = $product_plan->cost_price;
                $data_size_in_mb = $product_plan->data_size_in_mb;
                $network_id = optional($product_plan->product_plan_category?->network)->id;
                $product_id = optional($product_plan->product_plan_category?->product)->id;
                $slug = $product_plan->product_plan_category?->product?->slug;

                $product_planss[$key]['api_id'] = $product_plan->api_id ? (int)$product_plan->api_id : null;
   

                for ($i = 1; $i <= 12; $i++) {
                    $user_level = "user_level_{$i}_selling_price";

                    if (in_array($slug, ['data', 'cable_subscription'])) {
                        if ($slug === 'data') {
                            $get_planprofit = PlanProfitSetting::where('network_id', $network_id)
                                ->where('product_id', $product_id)
                                ->where('data_size_in_mb', $data_size_in_mb)
                                ->first();

                            $profitlevel_for_user = "profit_$i";
                            $profit_value = $get_planprofit?->$profitlevel_for_user ?? 50;
                            $selling_price = $cost_price + abs($profit_value);
                        } else {
                            $selling_price = $product_plan->$user_level ?? 5000;
                        }
                    } else {
                        // Airtime & electricity for now
                        $selling_profit = $product_plan->$user_level ?? 1; // percent
                        $selling_price = $selling_profit;
                    }

                    $product_planss[$key]['cost_price_aff_' . $i] = $selling_price;
                }

                $product_planss[$key]['is_api'] = 'yes';
                $product_planss[$key]['plan_category'] = $product_plan->product_plan_category;
                $product_planss[$key]['product'] = optional($product_plan->product_plan_category?->product);
                $product_planss[$key]['network'] = optional($product_plan->product_plan_category?->network);
                $product_planss[$key]['product_plan_name'] = $product_plan->product_plan_name;
                $product_planss[$key]['data_size_in_mb'] = (int) $product_plan->data_size_in_mb;
                $product_planss[$key]['validity_in_days'] = (int) $product_plan->validity_in_days;
            }

            // Always return
            $data_sizes = collect($product_planss)
                ->pluck('data_size_in_mb')
                ->unique()
                ->sort()
                ->values()
                ->toArray();

            $networks = Network::get();
            $products = Product::get();
            $product_plan_categories = ProductPlanCategory::get();

            return [
                'status' => 1,
                'message' => 'Success',
                'plans' => $product_planss,
                'networks' => $networks,
                'products' => $products,
                'product_plan_categories' => $product_plan_categories,
            ];
    }

}