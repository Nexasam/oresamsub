<?php

namespace App\Http\Controllers;

use App\Models\BulkDataProductPlans;
use App\Models\Network;
use App\Models\Product;
use App\Models\UserPlan;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\ProductPlanCategory;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class BulkDataPlanController extends Controller
{
    public function index($product_plan_category_id){
        $user_plans = UserPlan::where('plan_level','<=',env('RESELLER_PLAN_COUNT'))->get();
        $bulk_data_plans = BulkDataProductPlans::with('product_plan_category')->where('product_plan_category_id',$product_plan_category_id)->paginate(50);
        $product_plan_category = ProductPlanCategory::where('id',$product_plan_category_id)->first();
        if(! $product_plan_category){
            return redirect()->back();
        }
        $data['product_plan_category'] = $product_plan_category;
        $data['user_plans'] = $user_plans;
        $data['bulk_data_plans'] = $bulk_data_plans;
        return view('admin.bulk_data_plans.index')->with($data);
    }

    public function store(Request $request){
        // dd($request->all());
 
        $validator = Validator::make($request->all(), [
            'bulk_data_plan_name' => 'required|max:255',
            'data_value_tb' => 'required|numeric',
            'product_plan_category_id' => 'required|exists:product_plan_categories,id',
            'cost_price' => 'required|numeric',
            'default_selling_price' => 'required|numeric',
            'user_level_1_selling_price' => 'required|numeric',
            'user_level_2_selling_price' => 'required|numeric',
            'user_level_3_selling_price' => 'required|numeric',
            'user_level_4_selling_price' => 'required|numeric',
            'mb_data_measurement' => 'required|numeric',
          ]);
          

          if ($validator->stopOnFirstFailure()->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
          }
    
         $data = $validator->validated();
         $data['data_value_gb'] = $data['data_value_tb'] * $data['mb_data_measurement'];
         $data['data_value_mb'] = $data['data_value_tb'] * $data['mb_data_measurement'] * $data['mb_data_measurement'];
        //  dd($data);
         
          $create_bulk_data_product = BulkDataProductPlans::create($data);
    
          if($create_bulk_data_product){
            Session::flash('success','Bulk data plan successfully created');
          }else{
            Session::flash('failure','Error occurred while creating product');
          }
    
          return redirect()->back();
    }
}
