<?php

namespace App\Http\Controllers;

use App\Models\ProductPlan;
use Illuminate\Http\Request;

class PriceChangeController extends Controller
{
    //currently for megasubplug
    public function changeMegasubPrice(){
        $plan_category_id_smemtn = '9c39f216-00a0-42ab-b195-558133f67a15';
        $mtncostprice = 640;
        $mtnsprice1 = 660;
        $mtnsprice2 = 655;
        $mtnsprice3 = 650;
        $mtnsprice4 = 645;
        $product_plans = ProductPlan::where('product_plan_category_id',$plan_category_id_smemtn)
                         ->where('automation_id','9c2887ea-55b5-4f19-904e-e490a10682ea')
                         ->get();
        foreach($product_plans as $product_plan){
            $product_plan_name = $product_plan['product_plan_name'];
            $old_selling_price = $product_plan['default_selling_price'];
            $size = $product_plan['data_size_in_mb'] / 1000;
            if($product_plan['data_size_in_mb'] == 500){
                $size = 0.5;
            }
            $old_user_level_1_selling_price = $product_plan['user_level_1_selling_price'] ;
            $old_user_level_2_selling_price = $product_plan['user_level_2_selling_price'];
            $old_user_level_3_selling_price = $product_plan['user_level_3_selling_price'];
            $old_user_level_4_selling_price = $product_plan['user_level_4_selling_price'];
            $mtnprice11 = $mtnsprice1 * $size;
            $mtnprice22 = $mtnsprice2 * $size;
            $mtnprice33 = $mtnsprice3 * $size;
            $mtnprice44 = $mtnsprice4 * $size;
            // $old_selling_price5 = $product_plan['default_selling_price'];
            echo "Selling price: $product_plan_name<br>";
            echo "Selling price: $old_selling_price - $mtncostprice<br>";
            echo "Selling price1: $old_user_level_1_selling_price : $mtnprice11<br>";
            echo "Selling price2: $old_user_level_2_selling_price : $mtnprice22<br>";
            echo "Selling price3: $old_user_level_3_selling_price : $mtnprice33<br>";
            echo "Selling price4: $old_user_level_4_selling_price : $mtnprice44<br>";
            echo "<hr><hr><hr>";
        }
        
        
            
    }
}
