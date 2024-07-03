<?php

namespace App\Http\Controllers;

use App\Models\Network;
use App\Models\Product;
use App\Models\Automation;
use Illuminate\Http\Request;
use App\Models\ProductPlanCategory;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ProductPlanCategoryController extends Controller
{
    public function index(){
        $product_plan_categories = ProductPlanCategory::with(['product' => function($query){
            $query->where('slug','data');
        }])->latest()->get();


        $automations = Automation::select('id','automation_name')->get();
        $networks = Network::select('id','network_name')->get();
        $products = Product::select('id','product_name')->get();
        
        $data['automations'] = $automations;
        $data['products'] = $products;
        $data['networks'] = $networks;
        $data['product_plan_categories'] = $product_plan_categories;
       
        return view('admin.product_plan_categories.index')->with($data);
    }

    public function store(Request $request){
       
       
         $validator = Validator::make($request->all(), [
            'product_plan_category_name' => 'required|max:255|unique:product_plan_categories,product_plan_category_name',
            'product_id' => 'required|exists:products,id',
            'network_id' => 'nullable|exists:networks,id',
            'automation_id' => 'required|exists:automations,id',
          ]);


          
    
          if ($validator->stopOnFirstFailure()->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
          }

          $data = $validator->validated();
         
          $create_product_plan_categories = ProductPlanCategory::create($data);
    
          if($create_product_plan_categories){
            Session::flash('success','Product plan categories successfully created');
          }else{
            Session::flash('failure','Error occurred while creating product plan category');
          }
    
          return redirect()->back();
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
