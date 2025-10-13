<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\Product;
use App\Models\UserPlan;
use App\Models\ProductPlan;
use App\Models\PlanProfitSetting;
use App\Models\ProductPlanCategory;
use Illuminate\Support\Facades\Hash;
use App\Models\ProductPlanCustomPricing;

class ProductPlanService{

    public function fetch_all_data_plans($data){
        $user_details = $data['user'];
        $network_id = $data['network_id'];
        $product_plan_category_id = $data['product_plan_category_id'] ?? NULL;
        $product_id = $data['product_id'];
        $is_api = $data['is_api'] ?? NULL;

        //selling plan level
        $user_plan_id = $user_details->user_plan_id;
        $user_id = $user_details->id;
        $user_level = UserPlan::select('plan_level')->where('id',$user_plan_id)->first();
        $plan_level = $user_level->plan_level;
        $costprice_order = 'cost_price'; //

        $product_plans = ProductPlan::orderByRaw('CASE WHEN CAST(data_size_in_mb AS UNSIGNED) < 500 THEN 1 ELSE 0 END') // Push <500MB to bottom
        ->orderByRaw('CAST(data_size_in_mb AS UNSIGNED)') // Then order by size
        ->orderByRaw('CAST(' . $costprice_order . ' AS UNSIGNED)') // Then by price
        ->orderByRaw('CAST(validity_in_days AS UNSIGNED) DESC') // Then by validity
        ->get();
        
        $product_planss = [];
        $dat = [];

        // if(count($product_plans) >= 1){

            foreach($product_plans as $key=>$product_plan){
                $cost_price = $product_plan['cost_price'];
                $data_size_in_mb = $product_plan['data_size_in_mb'];
             
                $network_id = $product_plan->product_plan_category->network->id;
                $product_id = $product_plan->product_plan_category->product->id;
                $slug = Product::select('slug')->where('id',$product_id)->first()->slug;

                $product_planss[$key]['plan_id'] =  (int)$product_plan->api_id ?? NULL; //api for api calls
                $product_planss[$key]['product_plan_id'] = $product_plan->id;
        
                for($i=1; $i <=12; $i++){

                    $user_level = "user_level_{$i}_selling_price";
                    if($slug == 'data' || $slug == 'cable_subscription'){
                        if($slug == 'data'){
                            $get_planprofit = PlanProfitSetting::where('network_id',$network_id)
                            ->where('product_id',$product_id)
                            ->where('data_size_in_mb',$data_size_in_mb)
                            ->first();
                    
                            $profitlevel_for_user = "profit_$i" ?? 'profit_1';
                            $profit_value = $get_planprofit->$profitlevel_for_user ?? 50;
                            $selling_price = $cost_price + abs(num: $profit_value);

                        }else{
                            $selling_price = $product_plan->$user_level ?? 5000;
                        }

                    }else{
                        //airtime and electricity for now
                        $selling_profit = $product_plan->$user_level ?? 1; //percent
                        $selling_price = $selling_profit; //percent
                    }
                    
                    $product_planss[$key]['cost_price_aff_'.$i] = $selling_price;

                }
               
                $product_planss[$key]['is_api'] = 'yes';  
                $product_planss[$key]['product_id'] = $product_id;  
                $product_planss[$key]['network_id'] = $network_id;  
                $product_planss[$key]['product_plan_name'] = $product_plan->product_plan_name;
                $product_planss[$key]['data_size_in_mb'] = $product_plan->data_size_in_mb;
                $product_planss[$key]['validity_in_days'] = $product_plan->validity_in_days; 
                             
            }

        // }else{
        //     $product_planss[0]['cost_price'] = NULL;
        //     $product_planss[0]['product_plan_id'] = NULL;
        //     $product_planss[0]['api_id'] = NULL;
        //     $product_planss[0]['selling_price'] = NULL;
        //     $product_planss[0]['product_plan_name'] = NULL;
        //     $product_planss[0]['data_size_in_mb'] = NULL;
        //     $product_planss[0]['validity_in_days'] = NULL;    
        //     $product_planss[0]['automation_id'] = NULL;  
        // }

        if($is_api != NULL){
            return [
                'status' => 1,
                'message' => $product_planss,
                'plans' => $product_planss,
            ];
        }
        
        $data_sizes = collect($product_planss)
        ->pluck('data_size_in_mb')
        ->unique()
        ->sort()
        ->values()
        ->toArray();
        return [
            'status' => 1,
            'message' => $product_planss,
            'plans' => $product_planss,
            'sizes' => $data_sizes,
            'plan_level' => $plan_level
        ];
    }

}