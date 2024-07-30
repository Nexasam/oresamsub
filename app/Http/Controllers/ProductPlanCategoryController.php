<?php

namespace App\Http\Controllers;

use App\Models\Network;
use App\Models\Product;
use App\Models\UserPlan;
use App\Models\Automation;
use Illuminate\Http\Request;
use League\CommonMark\Renderer\Inline\TextRenderer;
use Yajra\DataTables\DataTables;
use App\Models\ProductPlanCategory;
use App\Models\BulkDataProductPlans;
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
        // dd('tt');
       
        return view('admin.product_plan_categories.index')->with($data);
    }


    public function view_details($id){
      $product_plan_category = ProductPlanCategory::where('id',$id)->first();
      $bulk_data_plans = BulkDataProductPlans::with('product_plan_category')->where('product_plan_category_id',$id)->paginate(50);
      $user_plans = UserPlan::where('plan_level','<=',env('RESELLER_PLAN_COUNT'))->get();
      $products = Product::select('id','product_name')->get();
      $networks = Network::select('id','network_name')->get();
      $automations = Automation::select('id','automation_name')->get();

      $data['automations'] = $automations;
      $data['products'] = $products;
      $data['networks'] = $networks;
      $data['user_plans'] = $user_plans;
      $data['bulk_data_plans'] = $bulk_data_plans;
      $data['product_plan_category'] = $product_plan_category;
      return view('admin.product_plan_categories.view_details')->with($data);
    }

    public function update_details(Request $request){
      $validator = Validator::make($request->all(), [
        'product_plan_category_name' => 'required|max:255',
        'product_id' => 'required|exists:products,id',
        'network_id' => 'nullable|exists:networks,id',
        'automation_id' => 'required|exists:automations,id',
        'discount_value' => 'required'
      ]);

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

      $data = $validator->validated();
     
      // dd($data);
      if($request->discount_value > 100){
        Session::flash('failure','Discount value cannot be greater than 100 percent');
       
        return redirect()->back();
        
      }

      $create_product_plan_categories = ProductPlanCategory::where('id',$request->id)->update($data);

      if($create_product_plan_categories){
        Session::flash('success','Product plan category: '.$request->product_plan_category_name.' was successfully updated');
      }else{
        Session::flash('failure','Error occurred while creating product plan category');
      }

      return redirect()->route('admin.product_plan_categories.index');
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
         
          // dd($data);

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

    public function toggle_hot_sales(Request $request){
      
      $validator = Validator::make($request->all(), [
        'planCategoryId' => 'required|max:255|exists:product_plan_categories,id',
        'token' => 'required',
      ]);
      

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

      $detail = ProductPlanCategory::where('id',$request->planCategoryId)->first();
      $update = $detail->is_hot_sales ? 0 : 1;
      $detail->update([
        'is_hot_sales' => $update
      ]);
      return response()->json(['status'=>'1', 'message'=>'success' ]);
     
    }

    public function admin_fetch_product_plan_categories(Request $request){
          // => function($query){
          //   $query->where('slug','data');
        // }
        $data = ProductPlanCategory::with(['product','automation','network'])->latest()->get();

      //  return $data;
        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('DT_RowIndex',function($data){
          return $data->id;
        })
        ->addColumn('product_plan_category_name',function($data){
          return $data->product_plan_category_name;
        })
        ->addColumn('product_id',function($data){
            return $data->product->product_name;
        }) 
        ->addColumn('automation_id',function($data){
          return $data->automation->automation_name;
          // $automations = Automation::all();
          
          // $options =  '';
          // foreach ($automations as $automation){
          //   $selected = $data->automation_id == $automation->id ? 'selected' : '';
          //   $value = $automation->id;
          //   $name = $automation->automation_name;
          //         $options .= '<option  '.$selected.' value="'.$value.'"><small>'.$name.'</small></option>';
          // }
          // $automation_display = '<div class="mb-2">';
          // $automation_display .= '<input type="hidden" class="product_category_id" id="product_category_id_'.$data->id.'" value="'.$data->id.'">';
          // $automation_display .= ' <select  class="my-auto ti-form-select update_automation_product_plan_category"  id="'.$data->id.'" name="automation_id_'.$data->id.'"  >';
          // $automation_display .= '<option value="">Select</option>';
          // $automation_display .= $options;
          // $automation_display .= '</select>';
          // $automation_display .= '</select><br>';
          // $automation_display .= '<small class="notify_span" id="notify_span'.$data->id.'"></small>';
          // $automation_display .= ' </div>';
          // // $automation_display .= ' </div>';
          
          // return $automation_display;
      }) 
      ->addColumn('discount_value',function($data){
        return $data->discount_value ?? 'nil';
       })  
      ->addColumn('network_id',function($data){
            return $data->network->network_name ?? 'nil';
        }) 
        ->addColumn('created_at',function($data){
          return $data->created_at;
         }) 
        ->addColumn('is_hot_sales',function($data){
          // onchange="toggleHotSales('.$data->id.')"
          $escapedUrl = htmlspecialchars(json_encode($data->id));
          $token = htmlspecialchars(json_encode(csrf_token()));
          $checked = $data->is_hot_sales == 1 ? 'checked':'';
          $actual_value = $data->is_hot_sales;
          $checkedd = htmlspecialchars(json_encode($actual_value));
          $toggle_btn = '<div class="flex items-center">';
          $toggle_btn .=  '<input onchange="toggleHotSales('.$escapedUrl.','.$token.','.$checkedd.')" type="checkbox" id="hs-basic-with-description-checked'.$data->id.'" class="ti-switch" '.$checked.'>';
          $toggle_btn .=  '<label for="hs-basic-with-description-checked" class="text-sm text-gray-500 ms-3 dark:text-white/70 "></label>';
          $toggle_btn .=  ' <span class="badge rounded-sm bg-success/10 text-success hidden" id="hot_sales_notification'.$data->id.'"></span>  </div>';
          
          return $toggle_btn;
          // return $data->is_hot_sales ? 'YES' : 'NO';
         }) 
        ->addColumn('action', function($data){
            // $actionBtn = ' ';
            $route = route('admin.bulk_data_plans.index',$data->id);
            $view_route = route('admin.product_plan_categories.view_details',$data->id);
            if ($data->product != NULL ){
              $action = '<a href="'.$route.'" class="hs-dropdown-toggle ti-btn ti-btn-primary">Manage Bulk Plans</a>';
             }else{
              $action = '<i>Not applicable</i>';
            }
            $escapedUrl = htmlspecialchars(json_encode($data->id));
            // $action .= '<a href="#" data-modal-target="default-modal'.$data->id.'" data-modal-toggle="default-modal'.$data->id.'"  onclick="testingFunction('.$escapedUrl.')"  class="ti-btn ti-btn-success">Edit</a>';
            $action .= '<a href="'.$view_route.'" class="hs-dropdown-toggle ti-btn ti-btn-success">Edit</a>';
            return $action;
          
        })
        ->escapeColumns([])
        ->make(true);

    }
}
