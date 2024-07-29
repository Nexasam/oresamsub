<?php

namespace App\Http\Controllers;

use App\Models\BulkDataProductPlans;
use App\Models\Product;
use App\Models\ProductPlanCategory;
use App\Models\Transaction;
use Exception;
use App\Models\Role;
use App\Models\User;
use App\Models\Network;
use App\Models\UserPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class MobileApiController extends Controller
{
     public function mobile_networks(){
        $data = Network::where('visibility',1)->get();
            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Networks successfully fetched',
                'data' => $data
            ]);
       

     }
  
     public function mobile_signup(Request $request){


                //TODO: candidate for a service: signup service
                $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'other_names' => ['nullable', 'string', 'max:255'],
                'phone_number' => ['required', 'string', 'max:255'],
                'upline_referral_phone_number' => ['nullable', 'string','exists:users,phone_number' ,'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()::defaults()],
            ]);
        
            $upline_details = User::where('phone_number',$request->upline_referral_phone_number)->first();
            $upline_id = $upline_details != NULL ? $upline_details->id : NULL;
        
            // $upline_id = $upline_details->id;
        
            $role_details = Role::where('role_name','User')->first();
            $default_reseller_plan = UserPlan::where('is_default',1)->first();
            $data['first_name'] = $request->first_name;
            $data['last_name'] = $request->last_name;
            $data['other_names'] = $request->other_names;
            $data['phone_number'] = $request->phone_number;
            $data['upline_id'] = $upline_id;
            $data['email'] = $request->email;
            $data['role'] = $role_details->id;
            $data['user_plan_id'] = $default_reseller_plan->id;
            $data['password'] = Hash::make($request->password);
            // $data['confirm_password'] = Hash::make($request->password_confirmation);
        
            $user = User::create($data);
        
            event(new Registered($user));
        
            // Auth::login($user);
        
            // return redirect(route('dashboard', absolute: false));
        
            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Registration was successfull',
                'data' => $user
            ]);
        
     }

     public function mobile_auth_check(){
        return response()->json([
           'message' => 'authenticated'
        ]);
     }
   
     public function mobile_login(Request $request)
     {
        $request->validate([
             'email' => 'required|email',
             'password' => 'required',
             'device_name' => 'required',
         ]);
  
         $user = User::where('email', $request->email)->first();
  
         if (! $user || ! Hash::check($request->password, $user->password)) {
             logger('oga o'); 
             throw ValidationException::withMessages([
                 'email' => ['The provided credentials are incorrect.'],
             ]);
             
         }
  
         $user->token = $user->createToken($request->device_name)->plainTextToken;
         return response()->json([
           'user' => $user
         ]);
     }

     public function mobile_product_plan_category(Request $request){
        $product_plan_categories = ProductPlanCategory::with(['product','network','automation'])->get();
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Product plan categories successfully fetched',
            'data' => $product_plan_categories
        ]);
   
     }

     public function mobile_products(Request $request){
        $products = Product::where('visibility',1)->where('active_status',1)->get();
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Products successfully fetched',
            'data' => $products
        ]);
   
     }

     public function mobile_bulk_data_plans(Request $request){
        $bulk_data_product_plans = BulkDataProductPlans::with('product_plan_category')->where('visibility',1)->get();
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Bulk data product plans successfully fetched',
            'data' => $bulk_data_product_plans
        ]);
   
     }

     public function mobile_transactions(Request $request){
        $transactions = Transaction::with('user')->get();
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'All transactions successfully fetched',
            'data' => $transactions
        ]);
   
     }

     
}
