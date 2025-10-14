<?php

namespace App\Http\Controllers\Api\v1\VendorUsersApi;

use App\Models\User;
use App\Models\Network;
use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\ProductPlanCategory;
use App\Traits\JsonResponseWrapper;
use App\Http\Controllers\Controller;
use App\Models\BulkDataProductPlans;
use App\Http\Services\DataPlansService;
use App\Http\Services\ProductPlanService;
use Illuminate\Support\Facades\Validator;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubCableTV;
use App\Http\Services\Api\v1\VendorUsersApi\Products\ProductsService;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubElectricity;

// use App\Http\Services\Api\v1\VendorUsersApi\Products\ProductsService;
// use App\Services\Api\Automation\MegaSubPlugAutomation\MegaSubCableTV;

class ProductsVendorController extends Controller
{
 
    use JsonResponseWrapper;


    public function syncplans(Request $request){
        $fetchpplans =   ProductPlan::with([
            'product_plan_category.product',
            'product_plan_category.network'
        ])->get();
        // foreach($fetchpplans as $plann){
            // $pplanservice['user'] = $request->api_user;
            // $pplanservice['network_id'] = $plann->product_plan_category->network->id;
            // $pplanservice['product_id'] = $plann->product_plan_category->product->id;
            // $pplanservice['is_api'] = 'yes';
            $plans = (new ProductPlanService())->fetch_all_data_plans($fetchpplans)['message'];
        // }
       

        return $this->success('All plans successfully fetched',data: $plans);  
    }

    public function fetch_networks(Request $request){  
        $data = Network::where('visibility',1)->select('network_name','api_id')->get();
        return $this->success('Networks successfully fetched',data: $data);    
     }

     public function fetch_data_plans(Request $request){  
        $network = $request->network_id ?? '';
        if($network == ''){
            return $this->error('Network ID is required');    

        }

        $networkuuid = Network::where('api_id',$network)
        ->value('id');
        
        $product_id = Product::where('slug','data')
        ->value('id');
      
        $dataservice['user'] = $request->api_user;
        $dataservice['network_id'] = $networkuuid;
        $dataservice['product_id'] = $product_id;
        $dataservice['is_api'] = 'yes';
        $plans = (new DataPlansService())->fetch_user_data_plans($dataservice)['plans'];

        return $this->success('Data plans successfully fetched',data: $plans);    
     }

     public function fetch_data_transactions(Request $request){
        $validator = Validator::make($request->all(), [
            'date_from' => ['nullable', 'string'],
            'date_to' => ['nullable', 'string'],
            'phone_recharged' => ['nullable', 'string'],
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error('Validation failed', data: $validator->errors()->first(), code: 403 );    
        }

        $user_details = $request->api_user;

        $date_from = $request->date_from ?? date('Y-m-d', strtotime('-2 days'));
        
        $date_to= $request->date_to ?? date('Y-m-d');

        $phone = $request->phone_recharged ?? '';


        if(strtotime($date_from) > strtotime($date_to)){
            return $this->error('Date from cannot be greater than Date to', data: $validator->errors()->first(), code: 403 );    
        }
        

        $limit = $request->limit ?? 500;
        $product_plan_category_filter = $request->product_plan_category_filter ?? '';

        $transactions = Transaction::when(!empty($date_from) && !empty($date_to), function ($query) use ($date_from, $date_to) {
            $date_to = date('Y-m-d', strtotime('+1 day', strtotime($date_to)));
            $query->where('created_at', '>=', $date_from)->where('created_at', '<=', $date_to);
        })
        ->when(!empty($phone), function ($query) use ($phone) {
            $query->where('phone_number', $phone);
        })
        ->with(['product_plan:id,product_plan_name']) // only load what you need
        ->where('wallet_category', '!=', 'data_wallet')
        ->where('transaction_category', 'data')
        ->where('user_id', $user_details->id)
        ->latest()
        ->limit(200)
        ->get([
            'id',
            'product_plan_id',
            'transaction_category',
            'status',
            'balance_before',
            'balance_after',
            'user_screen_message',
            'phone_number',
            'amount',
            'created_at',
            'retry_count',
            'txn_reference',
        ])
        ->map(function ($t) {
          
            return [
                "status" => match($t->status) {
                        "1"   => "success",
                        "2"  => "refunded",
                        "-1"  => "failed",
                        default => "unknown"
                },
                "product_name"      => $t->product_plan->product_plan_name ?? null,
                "balance_before"    => $t->balance_before,
                "balance_after"     => $t->balance_after,
                "user_screen_message" => $t->user_screen_message,
                "phone_number"      => $t->phone_number,
                "amount"      => $t->amount,
                "retry_count"      => $t->retry_count,
                "txn_reference"      => $t->txn_reference,
            ];
        });
        
        return $this->success('Data Transactions successfully fetched',data: $transactions);    
     }


    /**
     * Store a newly created resource in storage.
     */
    public function buy_data(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required',
            'plan' => 'required|exists:product_plans,api_id',
            'reference' => 'required|unique:transactions,txn_reference'
        ]);
        
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error($validator->errors()->first(), code: 403 );    
        }

        // $network_id = Network::where('api_id',$request->network)->value('id');
        $product_plan_id = ProductPlan::with('product_plan_category')->where('api_id',$request->plan)->value('id');
        $getnetwork = ProductPlan::with('product_plan_category.network')->where('api_id',$request->plan)->first();
        $network_id = $getnetwork->product_plan_category->network->id;

        $data['network_id'] = $network_id;
        $data['reference'] = $request->reference ?? NULL;
        $data['phone_number'] = $request->mobile_number;
        $data['product_plan_category_id'] = NULL;
        $data['product_plan_id'] = $product_plan_id;
        $data['pin'] = $request->api_user->pin;
        $data['wallet_category'] = $request->wallet_category ?? 'main_wallet';
        $data['validatephonenetwork'] = $request->validatephonenetwork ?? 1;
        $data['user_id'] = $request->api_user->id;//this is required
        $data['user'] = $request->api_user;//this is required

        $buy_data = (new ProductsService())->buy_data_service($data);

        $status = $buy_data['status'];
        $message = $buy_data['message'];
        $data = $buy_data['data'] ?? [];
        
        $data2 =[
            'id'=>$buy_data['id'],
            'txn_reference'=>$buy_data['txn_reference'] ?? NULL,
            'status'=>$buy_data['status'] ?? NULL,
            'Status'=>$buy_data['Status'] ?? NULL,
            'plan'=>$buy_data['plan'] ?? NULL,
            'balance_before'=>$buy_data['balance_before'],
            'balance_after'=>$buy_data['balance_after'],
            'message'=>$buy_data['message'],
            'user_message'=>$buy_data['user_message'],
            'admin_message'=>$buy_data['admin_message'],
            'plan_network'=>$buy_data['plan_network'],
            'plan_name'=>$buy_data['plan_name'],
            'plan_amount'=>$buy_data['plan_amount'],
            'create_date'=>$buy_data['create_date']
        ];

        if($status == 1){
            return $this->success($buy_data['message'],data: $data2);    
        }

        $status_code = $buy_data['status_code'] ?? 500;
        return $this->error( $message ,data: $data2, code: $status_code);   

    }

    public function fetch_transaction(Request $request){
        $validator = Validator::make($request->all(), [
            'reference' => ['required', 'string', 'exists:transactions,txn_reference'],
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error($validator->errors()->first(), code: 403 );       
        }

        $user_details = $request->api_user;

        $transaction = Transaction::select([
            'status',
            'amount',
            'balance_before',
            'balance_after',
            'user_screen_message',
            'admin_screen_message',
            'created_at',
            'retry_count',
            'txn_reference',
            'product_plan_id' // still needed to join with product_plan
        ])
        ->with([
            'product_plan:id,product_plan_name,api_id'
        ])
        ->where('user_id', $user_details->id)
        ->where('txn_reference', $request->reference)
        ->first();
    

        return $this->success('Transaction successfully fetched',data: $transaction);    
     }

     


    ////////////////////////////BELOW ARE NOT REALLY USED YET

    public function fetch_user_records_with_token($bearer_token){
        $user_details = User::where('api_token',$bearer_token)->first();
        if($user_details){
            return $user_details;
        }

        return false;
    }

  
  
     public function fetch_products(Request $request){
        $bearer_token = $request->bearerToken();
       
        //TODO: revamp to make better
        $user_details = $this->fetch_user_records_with_token($bearer_token);
        if(! $user_details){
            return $this->error('Authentication failed', data: [], code: 403 );    
        }
        $products = Product::where('visibility',1)->where('active_status',1)->get();
        return $this->success('Products successfully fetched',data: $products);    
     }

     public function fetch_product_plan_categories(Request $request){
        $validator = Validator::make($request->all(), [
            'network_id' => 'nullable|exists:networks,id',
            'product_slug' => 'required'
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error($validator->errors()->first(), data: $request->all(), code: 403 );    
        }

        $bearer_token = $request->bearerToken(); 
        //TODO: revamp to make better
        $user_details = $this->fetch_user_records_with_token($bearer_token);
        if(! $user_details){
            return $this->error('Authentication failed', data: [], code: 403 );    
        }

        // $products = ProductPlanCategory::with('network','product')->where('visibility',1)->get();
        $data['network_id'] = $request->network_id;
        $data['product_slug'] = $request->product_slug;
        
        

        if( ($request->product_slug == 'data' || $request->product_slug == 'airtime') && $request->network_id == ''  ){
            return $this->error('Network ID is required', data: [], code: 403 );    
        }

        $result = (new ProductsService())->fetch_product_plan_categories($data);

        $product_plans_categories = $result['product_plans_categories'];
        
        return $this->success('Product plans category successfully fetched',data: $product_plans_categories);    
     }

     public function fetch_product_plans(Request $request){
        $validator = Validator::make($request->all(), [
            'product_slug' => 'required'
        ]);
        
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error($validator->errors()->first(), data: $request->all(), code: 403 );    
        }

        $bearer_token = $request->bearerToken(); 
        //TODO: revamp to make better
        $user_details = $this->fetch_user_records_with_token($bearer_token);
        if(! $user_details){
            return $this->error('Authentication failed', data: [], code: 403 );    
        }

        // return response()->json(['status'=>'1','user_level'=>3 ,'message'=>'Product plans fetched','counter' =>5,'data' => $request->all() ]);
        $data['network_id'] = $request->network_id ?? '';
        $data['amount'] = $request->amount ?? '';
        $data['plan_category_id'] = $request->plan_category_id ?? '';
        $data['product_slug'] = $request->product_slug ?? ''; //this is required
        $data['user_id'] = $user_details->id ?? ''; //this is required

        if( ($request->product_slug == 'utility_bills' || $request->product_slug == 'airtime') && $request->amount == ''  ){
        return $this->error('Amount is required', data: [], code: 403 );    
        }

        if( ($request->product_slug == 'data' || $request->product_slug == 'airtime') && $request->network_id == ''  ){
            return $this->error('Network is required', data: [], code: 403 );    
        }

        // return $this->success('Product plans successfully fetched',data: $data);    
        $result = (new ProductsService())->fetch_product_plans($data);   
        $product_plans = $result['product_plans'];
        $plan_level = $result['plan_level'];
    
    
        return $this->success('Product plans successfully fetched',data: $product_plans);    
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
            'date_from' => ['nullable', 'string'],
            'date_to' => ['nullable', 'string'],
            'phone_recharged' => ['nullable', 'string'],
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error('Validation failed', data: $validator->errors()->first(), code: 403 );    
        }

        $bearer_token = $request->bearerToken(); 
        //TODO: revamp to make better
        $user_details = $this->fetch_user_records_with_token($bearer_token);
        if(! $user_details){
            return $this->error('Authentication failed', data: [], code: 403 );    
        }


        $date_from = $request->date_from ?? date('Y-m-d', strtotime('-2 days'));
        
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
        ->where('user_id',$user_details->id)
        ->latest()->limit($limit)->get();
        
        return $this->success('Transactions successfully fetched',data: $transactions);    

   
     }

     public function fetch_single_transaction(Request $request){
        $validator = Validator::make($request->all(), [
            'transaction_id' => ['required', 'string', 'exists:transactions,id'],
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error('Validation failed', data: $validator->errors()->first(), code: 403 );       
        }

        //TODO: revamp to make better
        $bearer_token = $request->bearerToken(); 
        $user_details = $this->fetch_user_records_with_token($bearer_token);
        if(! $user_details){
            return $this->error('Authentication failed', data: [], code: 403 );    
        }

        $transactions = Transaction::with(['product_plan'])
        ->where('wallet_category','!=','data_wallet')
        ->where('user_id',$user_details->id)
        ->where('id',$request->transaction_id)
        ->first();

        return $this->success('Single transaction successfully fetched',data: $transactions);    
     }

     /**
     * Store a newly created resource in storage.
     */
    public function buy_dataold(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'network_id' => 'required',
            'phone_number' => 'required',
            'product_plan_category_id' => 'required',
            'product_plan_id' => 'required',
            'wallet_category'=>['required',Rule::in(['main_wallet','data_wallet'])],
            'validatephonenetwork'=>['required',Rule::in([0,1])],
        ]);
        
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error('Validation failedvvv', data: $validator->errors()->first(), code: 403 );    
        }

        //TODO: revamp to make better
        $bearer_token = $request->bearerToken(); 
        $user_details = $this->fetch_user_records_with_token($bearer_token);
        if(! $user_details){
            return $this->error('Authentication failed', data: [], code: 403 );    
        }

        $data['network_id'] = $request->network_id;
        $data['phone_number'] = $request->phone_number;
        $data['product_plan_category_id'] = $request->product_plan_category_id;
        $data['product_plan_id'] = $request->product_plan_id;
        $data['pin'] = $user_details->pin;
        $data['wallet_category'] = $request->wallet_category;
        $data['validatephonenetwork'] = $request->validatephonenetwork;
        $data['user_id'] = $user_details->id;//this is required

        $buy_data = (new ProductsService())->buy_data_service($data);
        
       

        $status = $buy_data['status'];
        $message = $buy_data['message'];
        $data = $buy_data['data'] ?? [];
        if($status == 1){
            return $this->success('Data was successfully processed',data: $data);    
        }

        return $this->error( $message ,data: $data, code: 500);   
    
    }


    /**
     * Buy airtime
     */
    public function buy_airtime(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network_id' => 'required',
            'phone_number' => 'required',
            // 'product_plan_category_id' => 'required',
            'product_plan_id' => 'required',
            // 'pin' => ['required','string','regex:/^\d{4,5}$/'],
            'amount' => 'required|numeric|gt:0',
            'actual_amount' => 'required|numeric|gt:0',
            'validatephonenetwork'=>['required',Rule::in([0,1])],
        ]);
        
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error('Validation failed', data: $validator->errors()->first(), code: 403 );    
        }
        

        //TODO: revamp to make better
        $bearer_token = $request->bearerToken(); 
        $user_details = $this->fetch_user_records_with_token($bearer_token);
        if(! $user_details){
            return $this->error('Authentication failed', data: [], code: 403 );    
        }

        $data['network_id'] = $request->network_id;
        $data['phone_number'] = $request->phone_number;
        $data['product_plan_category_id'] = $request->product_plan_category_id;
        $data['product_plan_id'] = $request->product_plan_id;
        $data['pin'] = $user_details->pin;
        $data['amount'] = $request->amount; //the affiliate price: dontuser
        $data['actual_amount'] = $request->actual_amount; //the parent price
        $data['validatephonenetwork'] = $request->validatephonenetwork;
        $data['user_id'] = $user_details->id;//this is required
        logger($data);


        $buy_airtime = (new ProductsService())->buy_airtime_service($data);

        $status = $buy_airtime['status'];
        $message = $buy_airtime['message'];
        $data = $buy_airtime['data'] ?? [];
        if($status == 1){
            return $this->success('Airtime was successfully being processed',data: $data);    
        }
        return $this->error( $message ,data: $data, code: 500);   
    }


    /**
     * validate metre number
    */
    public function validate_metre_number(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'metre_number' => 'required',
            'product_plan_id' => 'required',
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error('Validation failed', data: $validator->errors()->first(), code: 403 );    
        }

        $bearer_token = $request->bearerToken(); 
        //TODO: revamp to make better
        $user_details = $this->fetch_user_records_with_token($bearer_token);
        if(! $user_details){
            return $this->error('Authentication failed', data: [], code: 403 );    
        }

        $user_id = $user_details->id; //compute this
        $metre_number = $request->metre_number;
        $plan_id = $request->product_plan_id;
        // $pin = $request->pin;
       
        $plan_details = ProductPlan::where('id',$plan_id)
        ->where('visibility',1)
        ->first();
        if(! $plan_details){
            return $this->error('Plan details not found', code: 404 );    
        }

        // if($user_details->pin != $pin){
        //     return $this->error('User pin is incorrect', code: 403 );    
        // }

        $validate_metre_name = (new MegaSubElectricity(metre_number: $metre_number, plan_id: $plan_id, user_id: $user_id))->validateMetreNumber();

        // return $validate_metre_name;

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
            'smart_card_number' => 'required',
            'product_plan_id' => 'required'
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error('Validation failed', data: $validator->errors()->first(), code: 403 );    
        }

         //TODO: revamp to make better
         $bearer_token = $request->bearerToken(); 
         $user_details = $this->fetch_user_records_with_token($bearer_token);
         if(! $user_details){
             return $this->error('Authentication failed', data: [], code: 403 );    
         }

        $user_id = $user_details->id; //compute this
        $smart_card_number = $request->smart_card_number;
        $plan_id = $request->product_plan_id;
       
        $plan_details = ProductPlan::where('id',$plan_id)
        ->where('visibility',1)
        ->first();
        if(! $plan_details){
            return $this->error('Plan details not found', code: 404 );    
        }

        
        // if($user_details->pin != $pin){
        //     return $this->error('User pin is incorrect', code: 403 );    
        // }

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
            // 'user_id' => 'required',
            'metre_number' => 'required',
            'validation_extra_info' => 'required',
            'electricity_product_plan_category_id' => 'required',
            'electricity_product_plan_id' => 'required',
            // 'wallet_category' => 'required',
            'actual_amount' => 'required|numeric|gt:0',
            'amount' => 'required|numeric|gt:0',
            // 'pin' => ['required','string','regex:/^\d{4,5}$/'],
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error('Validation failed', data: $validator->errors()->first(), code: 403 );    
        }

        //TODO: revamp to make better
        $bearer_token = $request->bearerToken(); 
        $user_details = $this->fetch_user_records_with_token($bearer_token);
        if(! $user_details){
            return $this->error('Authentication failed', data: [], code: 403 );    
        }

        $data['metre_number'] = $request->metre_number;
        $data['validation_extra_info'] = $request->validation_extra_info;
        $data['electricity_product_plan_category_id'] = $request->electricity_product_plan_category_id;
        $data['electricity_product_plan_id'] = $request->electricity_product_plan_id;
        $data['amount'] = $request->amount;
        $data['actual_amount'] = $request->actual_amount; //this is what is needed
        $data['pin'] = $user_details->pin;
        $data['user_id'] = $user_details->id; //this is required
        $data['no_of_slots'] = '1';//this is required
        $data['wallet_category'] = 'main_wallet';//this is required
     
        $buy_electricity = (new ProductsService())->buy_electricity_service($data);
        $data['validation_address'] = $buy_electricity->validation_address ??  'nil';

        $status = $buy_electricity['status'];
        $message = $buy_electricity['message'];
        $data = $buy_electricity['data'] ?? [];
        if($status == 1){
            return $this->success('Utility bill purchase was successful',data: $data);    
        }
        return $this->error( $message ,data: $data, code: 500);   
    }

     /**
     * buy cable tv
    */
    public function buy_cable_tv(Request $request)
    {
       

        $validator = Validator::make($request->all(), [
            'smart_card_number' => 'required',
            'validation_customer_name' => 'required',
            'cable_product_plan_category_id' => 'required',
            'cable_product_plan_id' => 'required',
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error('Validation failed', data: $validator->errors()->first(), code: 403 );    

        }

          //TODO: revamp to make better
          $bearer_token = $request->bearerToken(); 
          $user_details = $this->fetch_user_records_with_token($bearer_token);
          if(! $user_details){
              return $this->error('Authentication failed', data: [], code: 403 );    
          }

          
        $data['user_id'] = $user_details->id;//this is required
        $data['smart_card_number'] = $request->smart_card_number;//this is required
        $data['validation_customer_name'] = $request->validation_customer_name;//this is required
        $data['cable_product_plan_category_id'] = $request->cable_product_plan_category_id;//this is required
        $data['cable_product_plan_id'] = $request->cable_product_plan_id;//this is required
        $data['no_of_slots'] = '1';//this is required
        $data['wallet_category'] = 'main_wallet';//this is required
        $data['pin'] = $user_details->pin;

  
        $buy_cable = (new ProductsService())->buy_cable_service($data);

        $status = $buy_cable['status'];
        $message = $buy_cable['message'];
        $data = $buy_cable['data'] ?? [];
        if($status == 1){
            return $this->success('Cable TV subscription was successful',data: $data);    
        }
        return $this->error( $message ,data: $data, code: 500);   
    }
   

}
