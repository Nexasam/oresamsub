<?php

namespace App\Http\Controllers\Api\v1\VendorUsersApi;

use App\Models\User;
use App\Models\Network;
use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\Transaction;
use App\Services\Automation\AutomationLogic;
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
    
        $user = $request->api_user ?? null;
        logger('AffiliateProductsVendorController syncplans1111: ', ['user' => $user->id]);
        $plans = (new ProductPlanService())->fetch_all_data_plans($fetchpplans,$user);
        logger('AffiliateProductsVendorController syncplans: ', ['plans' => $plans]);

       
        return $this->success('All plans successfully fetched',data: $plans);  
    }


     /**
     * Buy airtime
     */
    public function buy_airtime(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'actual_amount' => 'required|numeric|gt:0',
            'amount' => 'required|numeric|gt:49',
            'mobile_number' => 'required',
            'plan' => 'required|exists:product_plans,api_id',
            'reference' => 'required|unique:transactions,txn_reference'
        ]);
        
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error('Validation failed', data: $validator->errors()->first(), code: 403 );    
        }
        
        
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error($validator->errors()->first(), code: 403 );    
        }

        $getnetwork = ProductPlan::with('product_plan_category.network','product_plan_category.product')->where('api_id',$request->plan)->first();
        $product_plan_category_id = $getnetwork->product_plan_category->id;
        $network_id = $getnetwork->product_plan_category->network->id;
        $product_id = $getnetwork->product_plan_category->product->id;
        $product_plan_id = $getnetwork->id;
    

        $data['network_id'] = $network_id;
        $data['product_id'] = $product_id;
        $data['reference'] = $request->reference ?? NULL;
        $data['phone_number'] = $request->mobile_number;
        $data['product_plan_category_id'] = $product_plan_category_id;
        $data['product_plan_id'] = $product_plan_id;
        $data['pin'] = $request->api_user->pin;
        $data['wallet_category'] = $request->wallet_category ?? 'main_wallet';
        $data['validatephonenetwork'] = $request->validatephonenetwork ?? 1;
        $data['user_id'] = $request->api_user->id;//this is required
        $data['user'] = $request->api_user;//this is required
        $data['amount'] = $request->amount;//this is required
        $data['actual_amount'] = $request->amount;//this is required
    

        $buy_airtime = (new ProductsService())->buy_airtime_service($data);

        $status = $buy_airtime['status'];
        $message = $buy_airtime['message'];
        $data = $buy_airtime['data'] ?? [];
        
        $data2 =[
            'id'=>$buy_airtime['id'] ?? NULL,
            'txn_reference'=>$buy_airtime['txn_reference'] ?? NULL,
            'status'=>$buy_airtime['status'],
            'Status'=>$buy_airtime['Status'] ?? NULL,
            'plan'=>$buy_airtime['plan'] ?? NULL,
            'balance_before'=>$buy_airtime['balance_before'] ?? NULL,
            'balance_after'=>$buy_airtime['balance_after'] ?? NULL,
            'message'=>$buy_airtime['message'] ?? NULL,
            'user_message'=>$buy_airtime['user_message'] ?? NULL,
            'admin_message'=>$buy_airtime['admin_message'] ?? NULL,
            'plan_network'=>$buy_airtime['plan_network'] ?? NULL,
            'plan_name'=>$buy_airtime['plan_name'] ?? NULL,
            'plan_amount'=>$buy_airtime['plan_amount'] ?? NULL,
            'create_date'=>$buy_airtime['create_date'] ?? NULL
        ];

        if($status == 1){
            return $this->success($buy_airtime['message'],data: $data2);    
        }

        $status_code = $buy_airtime['status_code'] ?? 500;
        return $this->error( $message ,data: $data2, code: $status_code);

    }

      /**
     * subscribe utility bill
    */
    public function buy_electricity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "amount"=>"required|integer",
            "plan"=>"required",
            "metre_number" =>"required",
            "validation_extra_info" =>"required|string",
            "validated_address" =>"nullable|string",
            'reference' => 'required|unique:transactions,txn_reference'
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error('Validation failedddd', data: $validator->errors()->first(), code: 403 );    
        }

        $getiddd = ProductPlan::with('product_plan_category.product')->where('api_id',$request->plan)->first();
        $product_id = $getiddd->product_plan_category->product->id;
        $product_plan_category_id = $getiddd->product_plan_category->id;
        $product_plan_id = $getiddd->id;
       
        // $buy_electricity = (new ProductsService())->buy_electricity_service($data);
        $data['validation_address'] = $buy_electricity->validation_address ??  'nil';
        $data['metre_number'] = $request->metre_number;
        $data['validation_extra_info'] = $request->validation_extra_info;
        $data['validated_address'] = $request->validated_address;
        $data['electricity_product_plan_category_id'] = $product_plan_category_id;
        $data['electricity_product_plan_id'] = $product_plan_id;
        $data['amount'] = $request->amount;
        $data['reference'] = $request->reference;
        $data['actual_amount'] = $request->actual_amount; //this is what is needed
        $data['pin'] = '';
        $data['no_of_slots'] = '1';//this is required
        $data['wallet_category'] = 'main_wallet';//this is required
        $data['user_id'] = $request->api_user->id;//this is required
        $data['user'] = $request->api_user;//this is required
        $data['product_id'] = $product_id;//this is required
        // logger('parent request'.$getnetwork);

        $buy_electricityy = (new ProductsService())->buy_electricity_service($data);
        $status = $buy_electricityy['status'];
        $message = $buy_electricityy['message'];
        $data = $buy_electricityy['data'] ?? [];
        
        $data2 =[
            'id'=>$buy_electricityy['id'] ?? NULL,
            'token'=>$buy_electricityy['token'] ?? NULL,
            'txn_reference'=>$buy_electricityy['txn_reference'] ?? NULL,
            'status'=>$buy_electricityy['status'],
            'Status'=>$buy_electricityy['Status'] ?? NULL,
            'plan'=>$buy_electricityy['plan'] ?? NULL,
            'balance_before'=>$buy_electricityy['balance_before'] ?? NULL,
            'balance_after'=>$buy_electricityy['balance_after'] ?? NULL,
            'message'=>$buy_electricityy['message'] ?? NULL,
            'user_message'=>$buy_electricityy['user_message'] ?? NULL,
            'admin_message'=>$buy_electricityy['admin_message'] ?? NULL,
            'plan_network'=>$buy_electricityy['plan_network'] ?? NULL,
            'plan_name'=>$buy_electricityy['plan_name'] ?? NULL,
            'plan_amount'=>$buy_electricityy['plan_amount'] ?? NULL,
            'create_date'=>$buy_electricityy['create_date'] ?? NULL
        ];

        if($status == 1){
            return $this->success($buy_electricityy['message'],data: $data2);    
        }

        $status_code = $buy_electricityy['status_code'] ?? 500;
        return $this->error( $message ,data: $data2, code: $status_code); 


    }

    public function buy_cable_tv(Request $request)
    {
       

        $validator = Validator::make($request->all(), [
            'plan' => 'required|exists:product_plans,api_id',
            'reference' => 'required|unique:transactions,txn_reference',
            'smart_card_number' => 'required',
            'validation_customer_name' => 'required',
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error('Validation failed', data: $validator->errors()->first(), code: 403 );    
        }

        $getiddd = ProductPlan::with('product_plan_category.product')->where('api_id',$request->plan)->first();
        $product_id = $getiddd->product_plan_category->product->id;
        $product_plan_category_id = $getiddd->product_plan_category->id;
        $product_plan_id = $getiddd->id;

          
        $data['user_id'] = $request->api_user->id;//this is required
        $data['user'] = $request->api_user;//this is required
        $data['smart_card_number'] = $request->smart_card_number;//this is required
        $data['validation_customer_name'] = $request->validation_customer_name;//this is required
        $data['cable_product_plan_category_id'] = $product_plan_category_id;//this is required
        $data['cable_product_plan_id'] = $product_plan_id;//this is required
        $data['no_of_slots'] = '1';//this is required
        $data['wallet_category'] = 'main_wallet';//this is required
        
        $data['pin'] = $request->api_user->pin ?? '';
        $data['reference'] = $request->reference ?? '';



        $buy_cablee = (new ProductsService())->buy_cable_service($data);
        $status = $buy_cablee['status'];
        $message = $buy_cablee['message'];
        $data = $buy_cablee['data'] ?? [];
        
        $data2 =[
            'id'=>$buy_cablee['id'] ?? NULL,
            'txn_reference'=>$buy_cablee['txn_reference'] ?? NULL,
            'status'=>$buy_cablee['status'],
            'Status'=>$buy_cablee['Status'] ?? NULL,
            'plan'=>$buy_cablee['plan'] ?? NULL,
            'balance_before'=>$buy_cablee['balance_before'] ?? NULL,
            'balance_after'=>$buy_cablee['balance_after'] ?? NULL,
            'message'=>$buy_cablee['message'] ?? NULL,
            'user_message'=>$buy_cablee['user_message'] ?? NULL,
            'admin_message'=>$buy_cablee['admin_message'] ?? NULL,
            'plan_network'=>$buy_cablee['plan_network'] ?? NULL,
            'plan_name'=>$buy_cablee['plan_name'] ?? NULL,
            'plan_amount'=>$buy_cablee['plan_amount'] ?? NULL,
            'create_date'=>$buy_cablee['create_date'] ?? NULL
        ];

        if($status == 1){
            return $this->success($buy_cablee['message'],data: $data2);    
        }

        $status_code = $buy_cablee['status_code'] ?? 500;
        return $this->error( $message ,data: $data2, code: $status_code); 


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

     public function fetch_airtime_plans(Request $request){  
        $network = $request->network_id ?? '';
        if($network == ''){
            return $this->error('Network ID is required');    

        }

        $networkuuid = Network::where('api_id',$network)
        ->value('id');
        
        $product_id = Product::where('slug','airtime')
        ->value('id');
      
        $airtimeservice['user'] = $request->api_user;
        $airtimeservice['network_id'] = $networkuuid;
        $airtimeservice['product_id'] = $product_id;
        $airtimeservice['is_api'] = 'yes';
        $plans = (new DataPlansService())->fetch_user_data_plans($airtimeservice)['plans'];

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
        // dd('buy data abeg');
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required',
            'plan' => 'required|exists:product_plans,api_id',
            'reference' => 'required|unique:transactions,txn_reference'
        ]);
        
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error($validator->errors()->first(), code: 403 );    
        }

        // $network_id = Network::where('api_id',$request->network)->value('id');
        // $product_plan_id = ProductPlan::where('api_id',$request->plan)->value('id');
        $getnetwork = ProductPlan::with('product_plan_category.network','product_plan_category.product')->where('api_id',$request->plan)->first();
        $network_id = $getnetwork->product_plan_category->network->id;
        $product_id = $getnetwork->product_plan_category->product->id;
        $product_plan_id = $getnetwork->id;
        // logger('tttt'.$network_id.' '.$product_id.' '.$product_plan_id );


        $data['network_id'] = $network_id;
        $data['product_id'] = $product_id;
        $data['reference'] = $request->reference ?? NULL;
        $data['phone_number'] = $request->mobile_number;
        $data['product_plan_category_id'] = NULL;
        // $data['product_plan_id'] = $product_plan_id;
        $data['product_plan_id'] = $product_plan_id;
        $data['pin'] = $request->api_user->pin;
        $data['wallet_category'] = $request->wallet_category ?? 'main_wallet';
        $data['validatephonenetwork'] = $request->validatephonenetwork ?? 1;
        $data['user_id'] = $request->api_user->id;//this is required
        $data['user'] = $request->api_user;//this is required
        // logger('parent request'.$getnetwork);
        
        $buy_data = (new ProductsService())->buy_data_service($data);
        $status = $buy_data['status'];
        $message = $buy_data['message'];
        $data = $buy_data['data'] ?? [];
        
        $data2 =[
            'id'=>$buy_data['id'] ?? NULL,
            'txn_reference'=>$buy_data['txn_reference'] ?? NULL,
            'status'=>$buy_data['status'],
            'Status'=>$buy_data['Status'] ?? NULL,
            'plan'=>$buy_data['plan'] ?? NULL,
            'balance_before'=>$buy_data['balance_before'] ?? NULL,
            'balance_after'=>$buy_data['balance_after'] ?? NULL,
            'message'=>$buy_data['message'] ?? NULL,
            'user_message'=>$buy_data['user_message'] ?? NULL,
            'admin_message'=>$buy_data['admin_message'] ?? NULL,
            'plan_network'=>$buy_data['plan_network'] ?? NULL,
            'plan_name'=>$buy_data['plan_name'] ?? NULL,
            'plan_amount'=>$buy_data['plan_amount'] ?? NULL,
            'create_date'=>$buy_data['create_date'] ?? NULL
        ];

        if($status == 1){
            return $this->success($buy_data['message'],data: $data2);    
        }

        $status_code = $buy_data['status_code'] ?? 500;
        return $this->error( $message ,data: $data2, code: $status_code);   

    }


    /**
    * Store a newly created resource in storage. FOR MSORG WEBSITES:
    */
    public function buy_datav2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'mobile_number' => 'required',
            // 'plan' => 'required|exists:product_plans,api_id',
            // 'reference' => 'required|unique:transactions,txn_reference'
            // "network"=>'required|exists:networks,api_id',
            "mobile_number"=>"required",
            "plan"=>'required|exists:product_plans,api_id',
            "Ported_number"=>'required'
        ]);
        
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error($validator->errors()->first(), code: 403 );    
        }

        // $network_id = Network::where('api_id',$request->network)->value('id');
        $product_plan_id = ProductPlan::with('product_plan_category')->where('api_id',$request->plan)->value('id');
        $getnetwork = ProductPlan::with('product_plan_category.network')->where('api_id',$request->plan)->first();
        $getnetworkcat = $getnetwork->product_plan_category->product_plan_category_name; 
        $network_id = Network::where('api_id',$request->network)->value('id');
        $product_id = $getnetwork->product_plan_category->product->id;
        $data['network_id'] = $network_id;
        $data['product_id'] = $product_id;
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
        $statusw = $buy_data['Status'] ?? 'failed';
        $message = $buy_data['message'];
        $data = $buy_data['data'] ?? [];


        $data2 = [
            'id'              => $buy_data['id'] ?? null,
            'ident'           => $buy_data['ident'] ?? null,
            'payment_medium'  => $buy_data['payment_medium'] ?? 'MAIN WALLET',
            'duration'        => $getnetwork->validity_in_days.' DAYS',
            'plan_type'       => $getnetworkcat,
            'network'         => $request->network,
            'apiresponse'     => $buy_data['user_message'] ?? $buy_data['message'] ?? null,
            'api_response'    => $buy_data['admin_message'] ?? $buy_data['message'] ?? null,
            'balance_before'  => $buy_data['balance_before'] ?? null,
            'balance_after'   => $buy_data['balance_after'] ?? null,
            'mobile_number'   => $buy_data['mobile_number'] ?? null,
            'plan'            => (int) $request->plan,
            'Status'          => $statusw,
            'plan_network'    => $buy_data['plan_network'] ?? null,
            'plan_name'       => $buy_data['plan_name'] ?? null,
            'plan_amount'     => $buy_data['plan_amount'] ?? null,
            'create_date'     => $buy_data['create_date'] ?? now(),
            'Ported_number'   => $request->Ported_number,
        ];
        
        $status_code = $buy_data['status_code'] ?? 200;
        $message = $buy_data['message'] ?? 'Transaction processed successfully.';
        
        if ($statusw == 'successful') {
            return response()->json($data2, 200, [], JSON_PRETTY_PRINT);
        }
        
        return response()->json($data2, $status_code, [], JSON_PRETTY_PRINT);
    }

   
    public function buyService(Request $request){
        // return $this->success('Buy service endpoint hit',data: $request->all());
        
        $validator = Validator::make($request->all(), [
            // 'mobile_number' => 'required',
            // 'plan' => 'required|exists:product_plans,api_id',
            // 'reference' => 'required|unique:transactions,txn_reference'
            // "network"=>'required|exists:networks,api_id',
            "mobile_number"=>"required",
            "plan"=>'required|exists:product_plans,api_id',
            "reference"=>'required|unique:transactions,txn_reference'
        ]);
        //data productt only for now.
        
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error($validator->errors()->first(), code: 403 );    
        }

        // $network_id = Network::where('api_id',$request->network)->value('id');
        $product_plan_id = ProductPlan::with('product_plan_category')->where('api_id',$request->plan)->value('id');
        $getnetwork = ProductPlan::with(['product_plan_category.network','product_plan_category.product'])->where('api_id',$request->plan)->first();
        $getnetworkcat = $getnetwork->product_plan_category->product_plan_category_name; 
        // $network_id = Network::where('api_id',$request->network)->value('id');
        $product_id = $getnetwork->product_plan_category->product->id;
        $network_id = $getnetwork->product_plan_category->network->id;

        $data['network_id'] = $network_id;
        $data['product_id'] = $product_id;

        $data['reference'] = $request->reference ?? NULL;
        $data['phone_number'] = $request->mobile_number;
        $data['product_plan_category_id'] = NULL;
        $data['product_plan_id'] = $product_plan_id;
        $data['pin'] = $request->api_user->pin;
        $data['wallet_category'] = $request->wallet_category ?? 'main_wallet';
        $data['validatephonenetwork'] = $request->validatephonenetwork ?? 1;
        $data['user_id'] = $request->api_user->id;//this is required
        $data['user'] = $request->api_user;//this is required

        // return $data;

        $buy_data = (new ProductsService())->buy_data_service_one_api($data);

        return response()->json($buy_data, $buy_data['status_code'], [], JSON_PRETTY_PRINT);

        // return $buy_data;

        // $status = $buy_data['status'];
        // $statusw = $buy_data['Status'] ?? 'failed';
        // $message = $buy_data['message'];
        // $data = $buy_data['data'] ?? [];


        // $data2 = [
        //     // 'status'          => $statusw,
        //     'status'          => $statusw,
        //     'id'              => $buy_data['id'] ?? null,
        //     'ident'           => $buy_data['ident'] ?? null,
        //     'payment_medium'  => $buy_data['payment_medium'] ?? 'MAIN WALLET',
        //     'duration'        => $getnetwork->validity_in_days.' DAYS',
        //     'plan_type'       => $getnetworkcat,
        //     'network'         => $getnetwork->network->network_name ?? null,
        //     'apiresponse'     => $buy_data['user_message'] ?? $buy_data['message'] ?? null,
        //     // 'api_response'    => $buy_data['admin_message'] ?? $buy_data['message'] ?? null,
        //     'balance_before'  =>  $buy_data['balance_before'] ?? null,
        //     'balance_after'   =>  $buy_data['balance_after'] ?? null,
        //     'mobile_number'   =>  $buy_data['mobile_number'] ?? null,
        //     'plan'            => (int) $request->plan,
        //     'plan_network'    => $buy_data['plan_network'] ?? null,
        //     'plan_name'       => $buy_data['plan_name'] ?? null,
        //     'plan_amount'     => $buy_data['plan_amount'] ?? null,
        //     'service_charge'     => $buy_data['service_charge'] ?? null,
        //     'create_date'     => $buy_data['create_date'] ?? now()
        //     // 'Ported_number'   => true,
        // ];
        
        // $status_code = $buy_data['status_code'] ?? 200;
        // $message = $buy_data['message'] ?? 'Transaction processed successfully.';
        
        // if ($statusw == 'successful') {
        //     return response()->json($data2, 200, [], JSON_PRETTY_PRINT);
        // }
        
        // return response()->json($data2, $status_code, [], JSON_PRETTY_PRINT);  
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
     * validate metre number
    */
    public function validate_metre_number(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'metre_number' => 'required',
            'plan' => 'required|exists:product_plans,api_id',
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
        $plan_id = $request->plan;
        // $pin = $request->pin;
       
        $plan_details = ProductPlan::with('automation')->where('api_id',$plan_id)
        ->where('visibility',1)
        ->first();
        if(! $plan_details){
            return $this->error('Plan details not found', code: 404 );    
        }


        // $validate_metre_name = (new MegaSubElectricity(metre_number: $metre_number, plan_id: $plan_id, user_id: $user_id))->validateMetreNumber();
        // $validate_metre_name = (new MegaSubElectricity(metre_number: $metre_number, plan_id: $plan_id, user_id: $user_id))->validateMetreNumber();

       
        $data['automation_id'] = $plan_details->automation->id; //automation id
        $data['automation_details'] = $plan_details->automation; //automation details   
        $data['network_id'] = ""; //network id
        $data['plan_id'] = $plan_details->id; //plan id
        $data['phone_number'] = ""; //phone number  
        $data['metre_number'] = $metre_number; //smart card number
        $data['token'] = ""; //token
        $data['url'] = ""; //url
        $data['amount'] = ""; //amount
        $validate_metre_details = AutomationLogic::validateElectricitySubscrption($data);
        logger('metre validation result'.json_encode($validate_metre_details));
             
      

        $status = $validate_metre_details['status'] ?? -1;
        $message = $validate_metre_details['message'] ?? 'nil';
        $data = $validate_metre_details['data'] ?? [
            'name' => $validate_metre_details['name'] ?? 'nil',
            'address' => $validate_metre_details['address'] ?? 'nil',
        ];
        
        if($status == 1){
            return $this->success('Metre number validation was successful', data: $data );    
        }
        return $this->error( 'Metre number  validation was not successful' ,data: $data, code: 500);   
    }

    /**
     * validate smart card name
    */
    public function validate_cable_tv(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'smart_card_number' => 'required',
            'plan' => 'required|exists:product_plans,api_id',
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->error('Validation failed', data: $validator->errors()->first(), code: 403 );    
        }

        $user_details = $request->api_user;
        $user_id = $user_details->id; //compute this
        $smart_card_number = $request->smart_card_number;
        $plan_id = $request->plan;
       
        $plan_details = ProductPlan::with('automation')->where('api_id',$plan_id)
        ->where('visibility',1)
        ->first();
        if(! $plan_details){
            return $this->error('Plan details not found', code: 404 );    
        }

        // $validate_smart_card_number = (new MegaSubCableTV(smart_card_number: $smart_card_number, plan_id: $plan_id, user_id: $user_id))->validateSmartCardNumber();
        // $automation_slug = $plan_details->automation->slug;
        $data['automation_id'] = $plan_details->automation->id; //automation id
        $data['automation_details'] = $plan_details->automation; //automation details   
        $data['network_id'] = ""; //network id
        $data['plan_id'] = $plan_details->id; //plan id
        $data['phone_number'] = ""; //phone number  
        $data['smart_card_number'] = $smart_card_number; //smart card number
        $data['token'] = ""; //token
        $data['url'] = ""; //url
        $data['amount'] = ""; //amount
        $validate_smart_card_number = AutomationLogic::validateCableSubscription($data);
        logger('cable validation result'.json_encode($validate_smart_card_number));
             
      

        $status = $validate_smart_card_number['status'] ?? -1;
        $message = $validate_smart_card_number['message'] ?? 'nil';
        $data = $validate_smart_card_number['data'] ?? [
            'name' => $validate_smart_card_number['name'] ?? 'nil',
            'address' => $validate_smart_card_number['address'] ?? 'nil',
        ];
        
        if($status == 1){
            return $this->success('Smart card validation was successful', data: $data );    
        }
        return $this->error( 'Smart card validation was not successful' ,data: $data, code: 500);   
    }


    /**
     * subscribe utility bill
    */
    public function buy_electricityold(Request $request)
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
    public function buy_cable_tvold(Request $request)
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
