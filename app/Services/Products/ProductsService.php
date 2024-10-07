<?php
namespace App\Services\Products;

use App\Models\Product;
use App\Models\Setting;
use App\Models\UserPlan;
use App\Models\ProductPlan;
use App\Models\Transaction;
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

    static public function check_purchase_limit($data){
        $days_count = $data['days_count'] ;
        $user_id = $data['user_id'] ?? NULL; // null should not happen

        // for($i=0; $i < count($days_count); $i++){
        //     $start_date =  date('Y-m-d', strtotime('-'.$days_count[$i].' days'));

        //     $array[] = $start_date;

        // }
        
        // return [
        //     'status' => -1,
        //     'message' => $array
        // ];

        for($i=0; $i < count($days_count); $i++){
            if($days_count[$i] == 1){
                $start_date = date('Y-m-d');
                $end_date = date('Y-m-d');
                $product_purchase_limit = Setting::where('field_name','product_purchase_limit_daily')->first()->field_value ?? 0;
                $day_variable = 'today';
    
            }else if($days_count[$i] == 7){
                $end_date = date('Y-m-d');
                $start_date =  date('Y-m-d', strtotime('-'.$days_count[$i].' days'));
                $product_purchase_limit = Setting::where('field_name','product_purchase_limit_last_7_days')->first()->field_value ?? 0;
                $day_variable = 'the last 7 days';
            }else if($days_count[$i] == 30){
                $end_date = date('Y-m-d');
                $start_date =  date('Y-m-d', strtotime('-'.$days_count[$i].' days'));
                $product_purchase_limit = Setting::where('field_name','product_purchase_limit_last_30_days')->first()->field_value ?? 0;
                $day_variable = 'the last 30 days';
            }else{
                $product_purchase_limit = 0;
            }
    
    
            $check_transaction_sum = Transaction::where('user_id',$user_id)->where('status',1)->whereDate('created_at','>=',$start_date)->whereDate('created_at','<=',$end_date)->sum('amount');
            if($check_transaction_sum >= $product_purchase_limit){
                return [
                    'status' => -1,
                    'message' => 'Sorry, transaction limit has been reached for '.$day_variable.'. Reach out to our Support team  via whatsapp to increase limit. Thank you.'
                    // 'message' => $check_transaction_sum
                ];
            }
    
        }
     
        return [
            'status' => 1,
            'message' => 'Good. User can still carry out transaction'
        ];

        
      
        
        // return [
        //     'status' => 0,
        //     'message' => 'Sorry an error occurred'
        // ]
        
    }
}
