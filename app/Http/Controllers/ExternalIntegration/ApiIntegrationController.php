<?php

namespace App\Http\Controllers\ExternalIntegration;

use Exception;
use App\Models\Role;
use App\Models\User;
use App\Models\Network;
use App\Models\Product;
use App\Models\Setting;
use App\Models\UserPlan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\ProductPlanCategory;
use App\Http\Controllers\Controller;
use App\Models\BulkDataProductPlans;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
// use Illuminate\Support\Facades\Password;
use App\Traits\JsonResponseWrapperMobile;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use App\Http\Services\Sms\Termii\TermiiService;
use App\Http\Services\UserOnboarding\TransactionPinService;


class ApiIntegrationController extends Controller
{
    use JsonResponseWrapperMobile;

     public function networks(){
        $data = Network::where('visibility',1)->get();
        $this->success('Network successfully fetched', data: $data);
     }
  
     public function signup(Request $request){
                //TODO: candidate for a service: signup service
                $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string','unique:users,username', 'max:255'],
                'upline_referral_phone_number' => ['nullable', 'string','exists:users,phone_number' ,'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'tnc' => ['required'],
                'password' => ['required', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()::defaults()],
            ]);

            
            $upline_details = User::where('phone_number',$request->upline_referral_phone_number)->first();
            $upline_id = $upline_details != NULL ? $upline_details->id : NULL;

            $role_details = Role::where('role_name','User')->first();
            $default_reseller_plan = UserPlan::where('is_default',1)->first();
            $data['first_name'] = $request->first_name;
            $data['username'] = $request->username;
            $data['last_name'] = $request->last_name;
            $data['upline_id'] = $upline_id;
            $data['email'] = $request->email;
            $data['role_id'] = $role_details->id;
            $data['email_verified_at'] = date('Y-m-d H:i:s');
            $data['user_plan_id'] = $default_reseller_plan->id;
            $data['password'] = Hash::make($request->password);
            $user = User::create($data);
        
            event(new Registered($user));

            $token = $user->createToken($user->id)->plainTextToken;
    
            return $this->success('Registration was successful. Redirecting for phone number verification',token: $token, data: $user);
     }

     public function set_transaction_pin(Request $request){
            
        $request->validate([
            'user_id' => ['required', 'string', 'exists:users,id'],
            'pin' => ['required', 'string','digits:4'], 
            'confirm_pin' => ['required', 'string','digits:4'], 
        ]);

        $data['pin'] = $request->pin;
        $data['confirm_pin'] = $request->confirm_pin;
        $data['user_id'] = $request->user_id;

        $set_pin = (new TransactionPinService())->set_pin($data);

        if($set_pin['status'] == 1){
            return $this->success($set_pin['message']);          
        }

        return $this->error($set_pin['message'], data: $set_pin['data']);          

    }


    public function phone_verification(Request $request){
            
            $request->validate([
                'user_id' => ['required', 'string', 'exists:users,id'],
                'phone_number' => ['required', 'string','unique:users,phone_number'], //unique:users,phone_number
            ]);

            $data['phone_number'] = $request->phone_number;
            $data['user_id'] = $request->user_id;

            $send_otp = (new TermiiService())->send_otp($data);

            if($send_otp['status'] == 1){
                return $this->success($send_otp['message'], data: $send_otp['data']);          
            }

            return $this->error($send_otp['message'], data: $send_otp);          
    }



    public function confirm_phone_verification(Request $request){
            
        $request->validate([
            'user_id' => ['required', 'string', 'exists:users,id'],
            'otp' => ['required', 'string'], //unique:users,phone_number
        ]);

        $data['otp'] = $request->otp;
        $data['user_id'] = $request->user_id;

        $confirm_otp = (new TermiiService())->confirm_otp($data);

        if($confirm_otp['status'] == 1){
            return $this->success($confirm_otp['message'], data: $confirm_otp['data']);          
        }

        return $this->error($confirm_otp['message'], data: $confirm_otp['data']);          
    }

     public function mobile_auth_check(){
        return response()->json([
           'message' => 'authenticated'
        ]);
     }
   
     public function login(Request $request)
     {
        $request->validate([
             'email' => 'required|email|exists:users,email',
             'password' => 'required',
             'device_name' => 'required',
         ]);
  
         $user = User::where('email', $request->email)->first();
  
         if (! $user || ! Hash::check($request->password, $user->password)) {
             logger('oga o'); 
            //  throw ValidationException::withMessages([
            //      'email' => ['The provided credentials are incorrect.'],
            //  ]);  
            return $this->error('The provided credentials are incorrect.',data:$request->all());
             
         }

         $token = $user->createToken($request->email)->plainTextToken;
      
         
         return $this->success('Login was successful',data: $user, token: $token );
     }

   

     public function dashboard(Request $request){
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
 
        $user = User::select('main_wallet','first_name','last_name','email','username')->where('id',$request->user_id)->first();
        $user->main_wallet_formatted = (float)  $user->main_wallet;
        $data['user'] =  $user;
        $data['products'] = Product::where('visibility',1)->where('active_status',1)->get();
        $data['recent_transactions'] = Transaction::where('user_id',$request->user_id)->orderByDesc('updated_at')->limit(10)->get();
        $data['hot_sales'] = ProductPlanCategory::where('is_hot_sales',1)->get();
        // return response()->json([
        //     'status' => true,
        //     'code' => 200,
        //     'message' => 'Products successfully fetched',
        //     'data' => $products
        // ]);

        return $this->success('Dashboard data successfully fetched',data: $data );

   
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

     public function products(Request $request){
        $products = Product::where('visibility',1)->where('active_status',1)->get();
        // return response()->json([
        //     'status' => true,
        //     'code' => 200,
        //     'message' => 'Products successfully fetched',
        //     'data' => $products
        // ]);

        return $this->success('Products successfully fetched',data: $products );

   
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
