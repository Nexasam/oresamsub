<?php

namespace App\Http\Services;

use App\Models\AutomationProductPlan;
use App\Models\PlanProfitSetting;
use App\Models\ProductPlan;
use App\Models\ProductPlanCategory;
use App\Models\ProductPlanCustomPricing;
use App\Models\UserPlan;

class DataPlansService{

    public function fetch_user_data_plans($data){
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

        //you need to note later if the user selelcted a type:
        if($product_plan_category_id == NULL){
            //do i need this sef?
            // $product_plan_categories = ProductPlanCategory::select('id','automation_id')
            // ->where('product_id',$product_id)
            // ->where('network_id',$network_id)
            // ->get();

            $product_plan_categories_id_arr = ProductPlanCategory::where('product_id',$product_id)
            ->where('network_id',$network_id)
            ->pluck('id')
            ->toArray();
            
        }else{
            //do i need this sef?
            // $product_plan_categories = ProductPlanCategory::when(!empty($network_id), function($query) use ($network_id) {
            //     $query->where('network_id',$network_id);
            // })
            // ->select('id','automation_id')
            // ->where('product_id',$product_id)
            // ->where('id',$product_plan_category_id)
            // ->get();

            $product_plan_categories_id_arr = [$product_plan_category_id];
        }


        $product_plans = ProductPlan::whereIn('product_plan_category_id', $product_plan_categories_id_arr)
        ->where('visibility', 1)
        ->orderByRaw('CASE WHEN CAST(data_size_in_mb AS UNSIGNED) < 500 THEN 1 ELSE 0 END') // Push <500MB to bottom
        ->orderByRaw('CAST(data_size_in_mb AS UNSIGNED)') // Then order by size
        ->orderByRaw('CAST(' . $costprice_order . ' AS UNSIGNED)') // Then by price
        ->orderByRaw('CAST(validity_in_days AS UNSIGNED) DESC') // Then by validity
        ->get();
        
        $product_planss = [];
        $dat = [];

        if(count($product_plans) >= 1){

            foreach($product_plans as $key=>$product_plan){
                $cost_price = $product_plan['cost_price'];
                // $data_size_in_mb = $product_plan['data_size_in_mb'];
                // $get_planprofit = PlanProfitSetting::where('network_id',$network_id)
                // ->where('product_id',$product_id)
                // ->where('data_size_in_mb',$data_size_in_mb)
                // ->first();
                // $profitlevel_for_user = "profit_$plan_level"; 
                // $profit = $get_planprofit->$profitlevel_for_user;
                // $selling_price = $cost_price + abs($profit);

                // //custom case
                // //HERE SELLING PRICE CHANGES IF THEHRE IS A CUSTOM SETTING: put in a service later
                // $check_custom_setting = ProductPlanCustomPricing::where('product_plan_id','=', $product_plan->id)
                // ->where('user_id',$user_details->id)
                // ->first();
                // $selling_price = $check_custom_setting == NULL ? $selling_price : $check_custom_setting->price;  

                $dat['product_id'] = $product_id;
                $dat['user'] = $user_details;
                $dat['plan_details'] = $product_plan;
                $dat['network_id'] = $network_id;
                $selling_price = $this->get_customer_price_per_plan($dat)['message'];
                $upline_commission = $this->get_customer_price_per_plan($dat)['upline_commission'] ?? 5;

                //HERE SELLING PRICE CHANGES IF THEHRE IS A CUSTOM SETTING: put in a service later::::WATCH THIS IN PROD.
                $check_custom_setting = ProductPlanCustomPricing::where('product_plan_id', $product_plan->id)
                ->where('user_id',$user_details->id)
                ->first();
                $selling_price = $check_custom_setting == NULL ? $selling_price : $check_custom_setting->price;  
             
    
             
                if($is_api != NULL){
                  //api route
                  $product_planss[$key]['plan_id'] =  (int)$product_plan->api_id ?? NULL; //api for api calls
                  $product_planss[$key]['cost_price'] = $selling_price; //their cost price will be selling price 
                 }else{
                  //likely web/mobile route
                  $product_planss[$key]['product_plan_id'] = $product_plan->id;
                  $product_planss[$key]['selling_price'] = $selling_price;
                  $product_planss[$key]['automation_id'] = $product_plan->automation_id;  
                }

                $product_planss[$key]['product_plan_name'] = $product_plan->product_plan_name;
                $product_planss[$key]['data_size_in_mb'] = $product_plan->data_size_in_mb;
                $product_planss[$key]['validity_in_days'] = $product_plan->validity_in_days; 
                $product_planss[$key]['upline_commission'] = $upline_commission; 
                             
            }

        }else{
            $product_planss[0]['cost_price'] = NULL;
            $product_planss[0]['product_plan_id'] = NULL;
            $product_planss[0]['api_id'] = NULL;
            $product_planss[0]['selling_price'] = NULL;
            $product_planss[0]['product_plan_name'] = NULL;
            $product_planss[0]['data_size_in_mb'] = NULL;
            $product_planss[0]['validity_in_days'] = NULL;    
            $product_planss[0]['automation_id'] = NULL;  
            $product_planss[0]['upline_commission'] = 0;  
        }

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

    public function get_customer_price_per_plan($data){
        $product_plan = $data['plan_details'];
        $commission_feature = $data['plan_details']->commission_feature;
        $upline_commission_option = $data['plan_details']->upline_commission_option;
        $commission_feature = $data['plan_details']->commission_feature;
        $cost_price = $data['plan_details']->cost_price;
        $data_size_in_mb = $data['plan_details']->data_size_in_mb;
        $network_id = $data['network_id'];
        $product_id = $data['product_id'];
        $user_details = $data['user'];
        $upline_commission = 0;

        $user_plan_id = $user_details->user_plan_id;
        $user_id = $user_details->id;
        $user_level = UserPlan::select('plan_level')->where('id',$user_plan_id)->first();
        $plan_level = $user_level->plan_level  ??  1;


        $check_automation_product_plans = AutomationProductPlan::where('product_plan_id', $product_plan->id)
        ->where('is_active',1)
        ->first();

        if($check_automation_product_plans){
            //if it exists, then use the new flow
          
            $spp = 'user_level_'.$plan_level.'_selling_price'; 
            $sppdefault = 'user_level_1_selling_price'; 
            $selling_price = $product_plan->$spp ?? $product_plan->$sppdefault; 
           

            logger('new price model for customers');
            return [
                'status' => 1,
                'message' => $selling_price,
                'upline_commission' => $upline_commission
            ];
        }

        ////////////////////////BELOW SHOWS THAT PLAN HAS NOT BEEN MIGRATED SO LET IT STAY WITH THE OLD FLOW.

        logger('still old price model for customers');
    
            
  

        $get_planprofit = PlanProfitSetting::where('network_id',$network_id)
        ->where('product_id',$product_id)
        ->where('data_size_in_mb',$data_size_in_mb)
        ->first();

        $profitlevel_for_user = "profit_$plan_level" ?? 'profit_1'; 
        $profit = $get_planprofit->$profitlevel_for_user ?? 50; //business profit

        if($commission_feature == 1 && $upline_commission_option == 'flat' ){
            $upline_commission = 5; //flat 5 naira for now
        }else{
            $plan_perc = $product_plan->upline_percentage_commission ?? 20; //default 30%
            if($plan_perc <= 0 || $plan_perc > 100){
                $plan_perc = 20;
            }
            $upline_commission = $profit * ($plan_perc / 100 ); //30% to upline
        }
        $selling_price = $cost_price + abs($profit);


        //custom case
        //HERE SELLING PRICE CHANGES IF THEHRE IS A CUSTOM SETTING: put in a service later
        // $check_custom_setting = ProductPlanCustomPricing::where('product_plan_id','=', $product_plan->id)
        // ->where('user_id',$user_details->id)
        // ->first();
        // $selling_price = $check_custom_setting == NULL ? $selling_price : $check_custom_setting->price; 


        $augmentsp = $cost_price + 50;

        return [
            'status' => 1,
            'message' => $selling_price ?? $augmentsp,
            'upline_commission' => $upline_commission
        ];
    }



    // version1
      // if(auth()->user()->email == 'oreofe@gmail.com' && $product_slug == 'data'){
        //     $uniqueplans = UniqueProductPlan::where('network_id',$request->network_id)->get();
        //     foreach($uniqueplans as $product_plan){

        //         //get thhe normal pricing
        //         $price_level = "price_".$plan_level;
        //         $amount = $product_plan->$price_level;
        //         $selling_price = $amount;

        //         //HERE SELLING PRICE CHANGES IF THEHRE IS A CUSTOM SETTING: put in a service later
        //         $check_custom_setting = ProductPlanCustomPricing::where('product_plan_id','=', $product_plan->id)->where('user_id',auth()->id())->first();
        //         $amount = $check_custom_setting == NULL ? $amount : $check_custom_setting->price;  
        //         $user_level_selling = "user_level_".$plan_level."_selling_price";
        //         $user_level_commission = "user_level_".$plan_level."_commission";
        //         // $selling_price = $product_plan->$user_level_selling;
        //         $upline_commission = $product_plan->$user_level_commission;
        //         $selling_price = $check_custom_setting == NULL ? $selling_price : $check_custom_setting->price;  
            
        //        if( ($product_slug == 'airtime' || $product_slug == 'utility_bills') && $amount != '' ){
        //              $purchase_discount = $product_plan->$user_level_selling;
        //              $actual_discount_value = ceil(($purchase_discount/100) * $amount);  
        //              $discounted_selling_price = $amount - abs($actual_discount_value);
        //              $selling_price = 0; //this is from the system, not applicable for airtime
        //        }else{
        //            $discounted_selling_price = $selling_price;
        //        }
 
        //        if($product_plan){
        //            $counter++;
        //            $product_planss[$counter]['product_plan_id'] = $product_plan->id;
        //            $product_planss[$counter]['amount'] = $amount;
        //            $product_planss[$counter]['selling_price'] = $discounted_selling_price;
        //            $product_planss[$counter]['upline_commission'] = $upline_commission;
        //            $product_planss[$counter]['product_plan_name'] = $product_plan->product_plan_name;
        //            $product_planss[$counter]['data_size_in_mb'] = $product_plan->data_size_in_mb;
        //            $product_planss[$counter]['validity_in_days'] = $product_plan->validity_in_days;    
        //            $product_planss[$counter]['automation_id'] = NULL;    
        //        }

        //    }

        //     // Extract unique sizes from $product_planss
        //     $data_sizes = collect($product_planss)
        //     ->pluck('data_size_in_mb')
        //     ->unique()
        //     ->sort()
        //     ->values()
        //     ->toArray();
        
        //     return response()->json(['status'=>'1','user_level'=>$plan_level ,'message'=>'Product plans fetched','counter' =>count($product_planss),'data' => $product_planss, 'sizes' => $data_sizes ]);

        // }

}