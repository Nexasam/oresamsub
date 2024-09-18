<?php
namespace App\Services\Products;

use App\Models\Product;
use App\Models\UserPlan;
use App\Models\ProductPlan;
use App\Models\ProductPlanCategory;

class   ProductsService{
    public function fetch_product_plans($data){
        $network_id = $data['network_id'];
        $amount = $data['amount'];
        $plan_category_id = $data['plan_category_id'];
        $product_slug = $data['product_slug'];//this is required
        
        $product_id = Product::where('slug',$product_slug)->first()->id;
        logger($plan_category_id);
         
        if($plan_category_id == ''){
            $product_plan_categories = ProductPlanCategory::select('id','automation_id')
            ->where('product_id',$product_id)
            ->where('network_id',$network_id)
            ->get();
        }else{
            $product_plan_categories = ProductPlanCategory::when(!empty($network_id), function($query) use ($network_id) {
                $query->where('network_id',$network_id);
            })->select('id','automation_id')
            ->where('product_id',$product_id)
            ->where('id',$plan_category_id)
            ->get();
        }

        // return response()->json(['status'=>'1','user_level'=>3 ,'message'=>'Product plans fetchedddd','counter' =>5,'data' => $network_id ]);



       
        $product_planss = [];
        $counter =0;

       //TODO: 
        $user_details = auth()->user();
        $user_plan_id = $user_details->user_plan_id;
        $user_id = $user_details->id;
        $user_level = UserPlan::select('plan_level')->where('id',$user_plan_id)->first();
        $plan_level = $user_level->plan_level;

        
        foreach($product_plan_categories as $key=>$product_plan_category){
            //get the automation id
            //get the product_category_id 

            if($product_slug == 'airtime'){
                $product_plans = ProductPlan::where('product_plan_category_id',$product_plan_category->id)
                ->where('automation_id',$product_plan_category->automation_id)
                ->where('visibility',1)
                ->limit(1)
                ->get();
            }else{
                $product_plans = ProductPlan::where('product_plan_category_id',$product_plan_category->id)
                ->where('visibility',1)
                ->where('automation_id',$product_plan_category->automation_id)
                ->orderBy('data_size_in_mb')
                ->get();
            }

            if(count($product_plans) > 0){
                foreach($product_plans as $product_plan){

                    $user_level_selling = "user_level_".$plan_level."_selling_price";
                    // $user_level_selling = "{user_level_$user_level_selling_price}";
                    $selling_price = $product_plan->$user_level_selling;
                    
                    if( ( $product_slug == 'airtime' || $product_slug == 'utility_bills' ) && $amount != ''){
                          $purchase_discount = $product_plan->$user_level_selling;
                          $actual_discount_value = ceil(($purchase_discount/100) * $amount);  
                          $discounted_selling_price = $amount - abs($actual_discount_value);
                          $selling_price = 0; //this is from the system, not applicable for airtime
                    }else{
                        $discounted_selling_price = $selling_price;
                    }
                   
                    if($product_plan){
                        $counter++;
                        $product_planss[$counter]['product_plan_id'] = $product_plan->id;
                        $product_planss[$counter]['amount'] = $amount;
                        $product_planss[$counter]['selling_price'] = $discounted_selling_price;
                        $product_planss[$counter]['product_plan_name'] = $product_plan->product_plan_name;
                        $product_planss[$counter]['data_size_in_mb'] = $product_plan->data_size_in_mb;
                        $product_planss[$counter]['validity_in_days'] = $product_plan->validity_in_days;    
                        $product_planss[$counter]['automation_id'] = $product_plan->automation_id;    
                    }
                }
            }    
        }


        return [
            'status' => 1,
            'product_plans' => $product_planss,
            'plan_level' => $plan_level
        ];
          
    }
}
