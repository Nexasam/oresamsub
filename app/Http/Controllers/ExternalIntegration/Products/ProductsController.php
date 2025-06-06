<?php

namespace App\Http\Controllers\ExternalIntegration\Products;

use Exception;
use App\Models\Role;
use App\Models\User;
use App\Models\Network;
use App\Models\Product;
use App\Models\Setting;
use App\Models\UserPlan;
use App\Models\ProductPlan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\ProductPlanCategory;
use App\Http\Controllers\Controller;
use App\Models\BulkDataProductPlans;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Registered;
use App\Traits\JsonResponseWrapperMobile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use App\Http\Services\Sms\Termii\TermiiService;
use App\Http\Services\UserOnboarding\TransactionPinService;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubCableTV;
use App\Http\Services\Api\v1\VendorUsersApi\Products\ProductsService;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubElectricity;


class ProductsController extends Controller
{
    use JsonResponseWrapperMobile;

    public function fetch_networks(Request $request){
        $data = Network::where('visibility',1)->get();
        return $this->success('Networks successfully fetched',data: $data);    
     }
  
     public function fetch_products(Request $request){
        $products = Product::where('visibility',1)->where('active_status',1)->get();
        return $this->success('Products successfully fetched',data: $products);    
     }

     public function fetch_product_plan_categories(Request $request){
        //validate here
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id|max:255',
            'product_slug' => ['nullable',Rule::in(['data', 'airtime', 'utility_bills', 'cable_subscription'])],
            'network_id' => 'nullable'
        ]);

        $data['network_id'] = $request->network_id ?? '';
        $data['product_slug'] = $request->product_slug;
        $data['user_id'] = $request->user_id;
        
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error($validator->errors()->first(), data: $request->all(), code: 403 );    
        }

        if( ($request->product_slug == 'data' || $request->product_slug == 'airtime') && $request->network_id == ''  ){
            return $this->error('Network ID is required', data: [], code: 403 );    
        }
        $result = (new ProductsService())->fetch_product_plan_categories($data);
        // return $this->error('Network ID is required', data: $request->all(), code: 403 );    

        $product_plans_categories = $result['product_plans_categories'];
        
        return $this->success('Product plans category successfully fetched',data: $product_plans_categories);    
     }

     public function fetch_product_plans(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'product_slug' => 'required'
        ]);
        
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error($validator->errors()->first(), data: $request->all(), code: 403 );    
        }
       
        // return response()->json(['status'=>'1','user_level'=>3 ,'message'=>'Product plans fetched','counter' =>5,'data' => $request->all() ]);
        $data['network_id'] = $request->network_id ?? '';
        $data['amount'] = $request->amount ?? '';
        $data['plan_category_id'] = $request->plan_category_id ?? '';
        $data['product_slug'] = $request->product_slug ?? ''; //this is required
        $data['user_id'] = $request->user_id ?? ''; //this is required

        if( ($request->product_slug == 'utility_bills' || $request->product_slug == 'airtime') && $request->amount == ''  ){
          return $this->error('Amount is required', data: [], code: 403 );    
        }

        // return $this->success('Product plans successfully fetched',data: $data);    
        $result = (new ProductsService())->fetch_product_plans($data);   
        $product_plans = $result['product_plans'];
        $plan_level = $result['plan_level'];
    
    
        return $this->success('Product plans successfully fetched',data: $product_plans);

     }

     public function parent_child_website_syncing($email){
        $data['email'] = $email;
        $result = (new ProductsService())->parent_child_website_syncing($data);   
        $data = $result['data'];
   
 
        return $this->success('Website Syncing was successful',data: $data);

     }

    //  public function mobile_bulk_data_plans(Request $request){
    //     $bulk_data_product_plans = BulkDataProductPlans::with('product_plan_category')->where('visibility',1)->get();
    //     return response()->json([
    //         'status' => true,
    //         'code' => 200,
    //         'message' => 'Bulk data product plans successfully fetched',
    //         'data' => $bulk_data_product_plans
    //     ]);
   
    //  }

     public function fetch_transactions(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'string', 'exists:users,id'],
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error('Validation failed', data: $validator->errors()->first(), code: 403 );    
        }

        $date_from = $request->date_from ?? date('Y-m-d', strtotime('-500 days'));
        
        $date_to= $request->date_to ?? date('Y-m-d');

        if(strtotime($date_from) > strtotime($date_to)){
            return $this->error('Date from cannot be greater than Date to', data: $validator->errors()->first(), code: 403 );    
        }

        $product_plan_category_filter = $request->product_plan_category_filter ?? '';
        
        $phone = $request->phone_recharged ?? '';
        

        $limit = $request->limit ?? 2000;
 
        $transactions = Transaction::when(!empty($date_from) && !empty($date_to) , function ($query) use ($date_from,$date_to){
            $date_to = date('Y-m-d', strtotime('+1 day', strtotime($date_to)));
            $query->where('created_at','>=',$date_from)->where('created_at','<=',$date_to);
        })->when(!empty($product_plan_category_filter) , function ($query) use ($product_plan_category_filter){
            $product_plan_ids = ProductPlan::where('product_plan_category_id',$product_plan_category_filter)->pluck('id');
            $query->whereIn('product_plan_id',$product_plan_ids);
        })->when(!empty($phone) , function ($query) use ($phone){
          $query->where('phone_number',$phone);
        })
        ->with(['product_plan'])
        ->where('wallet_category','!=','data_wallet')
        ->where('user_id',$request->user_id)
        ->latest()->limit($limit)->get();
        
        return $this->success('Transactions successfully fetched',data: $transactions);    

   
     }

     public function fetch_single_transaction(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'string', 'exists:users,id'],
            'transaction_id' => ['required', 'string', 'exists:transactions,id'],
        ]);

        if(! Transaction::where('user_id',$request->user_id)->where('id',$request->transaction_id)->first()){
          return $this->error('This match does not exist',data: $request->all(), code: 404);    
        }

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error('Validation failed', data: $validator->errors()->first(), code: 403 );       
        }

        $transactions = Transaction::with(['product_plan'])
        ->where('wallet_category','!=','data_wallet')
        ->where('user_id',$request->user_id)
        ->where('id',$request->transaction_id)
        ->first();

        return $this->success('Single transaction successfully fetched',data: $transactions);    
     }

     /**
     * Store a newly created resource in storage.
     */
    public function buy_data(Request $request)
    {
        // return $request->all();
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'network_id' => 'required',
            'phone_number' => 'required',
            'product_plan_category_id' => 'required',
            'product_plan_id' => 'required',
            'pin' => ['required','string','regex:/^\d{4,5}$/'],
            'wallet_category'=>['required',Rule::in(['main_wallet','data_wallet'])],
            'validatephonenetwork'=>['required',Rule::in([0,1])],
        ]);
        
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error('Validation failed', data: $validator->errors()->first(), code: 403 );    
        }

        // $data1['days_count'] = [1,7,30];
        // $data1['user_id'] = $request->user_id;
        // $check_purchase_limit =  ProductsService::check_purchase_limit($data1);
        // if($check_purchase_limit['status'] == -1){
        //     return $this->error($check_purchase_limit['message'], code: 403 );    
        // }

        $data['network_id'] = $request->network_id;
        $data['phone_number'] = $request->phone_number;
        $data['product_plan_category_id'] = $request->product_plan_category_id;
        $data['product_plan_id'] = $request->product_plan_id;
        $data['pin'] = $request->pin;
        $data['wallet_category'] = $request->wallet_category;
        $data['validatephonenetwork'] = $request->validatephonenetwork;
        $data['user_id'] = $request->user_id;//this is required

        $buy_data = (new ProductsService())->buy_data_service($data);
        // return $buy_data;

        $status = $buy_data['status'];
        $message = $buy_data['message'];
        $data['data'] = $buy_data['data'] ?? [];
        if($status == 1){
            return $this->success('Data was successfully processed',data: $data);    
        }

        return $this->error( $message, data: $buy_data['data'], code: 500);   
    
    }


    /**
     * Buy airtime
     */
    public function buy_airtime(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'network_id' => 'required',
            'phone_number' => 'required',
            // 'product_plan_category_id' => 'nullable',
            'product_plan_id' => 'required',
            'pin' => ['required','string','regex:/^\d{4,5}$/'],
            'amount' => 'required|numeric|gt:0',
            'actual_amount' => 'required|numeric|gt:0',
            'validatephonenetwork'=>['required',Rule::in([0,1])],
        ]);
        
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error('Validation failed', data: $validator->errors()->first(), code: 403 );    
        }

        $data['network_id'] = $request->network_id;
        $data['phone_number'] = $request->phone_number;
        $data['product_plan_category_id'] = $request->product_plan_category_id;
        $data['product_plan_id'] = $request->product_plan_id;
        $data['pin'] = $request->pin;
        $data['amount'] = $request->amount;
        $data['actual_amount'] = $request->actual_amount;
        $data['validatephonenetwork'] = $request->validatephonenetwork;
        $data['user_id'] = $request->user_id;//this is required


        $buy_airtime = (new ProductsService())->buy_airtime_service($data);

        $status = $buy_airtime['status'];
        $message = $buy_airtime['message'];
        $data['data'] = $buy_airtime['data'] ?? [];
        if($status == 1){
            return $this->success('Airtime was successfully processed',data: $data);    
        }
        return $this->error( $message ,data: $data['data'], code: 500);   
    }


    /**
     * validate metre number
    */
    public function validate_metre_number(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'metre_number' => 'required',
            'product_plan_id' => 'required',
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error('Validation failed', data: $validator->errors()->first(), code: 403 );    
        }

        $user_id = $request->user_id; //compute this
        $metre_number = $request->metre_number;
        $plan_id = $request->product_plan_id;

        $plan_details = ProductPlan::where('id',$plan_id)
        ->where('visibility',1)
        ->first();
        if(! $plan_details){
            return $this->error('Plan details not found', code: 404 );    
        }

        $user_details = User::where('id',$user_id)->first();

        if(! $user_details){
            return $this->error('User details not found', code: 404 );    
        }

        $validate_metre_name = (new MegaSubElectricity(metre_number: $metre_number, plan_id: $plan_id, user_id: $user_id))->validateMetreNumber();

        $status = $validate_metre_name['status'] ?? -1;
        $message = $validate_metre_name['message'] ?? '';
        $data = $validate_metre_name['data'] ?? [
            'name' => $validate_metre_name['name'] ?? '',
            'address' => $validate_metre_name['address'] ?? '',
        ];

        if($status == 1){
            return $this->success('Metre validation was successful', data: $data );    
        }
        return $this->error( 'Metre validation was not successful' ,data: $data, code: 500);   
    }

    /**
     * validate smart card name
    */
    public function validate_cable_tv(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'smart_card_number' => 'required',
            'product_plan_id' => 'required'
        ]);

     
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error('Validation failed', data: $validator->errors()->first(), code: 403 );    
        }

        $user_id = $request->user_id; //compute this
        $smart_card_number = $request->smart_card_number;
        $plan_id = $request->product_plan_id;
       
        $plan_details = ProductPlan::where('id',$plan_id)
        ->where('visibility',1)
        ->first();
        if(! $plan_details){
            return $this->error('Plan details not found', code: 404 );    
        }

        $user_details = User::where('id',$user_id)->first();

        if(! $user_details){
            return $this->error('User details not found', code: 404 );    
        }

        $validate_smart_card_number = (new MegaSubCableTV(smart_card_number: $smart_card_number, plan_id: $plan_id, user_id: $user_id))->validateSmartCardNumber();

        $status = $validate_smart_card_number['status'] ?? -1;
        $message = $validate_smart_card_number['message'] ?? '';
        $data = $validate_smart_card_number['data'] ?? [
            'name' => $validate_smart_card_number['name'] ?? '',
            'address' => $validate_smart_card_number['address'] ?? '',
        ];
        
        if($status == 1){
            return $this->success('Smart card validation was successful', data: $data );    
        }
        return $this->error( 'Smart card validation was not successful' ,data: $data, code: 500);   
    }


    /**
     * subscribe utility bill
    */
    public function buy_electricity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'metre_number' => 'required',
            'validation_extra_info' => 'required',
            'electricity_product_plan_category_id' => 'required',
            'electricity_product_plan_id' => 'required',
            // 'wallet_category' => 'required',
            'amount' => 'required',
            'actual_amount' => 'required',
            'pin' => ['required','string','regex:/^\d{4,5}$/'],
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error('Validation failed', data: $validator->errors()->first(), code: 403 );    

        }

        $data['metre_number'] = $request->metre_number;
        $data['validation_extra_info'] = $request->validation_extra_info;
        $data['electricity_product_plan_category_id'] = $request->electricity_product_plan_category_id;
        $data['electricity_product_plan_id'] = $request->electricity_product_plan_id;
        $data['amount'] = $request->amount;
        $data['actual_amount'] = $request->actual_amount;
        
        $data['pin'] = $request->pin;
        $data['user_id'] = $request->user_id; //this is required
        $data['no_of_slots'] = '1';//this is required
        $data['wallet_category'] = 'main_wallet';//this is required
     
        $buy_electricity = (new ProductsService())->buy_electricity_service($data);

        $status = $buy_electricity['status'];
        $message = $buy_electricity['message'];
        $data['data'] = $buy_electricity['data'] ?? [];
        if($status == 1){
            return $this->success('Utility bill purchase was successful',data: $data);    
        }
        return $this->error( $message ,data: $data['data'], code: 500);   
    }

     /**
     * buy cable tv
    */
    public function buy_cable_tv(Request $request)
    {
       

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'smart_card_number' => 'required',
            'validation_customer_name' => 'required',
            'cable_product_plan_category_id' => 'required',
            'cable_product_plan_id' => 'required',
            'pin' => ['required','string','regex:/^\d{4,5}$/']
        ]);

        $data['user_id'] = $request->user_id;//this is required
        $data['smart_card_number'] = $request->smart_card_number;//this is required
        $data['validation_customer_name'] = $request->validation_customer_name;//this is required
        $data['cable_product_plan_category_id'] = $request->cable_product_plan_category_id;//this is required
        $data['cable_product_plan_id'] = $request->cable_product_plan_id;//this is required
        $data['no_of_slots'] = '1';//this is required
        $data['wallet_category'] = 'main_wallet';//this is required
        $data['pin'] = $request->pin;

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error('Validation failed', data: $validator->errors()->first(), code: 403 );    
        }
     
        $buy_cable = (new ProductsService())->buy_cable_service($data);

        $status = $buy_cable['status'];
        $message = $buy_cable['message'];
        $data['data'] = $buy_cable['data'] ?? [];
        if($status == 1){
            return $this->success('Cable TV subscription was successful',data: $data);    
        }
        return $this->error( $message ,data: $data['data'], code: 500);   
    }

     
}
