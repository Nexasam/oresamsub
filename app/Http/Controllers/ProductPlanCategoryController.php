<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Automation;
use Illuminate\Http\Request;
use App\Models\ProductPlanCategory;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ProductPlanCategoryController extends Controller
{
    public function index(){
        $product_plan_categories = ProductPlanCategory::get();

        $automations = Automation::select('id','automation_name')->get();
        
        $data['automations'] = $automations;
        $data['product_plan_categories'] = $product_plan_categories;
       
        return view('admin.product_plan_categories.index')->with($data);
    }

    public function store(Request $request){
        // dd($request->all());
 
       
        $validator = Validator::make($request->all(), [
            'product_plan_category_name' => 'required|max:255|unique:products,product_name',
            'product_id' => 'required|exists:product_categories,id',
          ]);
          
    
          if ($validator->stopOnFirstFailure()->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
          }
    
          $data['product_name'] = $request->product_name;
          $data['product_categories_id'] = $request->product_category_id;
          $data['visibility'] = $request->visibility;
          $data['active_status'] = $request->active_status;
         
          $create_product = Product::create($data);
    
          if($create_product){
            Session::flash('success','Product successfully created');
          }else{
            Session::flash('failure','Error occurred while creating product');
          }
    
          return redirect()->route('admin.products.index');
    }

    public function updateAutomation(Request $request){
          $validator = Validator::make($request->all(), [
            'product_category_id' => 'required|max:255|required|exists:product_plan_categories,id',
            'automation_id' => 'required|exists:automations,id',
          ]);
          

          if ($validator->stopOnFirstFailure()->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
          }

          $data['product_category_id'] = $request->product_category_id;
          $data['automation_id'] = $request->automation_id;
          
          ProductPlanCategory::where('id',$request->product_category_id)
                              ->update([
                                'automation_id' => $request->automation_id
                              ]);

          return response()->json(['status'=>'-1', 'message'=>'successfully updated' ]);
    }
}
