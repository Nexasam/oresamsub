<?php

namespace App\Http\Controllers;

use App\Models\ProductPlan;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\ProductPlanCategory;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ProductPlanController extends Controller
{
    public function index(){
        // dd('na here');
        $product_plans = ProductPlan::with(['product','product_plan_category','automation'])
        ->where('visibility',1)
        ->get();
        $product_plan_categories = ProductPlanCategory::get();
        $data['product_plans'] = $product_plans;
        $data['product_plan_categories'] = $product_plan_categories;

        
        return view('admin.product_plans.index')->with($data);
    }


    public function toggle_product_public_visibility(Request $request){    
      $validator = Validator::make($request->all(), [
        'productPlanId' => 'required|max:255|exists:product_plans,id',
        'token' => 'required',
      ]);
      
      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

      $detail = ProductPlan::where('id',$request->productPlanId)->first();
      $update = $detail->public_visibility ? 0 : 1;
      $detail->update([
        'public_visibility' => $update
      ]);

      return response()->json(['status'=>'1', 'message'=>'success' ]);     
    }

    public function toggle_product_visibility(Request $request){
      
      $validator = Validator::make($request->all(), [
        'productPlanId' => 'required|max:255|exists:product_plans,id',
        'token' => 'required',
      ]);
      

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

      $detail = ProductPlan::where('id',$request->productPlanId)->first();
      $update = $detail->visibility ? 0 : 1;
      $detail->update([
        'visibility' => $update
      ]);

      return response()->json(['status'=>'1', 'message'=>'success']);

    }
    public function admin_fetch_product_plans(Request $request){
        $data = ProductPlan::with(['automation','product_plan_category.network','product_plan_category.product'])
        ->latest()
        ->get();

        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('DT_RowIndex',function($data){
          return $data->id;
        })
        ->addColumn('product_name',function($data){
          return $data->product_plan_category->product->product_name ?? '';
        })
        ->addColumn('network_name',function($data){
          return $data->product_plan_category->network->network_name ?? '';
        })
        ->addColumn('product_plan_name',function($data){
          return $data->product_plan_name;
        })
      
        ->addColumn('product_plan_category_id',function($data){
          return $data->product_plan_category->product_plan_category_name;
        })
        ->addColumn('automation',function($data){
          return $data->automation->automation_name;
        })
        ->addColumn('data_size_in_mb',function($data){
          return $data->data_size_in_mb;
        })
        ->addColumn('validity_in_days',function($data){
          return $data->validity_in_days;
        })
        ->addColumn('cost_price',function($data){
          return $data->cost_price;
        })
        ->addColumn('user_level_1_selling_price',function($data){
          return number_format($data->user_level_1_selling_price,2);
        })
        ->addColumn('user_level_2_selling_price',function($data){
          return number_format($data->user_level_2_selling_price,2);
        })
        ->addColumn('user_level_3_selling_price',function($data){
          return number_format($data->user_level_3_selling_price,2);
        })
        ->addColumn('user_level_4_selling_price',function($data){
          return number_format($data->user_level_4_selling_price,2);
        })
        ->addColumn('visibility',function($data){
          $escapedUrl = htmlspecialchars(json_encode($data->id));
          $token = htmlspecialchars(json_encode(csrf_token()));
          $checked = $data->visibility == 1 ? 'checked':'';
          $actual_value = $data->visibility;
          $checkedd = htmlspecialchars(json_encode($actual_value));
          $toggle_btn = '<div class="flex items-center">';
          $toggle_btn .=  '<input onchange="toggleProductPlanVisibility('.$escapedUrl.','.$token.','.$checkedd.')" type="checkbox" id="hs-basic-with-description-checked'.$data->id.'" class="ti-switch" '.$checked.'>';
          $toggle_btn .=  '<label for="hs-basic-with-description-checked" class="text-sm text-gray-500 ms-3 dark:text-white/70 "></label>';
          $toggle_btn .=  ' <span class="badge rounded-sm bg-success/10 text-success hidden" id="nnotification'.$data->id.'"></span>  </div>';
          
          return $toggle_btn;
          // return $data->visibility ? 'YES' : 'NO';
          // return $data->visibility == 1 ? 'PUBLIC' : 'PRIVATE';
        })
        ->addColumn('public_visibility',function($data){
          $escapedUrl = htmlspecialchars(json_encode($data->id));
          $token = htmlspecialchars(json_encode(csrf_token()));
          $checked = $data->public_visibility == 1 ? 'checked':'';
          $actual_value = $data->public_visibility;
          $checkedd = htmlspecialchars(json_encode($actual_value));
          $toggle_btn = '<div class="flex items-center">';
          $toggle_btn .=  '<input onchange="toggleProductPlanPublicVisibility('.$escapedUrl.','.$token.','.$checkedd.')" type="checkbox" id="hs-basic-with-description-checked'.$data->id.'" class="ti-switch" '.$checked.'>';
          $toggle_btn .=  '<label for="hs-basic-with-description-checked" class="text-sm text-gray-500 ms-3 dark:text-white/70 "></label>';
          $toggle_btn .=  ' <span class="badge rounded-sm bg-success/10 text-success hidden" id="nnotification2'.$data->id.'"></span>  </div>';
          
          return $toggle_btn;
          // return $data->public_visibility ? 'YES' : 'NO';
          // return $data->public_visibility == 1 ? 'PUBLIC' : 'PRIVATE';

        })
        ->addColumn('date',function($data){
          return $data->created_at;
        })
        ->escapeColumns([])
        ->make(true);
    }

    public function fetch_public_product_plans(Request $request){
      $data = ProductPlan::with(['product_plan_category.network','product_plan_category.product'])
      ->where('public_visibility',1)
      ->latest()->get();

      return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('DT_RowIndex',function($data){
        return $data->id;
      })
      ->addColumn('product_name',function($data){
        return $data->product_plan_category->product->product_name ?? '';
      })
      ->addColumn('product_plan_name',function($data){
        return $data->product_plan_name;
      })
      ->addColumn('network_name',function($data){
        return $data->product_plan_category->network->network_name ?? '';
      })
      ->addColumn('product_plan_category_id',function($data){
        return $data->product_plan_category->product_plan_category_name;
      })
      ->addColumn('data_size_in_mb',function($data){
        if($data->product_plan_category->product->slug == 'data'){
          return $data->data_size_in_mb;
        }else{
          return 'nil';
        }
      })
      ->addColumn('user_level_1_selling_price',function($data){
        if($data->product_plan_category->product->slug == 'airtime' || $data->product_plan_category->product->slug == 'utility_bills'){
          return number_format($data->user_level_1_selling_price,2). ' (% Discount)';
        }else{
          return number_format($data->user_level_1_selling_price,2);

        }
      })
      ->addColumn('validity_in_days',function($data){
        return $data->validity_in_days;
      })
      ->escapeColumns([])
      ->make(true);
  }

    public function store(Request $request){
        // $validator = Validator::make($request->all(), [
        //     'first_name' => 'required|max:255',
        //     'last_name' => 'required|max:255',
        //     'phone_number' => 'required',
        //     'email' => 'required|unique:users,email',
        //     'password' => 'required',
        //     'confirm_password' => 'required',
        //     'gender' => 'required',
        //   ]);
          
        //   if ($validator->stopOnFirstFailure()->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput();
        //   }

        

          $data['product_plan_name'] = $request->product_plan_name;
          // $data['product_id'] = $request->product_id;
          $data['product_plan_category_id'] = $request->product_plan_category_id; 
          $data['automation_product_plan_id'] = $request->id; ////planId
          $data['automation_id'] = $request->automation_id; ///
          $data['cost_price'] = $request->cost_price;
          $data['data_size_in_mb'] = $request->data_size_in_mb;
          $data['validity_in_days'] = $request->validity_in_day;
          $data['default_selling_price'] = $request->selling_price;
          $data['user_level_1_selling_price'] = $request->user_plan_1;
          $data['user_level_2_selling_price'] = $request->user_plan_2;
          $data['user_level_3_selling_price'] = $request->user_plan_3;
          $data['user_level_4_selling_price'] = $request->user_plan_4;
          $data['user_level_5_selling_price'] = NULL;
          $data['user_level_6_selling_price'] = NULL;
          $data['visibility'] = 1;
          $data['active_status'] = 1;
          // $data['network_id'] = $request->network_id;

        //   return response()->json(['status'=>'-1', 'message'=>$request->all()  ]);

        
          $product_plan = ProductPlan::updateOrCreate([
            'automation_product_plan_id' => $request->id,
            'automation_id' => $request->automation_id,
          ],$data);

          return response()->json(['status'=>'1', 'message'=>'successfully saved'. 'plan_id:'.$request->id.' auto_id: '.$request->automation_id. 'data:'.json_encode($data),  ]);

    
        //   if($product_plan){
        //     Session::flash('success','Product plan was successfully saved');
        //   }else{
        //     Session::flash('failure','Error occurred while saving product plan');
        //   }


    
        //   return redirect()->route('admin.users.index');
    }
}
