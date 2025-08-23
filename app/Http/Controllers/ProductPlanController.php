<?php

namespace App\Http\Controllers;

use App\Models\Network;
use App\Models\Product;
use App\Models\UserPlan;
use App\Models\Automation;
use App\Models\ProductPlan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
        // dd($data);

        
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

    public function product_plan_details($id){
        $data['automations'] = Automation::select('id','automation_name')->get();
        $data['networks'] = Network::select('id','network_name')->get();
        $data['products'] = Product::select('id','product_name')->get();

        $data['basic_plan'] = UserPlan::where('user_plan_name','Basic Plan')->first();
        $data['gold_plan'] = UserPlan::where('user_plan_name','Gold Reseller Plan')->first();
        $data['diamond_plan'] = UserPlan::where('user_plan_name','Diamond Reseller Plan')->first();
        $data['platinum_plan'] = UserPlan::where('user_plan_name','Platinum Reseller Plan')->first();
        
        $product_plan_categories = ProductPlanCategory::with(['product' => function($query){
          $query->where('slug','data');
        }])->latest()->get();
        
        $data['product_plan_categories'] = $product_plan_categories;
        $product_plan_details = ProductPlan::with('automation')->where('id',$id)->first();
        $data['product_plan'] = $product_plan_details;
        return view('admin.product_plans.product_plan_details')->with($data);
    }
    public function admin_fetch_product_plans(Request $request){
        $data = ProductPlan::with(['automation','product_plan_category.network','product_plan_category.product'])
        ->orderBy('updated_at','desc')
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
          // return number_format($data->user_level_1_selling_price,2);
          return $data->user_level_1_selling_price;
        })
        ->addColumn('user_level_2_selling_price',function($data){
          // return number_format($data->user_level_2_selling_price,2);
          return $data->user_level_2_selling_price;
        })
        ->addColumn('user_level_3_selling_price',function($data){
          // return number_format($data->user_level_3_selling_price);
          return $data->user_level_3_selling_price;
        })
        ->addColumn('user_level_4_selling_price',function($data){
          // return number_format($data->user_level_4_selling_price,2);
          return $data->user_level_4_selling_price;
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
        ->addColumn('updated_at',function($data){
          return $data->updated_at;
        })
        ->addColumn('action',function($data){
           // $route = 'transactions.transaction_details';
           $route = route('admin.product_plans.product_plan_details',$data->id);
           $actionBtn = '<a href="'.$route.'" type="button" class="hs-dropdown-toggle ti-btn ti-btn-primary" data-hs-overlay="#hs-vertically-centered-scrollable-modal'.$data->email.'">
           Details
           </a>';
           return $actionBtn;
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
          // return number_format($data->user_level_1_selling_price,2);
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
        
          if($request->validity_in_day == NULL || $request->validity_in_day == ''){
            return response()->json(['status'=>'-1', 'message'=> 'Error: Validity in days not set'  ]);
          }
          
          $fetch_product_plan_category = ProductPlanCategory::with('product')->where('id',$request->product_plan_category_id)->first();
          if(! $fetch_product_plan_category){
            return response()->json(['status'=>'-1', 'message'=> 'Error: Product category not set'  ]);
          }
          if($fetch_product_plan_category && ($fetch_product_plan_category->product->slug == 'airtime' || $fetch_product_plan_category->product->slug == 'utility_bills' ) ){
             if($request->user_plan_1 > 100 || $request->user_plan_2 > 100 || $request->user_plan_3 > 100 || $request->user_plan_4 > 100 ){
               return response()->json(['status'=>'-1', 'message'=> 'Error: Percentage discount cannot be greater than 100% for airtime and utility bills'  ]);
             }
          }
          
          $product_plan = ProductPlan::updateOrCreate([
            'automation_product_plan_id' => $request->id,
            'automation_id' => $request->automation_id,
          ],$data);

          // return response()->json(['status'=>'1', 'message'=>'successfully saved'. 'plan_id:'.$request->id.' auto_id: '.$request->automation_id. 'data:'.json_encode($data),  ]);
          return response()->json(['status'=>'1', 'message'=>'Plan was successfully saved' ]);

    
    }



    //single plan update
    public function update_plan2(Request $request){

        $validator = Validator::make($request->all(), [
          'product_plan_id' => 'required|max:255|exists:product_plans,id',
          'product_plan_name' => 'required|max:255',
          'cost_price' => 'required|numeric|gt:0',
          // 'data_size_in_mb' => 'required|numeric',
          'validity_in_days' => 'required|numeric',
          'default_selling_price' => 'required|numeric',
          'user_level_1_selling_price' => 'required|numeric',
          'user_level_2_selling_price' => 'required|numeric',
          'user_level_3_selling_price' => 'required|numeric',
          'user_level_4_selling_price' => 'required|numeric'

        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
          return response()->json(['status'=> false,'message'=> $validator->errors()->first()]);
        }

       
        if(auth()->user()->email != 'adebsholey4real@gmail.com'){
          return response()->json([
            'status' => false,
            'message'=> 'not authorized',
           ]);
        }
         
         $plan_id = $request->product_plan_id;
         $cost_price = $request->cost_price;
         $visibility = $request->visibility;
         $default_selling_price =  $request->default_selling_price;
         $user_level_1_selling_price =  $request->user_level_1_selling_price;
         $user_level_2_selling_price =  $request->user_level_2_selling_price;
         $user_level_3_selling_price =  $request->user_level_3_selling_price;
         $user_level_4_selling_price =  $request->user_level_4_selling_price;
         $data_size_in_mb =  $request->data_size_in_mb;
         $product_plan_name =  $request->product_plan_name;
         $validity_in_days =  $request->validity_in_days;


        //  $user_level_1_commission =  $request->user_level_1_commission;
        //  $user_level_2_commission =  $request->user_level_2_commission;
        //  $user_level_3_commission =  $request->user_level_3_commission;
        //  $user_level_4_commission =  $request->user_level_4_commission;
        //  $commission_feature =  $request->commission_feature;


         ProductPlan::where('id',$plan_id)->update([
          "product_plan_name" =>  $product_plan_name,
          "cost_price" =>  $cost_price,
          "visibility" =>  $visibility,
          "default_selling_price" =>  $default_selling_price,
          "user_level_1_selling_price" =>  $user_level_1_selling_price,
          "user_level_2_selling_price"=>  $user_level_2_selling_price,
          "user_level_3_selling_price" =>  $user_level_3_selling_price,
          "user_level_4_selling_price" =>  $user_level_4_selling_price,
          // "user_level_1_commission" =>  $user_level_1_commission,
          // "user_level_2_commission"=>  $user_level_2_commission,
          // "user_level_3_commission" =>  $user_level_3_commission,
          // "user_level_4_commission" =>  $user_level_4_commission,
          // "commission_feature" =>  $commission_feature,
          "data_size_in_mb" =>  $data_size_in_mb,
          "validity_in_days" =>  $validity_in_days,
         ]);
     

         sleep(2);
  
         return response()->json([
          'status' => true,
          'message'=> 'successfully updated plan',
         ]);
   }



    //automation update
    public function update(Request $request){
       
      //  return $request->all();
      $validator = Validator::make($request->all(), [
         'product_plan_id' => 'required|max:255|exists:product_plans,id',
         'product_plan_name' => 'required|max:255',
         'product_plan_category_idd' => 'required',
         'automation_product_plan_id' => 'required',
         'cost_price' => 'required|numeric|gt:0',
         'data_size_in_mb' => 'required|numeric',
         'validity_in_days' => 'required|numeric',
         'default_selling_price' => 'required|numeric',
         'user_level_1_selling_price' => 'required|numeric',
         'user_level_2_selling_price' => 'required|numeric',
         'user_level_3_selling_price' => 'required|numeric',
         'user_level_4_selling_price' => 'required|numeric',
         'upline_commission_option' => ['required',Rule::in(['flat','percentage'])],
         'upline_percentage_commission' => 'required|numeric',
         'upline_flat_commission' => 'required|numeric',
         'upline_commission_cap' => 'required|numeric',
       ]);

       if ($validator->stopOnFirstFailure()->fails()) {
         return redirect()->back()->withErrors($validator)->withInput();
       }

       if($request->upline_percentage_commission > 100){
          Session::flash('failure','Upline percentage commission cannot be greater than 100');
          return redirect()->back();
        }

       

       $data = $validator->validated();
       unset($data['product_plan_category_idd']);
       unset($data['product_plan_id']);
       $data['product_plan_category_id'] = $request->product_plan_category_idd;
      
      //  dd($data);

       $create_product_plans = ProductPlan::where('id',$request->product_plan_id)->update($data);
 
       if($create_product_plans){
         Session::flash('success','Product plan successfully updated');
       }else{
         Session::flash('failure','Error occurred while updating product plan');
       }
 
       return redirect()->route('admin.product_plans.index');
   }
}
