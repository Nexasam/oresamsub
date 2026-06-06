<?php
namespace App\Http\Services\Api\v1\VendorUsersApi\Products;

use App\Http\Services\DataPlansService;
use App\Models\UserProductPlanAutomation;
use Exception;
use App\Models\User;
use App\Models\Network;
use App\Models\Product;
use App\Models\Setting;
use App\Models\UserPlan;
use App\Models\Automation;
use App\Models\CouponCode;
use App\Models\ProductPlan;
use App\Models\Transaction;
use App\Models\UsedUserCouponCode;
use App\Models\UserBulkDataWallet;
use Illuminate\Support\Facades\DB;
use App\Models\ProductPlanCategory;
use App\Services\Utils\UtilService;
use App\Traits\WalletTransactionLogs;
use App\Http\Services\CouponCodeService;
use App\Models\ProductPlanCustomPricing;
use App\Services\Automation\AutomationLogic;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubVendData;
use App\Services\Automation\MsOrgGroupAutomation\MsOrgGroupAutomation;

class ProductsService{
    use WalletTransactionLogs;

    public function parent_child_website_syncing($data){
       
        $email = $data['email'];
        $get_user_plan_id = User::select('user_plan_id')->where('email',$email)->first();
        $products = Product::get();
        $product_plan_categories = ProductPlanCategory::get();
        $product_plans = ProductPlan::get();
        $networks = Network::get();
        $userplans = UserPlan::get();
        $automations = Automation::get();

        $data['user_plan_id'] = $get_user_plan_id->user_plan_id;
        $data['products'] = $products;
        $data['product_plan_categories'] = $product_plan_categories;
        $data['product_plans'] = $product_plans;
        $data['networks'] = $networks;
        $data['userplans'] = $userplans;
        $data['automations'] = $automations;

        return [
            'status' => 1,
            'data' => $data,
            'message' => 'data syncing was successfully done'
        ];
          
    }
   
    public function fetch_product_plans($data){
        $network_id = $data['network_id'];
        $amount = $data['amount'];
        $plan_category_id = $data['plan_category_id'];
        $product_slug = $data['product_slug'];//this is required
        $user_id = $data['user_id'];//this is required
        
        $product_id = Product::where('slug',$product_slug)
        ->where('visibility',1)
        ->where('active_status',1)
        ->first()->id;
        // logger($plan_category_id);
         
        if($plan_category_id == ''){
            if($product_slug == 'airtime' || $product_slug == 'data'){
                $product_plan_categories = ProductPlanCategory::select('id','automation_id','product_plan_category_name')
                ->where('product_id',$product_id)
                ->where('network_id',$network_id)
                ->get();
            }else{
                $product_plan_categories = ProductPlanCategory::select('id','automation_id','product_plan_category_name')
                ->where('product_id',$product_id)
                ->get();
            }
            
        }else{
            if($product_slug == 'airtime' || $product_slug == 'data'){
                $product_plan_categories = ProductPlanCategory::select('id','automation_id','product_plan_category_name')
                ->where('product_id',$product_id)
                ->where('network_id',$network_id)
                ->where('id',$plan_category_id)
                ->get();
            }else{
                $product_plan_categories = ProductPlanCategory::select('id','automation_id','product_plan_category_name')
                ->where('product_id',$product_id)
                ->where('id',$plan_category_id)
                ->get();
            }        
        }

         
        $product_planss = [];
        $product_plans_master = [];
        $counter =0;

       //TODO: 
        $user_details = User::with('user_plan')->where('id',$user_id)->first();

        $user_plan_id = $user_details->user_plan_id;
        $user_id = $user_details->id;
        $user_level = UserPlan::select('plan_level')->where('id',$user_plan_id)->first();
        $plan_level = $user_level->plan_level;

        
        foreach($product_plan_categories as $key=>$product_plan_category){
            //get the automation id
            //get the product_category_id 

            if($product_slug == 'airtime'){
                $product_plans = ProductPlan::with(['automation','product_plan_category.network','product_plan_category.product'])
                ->where('product_plan_category_id',$product_plan_category->id)
                // ->where('automation_id',$product_plan_category->automation_id)
                ->where('visibility',1)
                ->limit(1)
                ->get();
            }else{
                $product_plans = ProductPlan::with(['automation','product_plan_category.network','product_plan_category.product'])
                ->where('product_plan_category_id',$product_plan_category->id)
                ->where('visibility',1)
                // ->where('automation_id',$product_plan_category->automation_id)
                // ->orderBy('data_size_in_mb')
                ->orderByRaw('CAST(data_size_in_mb AS UNSIGNED)')
                ->orderByRaw('CAST(validity_in_days AS UNSIGNED) DESC')
                ->get();
            }

            if(count($product_plans) > 0){
                foreach($product_plans as $product_plan){

                    //HERE SELLING PRICE CHANGES IF THEHRE IS A CUSTOM SETTING: put in a service later
                    $check_custom_setting = ProductPlanCustomPricing::where('product_plan_id','=', $product_plan->id)->where('user_id',$user_id)->first();
                    $amount = $check_custom_setting == NULL ? $amount : $check_custom_setting->price; 

                    $user_level_selling = "user_level_".$plan_level."_selling_price";
                    // $user_level_selling = "{user_level_$user_level_selling_price}";
                    $selling_price = $product_plan->$user_level_selling;
                    
                    $selling_price = $check_custom_setting == NULL ? $selling_price : $check_custom_setting->price; 


                    if( ( $product_slug == 'airtime' || $product_slug == 'utility_bills' ) && $amount != ''){
                          $purchase_discount = $product_plan->$user_level_selling;
                          $actual_discount_value = ceil(($purchase_discount/100) * $amount);  
                          $discounted_selling_price = $amount - abs($actual_discount_value);
                          $selling_price = 0; //this is from the system, not applicable for airtime
                    }else{
                        
                        $discounted_selling_price = $selling_price;
                    }


                    //new pricing flow
                    if($product_slug == 'data'){
                        $dat['product_id'] = $product_plan->product_plan_category->product->id;
                        $dat['user'] = $user_details;
                        $dat['plan_details'] = $product_plan;
                        $dat['network_id'] = $product_plan->product_plan_category->network->id;
                        $selling_price = (new DataPlansService())->get_customer_price_per_plan($dat)['message'];
                        $discounted_selling_price = $selling_price;
                    }
 
            
                    if($product_plan){
                        $counter++;                   
                        $product_planss['product_plan_id'] = $product_plan->id;
                        if($product_slug == 'airtime' || $product_slug == 'utility_bills'){
                            $product_planss['amount'] = $amount;
                        }
                        $product_planss['selling_price'] = $discounted_selling_price;
                        $product_planss['product_plan_name'] = $product_plan->product_plan_name;
                        $product_planss['data_size_in_mb'] = $product_plan->data_size_in_mb;
                        $product_planss['validity_in_days'] = $product_plan->validity_in_days;    
                        // $product_planss['automation_id'] = $product_plan->automation_id;  
                        $product_planss['product_plan_category_id'] = $product_plan_category->id;
                        $product_planss['product_plan_category_name'] = $product_plan_category->product_plan_category_name;
                        $product_plans_master[] = $product_planss;
                    }
                }
            }    
        }


        return [
            'status' => 1,
            'product_plans' => $product_plans_master,
            'plan_level' => $plan_level
        ];
          
    }

    public function fetch_product_plan_categories($data){
        $network = $data['network_id'];
        $product_slug = $data['product_slug'];//this is required

      
        try{
            $product_id = Product::where('slug',$product_slug)->where('visibility',1)->where('active_status',1)->first()->id;
           
            if($product_slug == 'airtime' || $product_slug == 'data'){
                $product_plans_categories = ProductPlanCategory::with('product')->where('network_id',$network)
                ->where('visibility',1)
                ->where('product_id',$product_id)
                ->orderByDesc('updated_at')
                ->get();
            }else{
                $product_plans_categories = ProductPlanCategory::with('product')->where('visibility',1)
                ->where('product_id',$product_id)
                ->orderByDesc('updated_at')
                ->get();
            }

            
        }catch(Exception $e){
            return [
                'status' => -1,
                'message' => $e->getMessage(),
                'product_plans_categories' => []
            ];
        }
        
        return [
            'status' => 1,
            'product_plans_categories' => $product_plans_categories
        ];
          
    }

    function generateTxnReference($prefix, $userUuid) {
        // Take a shortened, hashed version of UUID (to avoid super long refs)
        $userHash = substr(hash('sha1', $userUuid), 0, 8); 
        
        $timestamp = date('Ymd_His_u'); // precise timestamp with microseconds
        $random    = bin2hex(random_bytes(3)); // 6 random hex chars
        
        return "{$prefix}_{$timestamp}_{$userHash}_{$random}";
    }

    public function buy_data_service($data){
       
        $network_id = $data['network_id'];
        $product_id = $data['product_id'];
        $phone_number = $data['phone_number'];
        $product_plan_category_id = $data['product_plan_category_id'] ?? NULL;
        $product_plan_id = $data['product_plan_id'];
        $pin = $data['pin'];
        $wallet_category = $data['wallet_category'];
        $validatephonenetwork = $data['validatephonenetwork'];
        $user_id = $data['user_id'];//this is required
        $user = $data['user'];//this is required
        $coupon_code = $data['coupon_code'] ?? NULL;
        $txn_reference = $data['reference'] ?? NULL;
        $upline_commission = 0;

        if($txn_reference == NULL){
            //generate a unique one
            $txn_reference = $this->generateTxnReference('DATA',$user_id);
        }

        //safe check
        $checktxn = Transaction::where('txn_reference',$txn_reference)->first();
        if($checktxn){
            return ['status'=>'-1', 'message'=>'Duplicate reference found' ]; //data removed
        }


        if($user == NULL){
            $user_details = User::with('user_plan')->where('id',$user_id)->first();;
            if(! $user_details){
                //end session and redirect to login
                return ['status'=>-1, 'message'=>'User record not found' ]; //data removed
            }
        }else{
            $user_details = $user;
        }


        $user_plan = $user_details->user_plan ?? NULL;
        if($user_plan == NULL){
            //end session and redirect to login
            return ['status'=>-1, 'message'=>'User plan ID is null'];  //'data' => $data
        }

        // logger('eee'.$product_plan_id);
        // $plan_details = ProductPlan::with('automation')->where('id',$product_plan_id)->where('visibility',1)->first();
        $plan_details = ProductPlan::with(['automation','product_plan_category.network','product_plan_category.product'])
        ->where('id',$product_plan_id)
        ->where('visibility',1)
        ->first();
        if(! $plan_details){
            return ['status'=>-1, 'message'=>'Please try another plan. This is not available'];  //'data' => $data
        }
       


        $plan_detailsold =  $plan_details; //preserve original plan for pricing fetch.


        //TESSSSSSSTTTTTT
        //here we overrite the plan id with cheapest costprice: vendor
        // $network_plan_categories_arr = ProductPlanCategory::where('network_id',$plan_details->product_plan_category->network->id)
        // ->where('product_id', $plan_details->product_plan_category->product->id)
        // ->pluck('id')
        // ->toArray();
        $network_plan_categories_arr = ProductPlanCategory::where('network_id',$network_id)
        ->where('product_id', $product_id)
        ->pluck('id')
        ->toArray();
    
        //overwritten plan details
        $plan_details = ProductPlan::with([
            'automation',
            'product_plan_category.product',
            'product_plan_category.network'
        ])
        ->where('data_size_in_mb', $plan_details->data_size_in_mb)
        ->where('validity_in_days', $plan_details->validity_in_days)
        ->whereIn('product_plan_category_id', $network_plan_categories_arr)
        ->where('visibility', 1)
        ->orderByRaw('CAST(cost_price AS UNSIGNED) ASC') // ✅ Sort numerically
        ->first();
        $product_plan_id = $plan_details->id;
        //TESSSSSSSTTTTTT




        // $user_level = UserPlan::select('plan_level')->where('id',$user_plan->id)->first();
        // $plan_level = $user_plan->plan_level;
        // $user_plan_selling_price = 'user_level_'.$plan_level.'_selling_price';
        // $amount = abs($plan_details->$user_plan_selling_price);

       
        
        if(! $plan_details){
            //end session and redirect to login
            return ['status'=>'-1', 'message'=>'Invalid Plan ID or Currently Unavailable' ];
        }
       
        $automation_id = $plan_details->automation->id;
        $data_value_mb = $plan_details->data_size_in_mb ?? 0;
        $product_plan_category_id = $plan_details->product_plan_category->id; 
        
        //new pricing
        $dat['product_id'] = $plan_detailsold->product_plan_category->product->id;
        $dat['user'] = $user_details;
        $dat['plan_details'] = $plan_detailsold;
        $dat['network_id'] = $network_id;
        $get_selling_price = (new DataPlansService())->get_customer_price_per_plan($dat);
        $amount = $get_selling_price['message'];
        $upline_commission = $get_selling_price['upline_commission'];




        $success = 0;
        $failure = 0;
        $status = 0;
        $message = 'Pending';
        $display_results = [];

        $data1['days_count'] = [1,7,30];
        $data1['user_id'] = $user_id;
        $data1['product'] = 'data';
        $check_purchase_limit =  ProductsService::check_purchase_limit($data1);
        if($check_purchase_limit['status'] == -1){
            logger('Purchase limit reached for user ID: '.$user_id);
            $description = 'Purchase of data';
            $creationData['transaction_category'] = 'data';
            $creationData['transaction_route'] = 'api';
            $creationData['txn_reference'] = $txn_reference;
            $creationData['user_id'] = $user_id;
            $creationData['set_for_manual'] = $set_for_manual ?? 0;
            $creationData['wallet_category'] = $wallet_category;
            $creationData['product_plan_id'] = $product_plan_id;
            $creationData['phone_number'] = $phone_number ?? NULL;
            $creationData['amount'] = $amount;
            $creationData['coupon_code_id'] = $coupon ?? NULL;
            $creationData['discounted_amount'] = $amount;
            $creationData['status'] = -1;
            $creationData['balance_before'] =$user_details->main_wallet;
            $creationData['balance_after'] = $user_details->main_wallet;
            $creationData['description'] = $description ?? 'Purchase of data';
            $creationData['user_screen_message'] = $check_purchase_limit['message'];
            $creationData['admin_screen_message'] = $check_purchase_limit['message'];
            $creationData['upline_commission'] = 0;
            $transaction = Transaction::create($creationData);


            $walletLog['user_id'] = $user_id;
            $walletLog['transaction_category'] = 'DATA_FROM_MAIN_WALLET';
            $walletLog['balance_before'] = $user_details->main_wallet;
            $walletLog['balance_after'] = $user_details->main_wallet;
            $walletLog['transaction_id'] = $transaction->id;
            $walletLog['action_by'] = $user_id;           
            $walletLog['description'] = 'Data Purchase from main wallet';
            $this->log_wallet_transactions($walletLog);

       
            $status = -1;
            return [
                'id'=>$transaction->id,
                'txn_reference'=>$txn_reference ?? NULL,
                'status'=>-1,
                'actual_status' => -1,
                'status_code' => 503,
                'message' => $check_purchase_limit['message'] ?? 'Transaction failed',
                'apiresponse' => $check_purchase_limit['message'] ?? 'Transaction failed',
                'user_message' => $check_purchase_limit['message'] ?? 'Transaction failed',
                'admin_message' => $check_purchase_limit['message'] ?? 'Transaction failed',
                "balance_before" => $user_details->main_wallet ?? NULL,
                "balance_after" =>  $user_details->main_wallet ?? NULL,
                "plan" => $plan_details->api_id,
                "Status" => match($status) {
                    "1"   => "successful",
                    "2"  => "refunded",
                    "-1"  => "failed",
                    1   => "successful",
                    2  => "refunded",
                    -1  => "failed",
                    default => "failed"
                },
                "plan_network" => Network::where('id',$network_id)->value('network_name'),
                "plan_name" => $plan_details->product_plan_name ?? NULL,
                'plan_amount'=>$amount ?? NULL, 
                'create_date'=>date('Y-m-d H:i:s'), 
                'data' => $display_results ?? NULL
            ];
        }


        if($user_details->pin != $pin){
            //end session and redirect to login
            return ['status'=>'-1', 'message'=>'Incorrect PIN' ];
        }
    
        $user_id = $user_details->id;
        $phone_numbers = $phone_number;
        $phone_numbers = trim($phone_numbers);
        $phone_numbers_array = explode(',',$phone_numbers);
        $phone_numbers_count = count($phone_numbers_array);

        DB::beginTransaction();
        try{


                    //HERE SELLING PRICE CHANGES IF THEHRE IS A CUSTOM SETTING: put in a service later
                    // $check_custom_setting = ProductPlanCustomPricing::where('product_plan_id','=', $product_plan_id)->where('user_id',$user_id)->first();
                    // $amount = $check_custom_setting == NULL ? $amount : $check_custom_setting->price;  
                    
                    ////validate wallet
                        if($wallet_category == 'main_wallet'){
                            $wallet_before = $user_details->main_wallet;
                            $total_amount = $phone_numbers_count * $amount;
                            if($total_amount > $wallet_before || $wallet_before < 0){
                                // return ['status'=>'-1', 'message'=>'Insufficient wallet balance...' ];
                                $description = 'Purchase of data';
                                $creationData['transaction_category'] = 'data';
                                $creationData['transaction_route'] = 'api';
                                $creationData['txn_reference'] = $txn_reference;
                                $creationData['user_id'] = $user_id;
                                $creationData['set_for_manual'] = $set_for_manual ?? 0;
                                $creationData['wallet_category'] = $wallet_category;
                                $creationData['product_plan_id'] = $product_plan_id;
                                $creationData['phone_number'] = $phone_numbers_array[0];
                                $creationData['amount'] = $amount;
                                $creationData['coupon_code_id'] = $coupon ?? NULL;
                                $creationData['discounted_amount'] = $amount;
                                $creationData['status'] = -1;
                                $creationData['balance_before'] =$user_details->main_wallet;
                                $creationData['balance_after'] = $user_details->main_wallet;
                                $creationData['description'] = $description ?? 'Purchase of data';
                                $creationData['user_screen_message'] = 'Insufficient balance';
                                $creationData['admin_screen_message'] = 'Insufficient balance';
                                $creationData['upline_commission'] = 0;
                                $transaction = Transaction::create($creationData);


                                $walletLog['user_id'] = $user_id;
                                $walletLog['transaction_category'] = 'DATA_FROM_MAIN_WALLET';
                                $walletLog['balance_before'] = $user_details->main_wallet;
                                $walletLog['balance_after'] = $user_details->main_wallet;
                                $walletLog['transaction_id'] = $transaction->id;
                                $walletLog['action_by'] = $user_id;           
                                $walletLog['description'] = 'Data Purchase from main wallet';
                                $this->log_wallet_transactions($walletLog);

                                DB::commit();                                

                                $status = -1;
                                return [
                                    'id'=>$transaction->id,
                                    'txn_reference'=>$txn_reference ?? NULL,
                                    'status'=>-1,
                                    'actual_status' => -1,
                                    'status_code' => 503,
                                    'message' => 'Insufficient wallet balance',
                                    'apiresponse' => $user_message ?? 'Insufficient balance',
                                    'user_message' => $user_message ?? 'Insufficient balance',
                                    'admin_message' => $admin_message ?? 'Insufficient balance',
                                    "balance_before" => $user_details->main_wallet ?? NULL,
                                    "balance_after" =>  $user_details->main_wallet ?? NULL,
                                    "plan" => $plan_details->api_id,
                                    "Status" => match($status) {
                                        "1"   => "successful",
                                        "2"  => "refunded",
                                        "-1"  => "failed",
                                        1   => "successful",
                                        2  => "refunded",
                                        -1  => "failed",
                                        default => "failed"
                                    },
                                    "plan_network" => Network::where('id',$network_id)->value('network_name'),
                                    "plan_name" => $plan_details->product_plan_name ?? NULL,
                                    'plan_amount'=>$amount ?? NULL, 
                                    'create_date'=>date('Y-m-d H:i:s'), 
                                    'data' => $display_results ?? NULL
                                ];

                            }
                    
                            //calling the actual vending via the automation:
                            // $automation_details = Automation::where('id',$automation_id)->first();
                            $automation_details = $plan_details->automation;
                    
                            //TODO: candidate for separation:
                            for($i = 0; $i < count($phone_numbers_array); $i++ ){
                                

                                $datacoupon['product_plan_id'] = $product_plan_id;
                                $datacoupon['amount'] = $amount; //original amount
                                $datacoupon['user'] = $user_details;
                                $datacoupon['coupon_code'] = $coupon_code;
                                $get_deducted_amount = (new CouponCodeService())->get_coupon_information($datacoupon);
                                $amount_after_coupon = $get_deducted_amount['amount'];
                                $amount = $amount_after_coupon; //this is the new amount
                                $coupon = $get_deducted_amount['coupon'];
                                $remaining_slots = $get_deducted_amount['remaining_slots'];
                                $dataa['coupon'] = $coupon;


                                $dataa['phone_number'] = $phone_number;
                                $dataa['automation_details'] = $automation_details;
                                $dataa['network_id'] = $network_id;
                                $dataa['plan_id'] = $product_plan_id;
                                $dataa['validatephonenetwork'] = $validatephonenetwork;
                                logger('DATAAA SERVICE INPUT: '.json_encode($dataa));
                                $sell_data = AutomationLogic::initiateDataPurchase($dataa);
                                $set_for_manual = $sell_data['set_for_manual'] ?? 0;
                                $upline_commission = $sell_data['upline_commission'] ?? 0;

                                // logger('DATAAA SERVICE: '.json_encode($sell_data));
                                $coupon_count  = 0;
                
                                if($sell_data['status'] == 1){
                                    $coupon_count  = 1;
                                    $success++;
                                    $status = 1;
                                    $wallet_before = User::where('id',$user_id)->first()->main_wallet;
                                    $wallet_after = $wallet_before - $amount;
                                }else{
                                    //it might be processing or it failed
                                    $coupon_count  = 0;

                                    $status = -1;
                                    $failure++;
                                    $wallet_before = User::where('id',$user_id)->first()->main_wallet;
                                    $wallet_after = $wallet_before;
                                }

                                //simulate success
                                $user_message = $sell_data['user_message'];
                                $admin_message = $sell_data['admin_message'];
                                $display_results[$i] = array(
                                    'message' => $user_message,
                                    'admin_message' => $admin_message,
                                    'status' => $status
                                );
                               
                    
                    
                                //this should not run though because it has already been checked
                                if($wallet_after <= 0){
                                    $status = -1;
                                    $user_message = 'Failed due to insufficient balance...';
                                    $admin_message = 'Failed due to insufficient balance...';
                                    $failure++;
                                    $display_results[$i] = array(
                                        'message' => $user_message,
                                        'admin_message' => $admin_message,
                                        'status' => $status
                                    );
                                }
                        
                                //one code per time
                                 if($remaining_slots != NULL && $coupon != NULL){
                                    CouponCode::where('id',$coupon)->update([
                                        'slots_remaining' => $remaining_slots - $coupon_count
                                    ]);
                                    UsedUserCouponCode::create([
                                        'coupon_code_id' => $coupon,
                                        'user_id' => $user_id,
                                    ]);
                                }else{
                                    // logger('no coupon used here...');
                                }

                                $description = 'Purchase of data';
                                $creationData['transaction_category'] = 'data';
                                $creationData['user_id'] = $user_id;
                                $creationData['txn_reference'] = $txn_reference;
                                $creationData['retry_count'] = $retry_count ?? 0;
                                $creationData['set_for_manual'] = $set_for_manual ?? 0;
                                $creationData['wallet_category'] = $wallet_category;
                                $creationData['product_plan_id'] = $product_plan_id;
                                $creationData['phone_number'] = $phone_numbers_array[$i];
                                $creationData['amount'] = $amount;
                                $creationData['coupon_code_id'] = $coupon;
                                $creationData['discounted_amount'] = $amount;
                                $creationData['status'] = $status;
                                $creationData['balance_before'] = $wallet_before;
                                $creationData['balance_after'] = $wallet_after;
                                $creationData['description'] = $description;
                                $creationData['user_screen_message'] = $user_message;
                                $creationData['admin_screen_message'] = $admin_message;
                                $creationData['upline_commission'] = $upline_commission ?? 0;
                                $transaction = Transaction::create($creationData);


                                $walletLog['user_id'] = $user_id;
                                $walletLog['transaction_category'] = 'DATA_FROM_MAIN_WALLET';
                                $walletLog['balance_before'] = $wallet_before;
                                $walletLog['balance_after'] = $wallet_after;
                                $walletLog['transaction_id'] = $transaction->id;
                                $walletLog['action_by'] = $user_id;           
                                $walletLog['description'] = 'Data Purchase from main wallet';
                                $this->log_wallet_transactions($walletLog);
                                
                    
                                User::where('id',$user_id)->update([
                                    'main_wallet' => $wallet_after
                                ]);
                    
                            }

                            DB::commit();
                    
                            // if($failure > 0){
                            //     return [ 
                            //     'status'=>2, 
                            //     'user_message' => $user_message,
                            //     'admin_message' => $admin_message,
                            //     'message'=>" $failure issue(s) found. Check transaction history", 
                            //     'data' => $display_results
                            //   ];  

                            // }


                            return [
                                'id'=>$transaction->id,
                                'txn_reference'=>$txn_reference,
                                'status'=>$status,
                                'actual_status' => $status,
                                'message' => $user_message,
                                'apiresponse' => $user_message,
                                'user_message' => $user_message,
                                'admin_message' => $admin_message,
                                "balance_before" => $wallet_before,
                                "balance_after" => $wallet_after,
                                "plan" => $plan_details->api_id,
                                "Status" => match($status) {
                                    "1"   => "successful",
                                    "2"  => "refunded",
                                    "-1"  => "failed",
                                    1   => "successful",
                                    2  => "refunded",
                                    -1  => "failed",
                                    default => "unknown"
                                },
                                "plan_network" => Network::where('id',$network_id)->value('network_name'),
                                "plan_name" => $plan_details->product_plan_name,
                                'plan_amount'=>$amount, 
                                'create_date'=>date('Y-m-d H:i:s'), 
                                'data' => $display_results
                            ];
                    
                        } 


                        if($wallet_category == 'data_wallet'){
                            return [
                                'status' => -1,
                                'message' => 'not available at the moment'
                            ];
                            $get_bulk_data_wallet_details = UserBulkDataWallet::where('user_id',$user_id)->where('product_plan_category_id',$product_plan_category_id)->first();
                            
                            if(! $get_bulk_data_wallet_details ){
                                $bulk_wallet_balance_before = 0;
                            }
                            $bulk_wallet_balance_before = $get_bulk_data_wallet_details->bulk_wallet_balance_mb;

                            $total_value_to_buy_in_mb = $phone_numbers_count * $data_value_mb;
                            if($total_value_to_buy_in_mb > $bulk_wallet_balance_before){
                                return ['status'=>'-1', 'message'=>'Insufficient data in wallet balance' ];
                            }
                    
                            //calling the actual vending via the automation:
                            // $automation_details = Automation::where('id',$automation_id)->first();
                    
                            //TODO: candidate for separation
                            for($i = 0; $i < count($phone_numbers_array); $i++ ){
                            
                                //vend data
                                //HERE the endpoint of the automation service is called
                                if($automation_details->slug == 'megasubplug'){
                                    $sell_data = (new MegaSubVendData($phone_numbers_array[$i],$product_plan_id,$validatephonenetwork))->buyData();
                                } 
                                else if($automation_details->automation_group == 'msorg'){
                                    $data_msorg['automation_id'] = $automation_details->id;
                                    $data_msorg['network_id'] = $network_id;
                                    $data_msorg['plan_id'] = $product_plan_id;
                                    $data_msorg['mobile_number'] = $phone_numbers_array[$i];
                                    $data_msorg['token'] = $automation_details->api_public_key;
                                    $data_msorg['url'] = $automation_details->data_url;
                                    $sell_data = (new MsOrgGroupAutomation($data_msorg))->buyData();
                                 
                                }
                                // else if($automation_details->slug == 'ogdams' || $automation_details->slug == 'ogdamsv2'){
                                //     $sell_data = (new OgdamsVendData($phone_numbers_array[$i],$product_plan_id))->buyData();
                                // }
                                else{
                                    //this will be like this until other automations are processed
                                    $sell_data['status'] = -1;
                                    $sell_data['user_message'] = 'Bulk data processing failed.';
                                    $sell_data['admin_message'] = 'Bulk data processing failed.';
                                }

                                if($sell_data['status'] == 1){
                                    $success++;
                                    $status = 1;
                                    $get_bulk_data_wallet_details = UserBulkDataWallet::where('user_id',$user_id)->where('product_plan_category_id',$product_plan_category_id)->first();
                                    $bulk_wallet_balance_before = $get_bulk_data_wallet_details->bulk_wallet_balance_mb;
                                    $bulk_wallet_balance_after = $bulk_wallet_balance_before - $data_value_mb; 
                                }else{
                                    //it might be processing or it failed
                                    $status = -1;
                                    $failure++;
                                    $get_bulk_data_wallet_details = UserBulkDataWallet::where('user_id',$user_id)->where('product_plan_category_id',$product_plan_category_id)->first();
                                    $bulk_wallet_balance_before = $get_bulk_data_wallet_details->bulk_wallet_balance_mb;
                                    $bulk_wallet_balance_after = $bulk_wallet_balance_before;
                                }
                                //simulate success

                                $user_message = $sell_data['user_message'];
                                $admin_message = $sell_data['admin_message'];
                                $display_results[$i] = array(
                                    'message' => $user_message,
                                    'admin_message' => $admin_message,
                                    'status' => $status
                                );


                                if($bulk_wallet_balance_after <= 0){
                                    $status = -1;
                                    $message = 'Failed due to insufficient balance via bulk data wallet';
                                    $failure++;
                                    $display_results[$i] = array(
                                        'message' => $user_message,
                                        'admin_message' => $admin_message,
                                        'status' => $status
                                    );
                                }

                                UserBulkDataWallet::where('user_id',$user_id)
                                ->where('product_plan_category_id',$product_plan_category_id)
                                ->update([
                                    'bulk_wallet_balance_mb' => $bulk_wallet_balance_after
                                ]);
                        
                                $description = 'Purchase of data via data wallet';
                                $creationData['transaction_category'] = 'data';
                                $creationData['txn_reference'] = $txn_reference;
                                $creationData['user_id'] = $user_id;
                                $creationData['wallet_category'] = $wallet_category;
                                $creationData['product_plan_id'] = $product_plan_id;
                                $creationData['phone_number'] = $phone_numbers_array[$i];
                                $creationData['amount'] = $amount;
                                $creationData['status'] = $status;
                                $creationData['balance_before'] = $bulk_wallet_balance_before;
                                $creationData['balance_after'] = $bulk_wallet_balance_after;
                                $creationData['description'] = $description;
                                $creationData['user_screen_message'] = $user_message;
                                $creationData['admin_screen_message'] = $admin_message;
                                $transaction = Transaction::create($creationData); 

                                $walletLog['user_id'] = $user_id;
                                $walletLog['transaction_category'] = 'DATA_FROM_DATA_WALLET';
                                $walletLog['balance_before'] = $bulk_wallet_balance_before;
                                $walletLog['balance_after'] = $bulk_wallet_balance_after;
                                $walletLog['transaction_id'] = $transaction->id;
                                $walletLog['action_by'] = $user_id;
                                $walletLog['description'] = 'Data Purchase from data wallet';
                                $this->log_wallet_transactions($walletLog);
                                          
                            }
    
                            DB::commit();
                            if($failure > 0){
                                return ['status'=>2, 'message'=>" $failure issue(s) found. Check transaction history", 'data' => $display_results  ];   
                            }
                            // return ['status'=>1, 'message'=>'Bulk data transaction was successfully processed', 'data' => $display_results  ];
                        }


        }catch(Exception $exception){
            DB::rollBack();
            logger($exception->getMessage().' on line: '. $exception->getLine().' in page: '. $exception->getFile());

            $description = 'Purchase of data';
            $creationData['transaction_category'] = 'data';
            $creationData['transaction_route'] = 'api';
            $creationData['txn_reference'] = $txn_reference;
            $creationData['user_id'] = $user_id;
            $creationData['set_for_manual'] = $set_for_manual ?? 0;
            $creationData['wallet_category'] = $wallet_category;
            $creationData['product_plan_id'] = $product_plan_id;
            $creationData['phone_number'] = $phone_number ?? NULL;
            $creationData['amount'] = $amount;
            $creationData['coupon_code_id'] = $coupon ?? NULL;
            $creationData['discounted_amount'] = $amount;
            $creationData['status'] = -1;
            $creationData['balance_before'] =$user_details->main_wallet;
            $creationData['balance_after'] = $user_details->main_wallet;
            $creationData['description'] = $description ?? 'Purchase of data';
            $creationData['user_screen_message'] = $check_purchase_limit['message'];
            $creationData['admin_screen_message'] = $check_purchase_limit['message'];
            $transaction = Transaction::create($creationData);


            $walletLog['user_id'] = $user_id;
            $walletLog['transaction_category'] = 'DATA_FROM_MAIN_WALLET';
            $walletLog['balance_before'] = $user_details->main_wallet;
            $walletLog['balance_after'] = $user_details->main_wallet;
            $walletLog['transaction_id'] = $transaction->id;
            $walletLog['action_by'] = $user_id;           
            $walletLog['description'] = 'Data Purchase from main wallet';
            $this->log_wallet_transactions($walletLog);


            return [
                'id'=>$transaction->id,
                'txn_reference'=>$txn_reference ?? NULL,
                'status'=>-1,
                'actual_status' => -1,
                'status_code' => 500,
                'message' => $user_message ?? 'Transaction failed',
                'apiresponse' => $user_message ?? 'Transaction failed',
                'user_message' => $user_message ?? 'Transaction failed',
                'admin_message' => $admin_message ?? 'Transaction failed',
                "balance_before" => $user_details->main_wallet ?? NULL,
                "balance_after" =>  $user_details->main_wallet ?? NULL,
                "plan" => $plan_details->api_id,
                "Status" => match($status) {
                    "1"   => "successful",
                    "2"  => "refunded",
                    "-1"  => "failed",
                    1   => "successful",
                    2  => "refunded",
                    -1  => "failed",
                    default => "failed"
                },
                "plan_network" => Network::where('id',$network_id)->value('network_name'),
                "plan_name" => $plan_details->product_plan_name ?? NULL,
                'plan_amount'=>$amount ?? NULL, 
                'create_date'=>date('Y-m-d H:i:s'), 
                'data' => $display_results ?? NULL
            ];

            // return ['status'=>'-1', 'message'=>'Something went wrong... Please try again', 'data'=>[]];
        }

    }


    //this will be updated for other product in the future
    public function buy_data_service_one_api($data){
    //    return $data;
        $network_id = $data['network_id'];
        $product_id = $data['product_id'];
        $phone_number = $data['phone_number'];
        $product_plan_category_id = $data['product_plan_category_id'] ?? NULL;
        $product_plan_id = $data['product_plan_id'];
        $pin = $data['pin'];
        $wallet_category = $data['wallet_category'];
        $validatephonenetwork = $data['validatephonenetwork'];
        $user_id = $data['user_id'];//this is required
        $user = $data['user'];//this is required
        $coupon_code = $data['coupon_code'] ?? NULL;
        $txn_reference = $data['reference'] ?? NULL;
        $upline_commission = 0;

        //WHEN WE ARE READY TO ADD AUTO SWITCHING BASED ON CHEAPEST PRICE,WE CAN MAKE THIS FETCH ALL
        $user_unique_plan_automation = UserProductPlanAutomation::with('userAutomation.automation')
        ->where('user_id', $user_id)
        ->where('product_plan_id', $product_plan_id)
        ->where('status', 1) // ✅ THIS is the correct place
        ->first();
        $service_charge = $user_unique_plan_automation->userAutomation->pricing_amount ?? 10; //this is the price set by the merchant for this plan. it can be null if they want to use the default price
        


        if($txn_reference == NULL){
          
            //generate a unique one
            $txn_reference = $this->generateTxnReference('DATA',$user_id);
        }

        $checktxn = Transaction::where('txn_reference',$txn_reference)->first();
        if($checktxn){
           $txn_reference = $this->generateTxnReference('DATA',$user_id);
        }

        //INITIAL CREATION
        $creationData = [
            'transaction_category'   => 'data',
            'transaction_route'      => 'api',
            'user_id'                => $user_id,
            'txn_reference'          => $txn_reference,
            'retry_count'            => $retry_count ?? 0,
            'set_for_manual'         => 0,
            'wallet_category'        => $wallet_category,
            'product_plan_id'        => $product_plan_id,
            'phone_number'           => $phone_number,
            'amount'                 => 0,
            'service_charge'         => $service_charge ?? 10,
            'coupon_code_id'         => null,
            'user_product_plan_automation_id'   => $user_unique_plan_automation->id,
            'discounted_amount'      => 0,
            'status'                 => 0, // 🔥 PENDING
            'balance_before'         => 0,
            'balance_after'          => 0,
            'description'            => 'Purchase of data',
            'user_screen_message'    => 'Transaction processing...',
            'admin_screen_message'   => 'Transaction initialized',
            'upline_commission'      => 0,
        ];
        
        $transaction = Transaction::create($creationData);

        if($user == NULL){
            $user_details = User::with('user_plan')->where('id',$user_id)->first();
            if(! $user_details){
                //add to db
                //end session and redirect to login
                $transaction->update([
                    'status'               => -1,
                    'balance_before'        => null,
                    'balance_after'        => null,
                    'user_screen_message'  => "User not found",
                    'admin_screen_message' => 'User not found',
                ]);

                return $this->res([
                    'id'             => $transaction->id ?? null,
                    'txn_reference'  => $txn_reference,
                    'status'         => -1,
                    'status_code'    => 404,
                
                    'message'        => "User not found",
                    'user_message'   => "User not found",
                    'admin_message'  => "User not found",
                
                    'balance_before' =>  null,
                    'balance_after'  =>  null,
                
                    'plan'           => $plan_details->api_id ?? null,
                    'plan_network'   => Network::where('id',$network_id)->value('network_name'),
                    'plan_name'      => $plan_details->product_plan_name ?? null,
                    'plan_amount'    => $amount ?? null,
                
                    'data'           => $display_results ?? [],
                ]);
            }
        }else{
            $user_details = $user;
        }


        //safe check
        // $checktxn = Transaction::where('txn_reference',$txn_reference)->first();
        // if($checktxn){
        //     // return ['status'=>'-1', 'message'=>'Duplicate reference found' ]; //data removed
        //     // add to db
        //     $transaction->update([
        //         'status'               => -1,
        //         'balance_before'        => $user_details->main_wallet,
        //         'balance_after'        => $user_details->main_wallet,
        //         'user_screen_message'  => "Duplicate reference found",
        //         'admin_screen_message' => 'Duplicate reference found',
        //     ]);
        //     return $this->res([
        //         'id'             => $transaction->id ?? null,
        //         'txn_reference'  => $txn_reference,
        //         'status'         => -1,
        //         'status_code'    => 404,
            
        //         'message'        => "Duplicate reference found",
        //         'user_message'   => "Duplicate reference found",
        //         'admin_message'  => "Duplicate reference found",
            
        //         'balance_before' => $user_details->main_wallet ?? null,
        //         'balance_after'  => $user_details->main_wallet ?? null,
            
        //         'plan'           => $plan_details->api_id ?? null,
        //         'plan_network'   => Network::where('id',$network_id)->value('network_name'),
        //         'plan_name'      => $plan_details->product_plan_name ?? null,
        //         'plan_amount'    => $amount ?? null,
            
        //         'data'           => $display_results ?? [],
        //     ]);
        // }


        // $user_plan = $user_details->user_plan ?? NULL;
        // if($user_plan == NULL){
        //     //end session and redirect to login
        //     return ['status'=>-1, 'message'=>'User plan ID is null'];  //'data' => $data
        // }

        // logger('eee'.$product_plan_id);
        // $plan_details = ProductPlan::with('automation')->where('id',$product_plan_id)->where('visibility',1)->first();
        $plan_details = ProductPlan::with(['automation','product_plan_category.network','product_plan_category.product'])
        ->where('id',$product_plan_id)
        ->first();
        if(! $plan_details){
            // return ['status'=>-1, 'message'=>'Please try another plan. This is not available'];  //'data' => $data
            $transaction->update([
                'status'               => -1,
                'balance_before'        => $user_details->main_wallet,
                'balance_after'        => $user_details->main_wallet,
                'user_screen_message'  => "Please try another plan. This is not available",
                'admin_screen_message' => 'Please try another plan. This is not available',
            ]);

            return $this->res([
                'id'             => $transaction->id ?? null,
                'txn_reference'  => $txn_reference,
                'status'         => -1,
                'status_code'    => 404,
            
                'message'        => "Please try another plan. This is not available",
                'user_message'   => "Please try another plan. This is not available",
                'admin_message'  => "Please try another plan. This is not available",
            
                'balance_before' => $user_details->main_wallet ?? null,
                'balance_after'  => $user_details->main_wallet ?? null,
            
                'plan'           => $plan_details->api_id ?? null,
                'plan_network'   => Network::where('id',$network_id)->value('network_name'),
                'plan_name'      => $plan_details->product_plan_name ?? null,
                'plan_amount'    => $amount ?? null,
            
                'data'           => $display_results ?? [],
            ]);
        }
        $plan_detailsold =  $plan_details; //preserve original plan for pricing fetch.


        //now lets see if that merchant has automations configure for that plan
        // $user_unique_plan_automation = UserProductPlanAutomation::with('userAutomation')->where('user_id',$user_id)
        // ->where('product_plan_id',$product_plan_id)
        // ->first();



        
        //
        if(! $user_unique_plan_automation || ! $user_unique_plan_automation->userAutomation || $user_unique_plan_automation->status != 1){
            // return ['status'=>-1, 'message'=>'No automation configured for this plan. Please try another plan.'];  //'data' => $data
           //    add to db: very important
           $transaction->update([
                'status'               => -1,
                'balance_before'        => $user_details->main_wallet,
                'balance_after'        => $user_details->main_wallet,
                'user_screen_message'  => "No provider configured for this plan. Please try another plan.",
                'admin_screen_message' => 'No provider configured for this plan. Please try another plan.'
            ]);

            return $this->res([
                'id'             => $transaction->id ?? null,
                'txn_reference'  => $txn_reference,
                'status'         => -1,
                'status_code'    => 404,
            
                'message'        => "Something went wrong. Please contact support",
                'user_message'   => "No provider configured for this plan. Please try another plan.",
                'admin_message'  => "No provider configured for this plan. Please try another plan.",
            
                'balance_before' => $user_details->main_wallet ?? null,
                'balance_after'  => $user_details->main_wallet ?? null,
            
                'plan'           => $plan_details->api_id ?? null,
                'plan_network'   => Network::where('id',$network_id)->value('network_name'),
                'plan_name'      => $plan_details->product_plan_name ?? null,
                'plan_amount'    => $amount ?? null,
            
                'data'           => $display_results ?? [],
            ]);
            
        }


        // $user_automation = $user_unique_plan_automation->userAutomation;
        // ->first();
        // return ['status'=>-1, 'message'=>'user plans: ','data'=>$user_unique_plan_automation];  //'data' => $data
        // return ['status'=>-1, 'message'=>'user plans: ','data'=>$service_charge];  //'data' => $data

        $success = 0;
        $failure = 0;
        $status = 0;
        $message = 'Pending';
        $display_results = [];
    
        $user_id = $user_details->id;
        $phone_numbers = $phone_number;
        $phone_numbers = trim($phone_numbers);
        $phone_numbers_array = explode(',',$phone_numbers);
        $phone_numbers_count = count($phone_numbers_array);

        DB::beginTransaction();
        try{


                    //HERE SELLING PRICE CHANGES IF THEHRE IS A CUSTOM SETTING: put in a service later
                    // $check_custom_setting = ProductPlanCustomPricing::where('product_plan_id','=', $product_plan_id)->where('user_id',$user_id)->first();
                    // $amount = $check_custom_setting == NULL ? $amount : $check_custom_setting->price;  
                    
                    ////validate wallet
                        if($wallet_category == 'main_wallet'){
                            $wallet_before = $user_details->main_wallet;
                            $amount = 0;
                            $service_charge = $service_charge ?? 9; //this is the amount set by the merchant for this plan. it can be null if they want to use the default price
                            $total_amount = $phone_numbers_count * $service_charge; //service charge
                            if($total_amount > $wallet_before || $wallet_before < 0){
                                // return ['status'=>'-1', 'message'=>'Insufficient wallet balance...' ];
                                // $description = 'Purchase of data';
                                // $creationData['transaction_category'] = 'data';
                                // $creationData['transaction_route'] = 'api';
                                // $creationData['txn_reference'] = $txn_reference;
                                // $creationData['user_id'] = $user_id;
                                // $creationData['set_for_manual'] = $set_for_manual ?? 0;
                                // $creationData['wallet_category'] = $wallet_category;
                                // $creationData['product_plan_id'] = $product_plan_id;
                                // $creationData['phone_number'] = $phone_numbers_array[0];
                                // $creationData['amount'] = $amount;
                                // $creationData['service_charge'] = $service_charge;
                                // $creationData['coupon_code_id'] = $coupon ?? NULL;
                                // $creationData['discounted_amount'] = $amount;
                                // $creationData['status'] = -1;
                                // $creationData['balance_before'] =$user_details->main_wallet;
                                // $creationData['balance_after'] = $user_details->main_wallet;
                                // $creationData['description'] = $description ?? 'Purchase of data';
                                // $creationData['user_screen_message'] = 'Insufficient balance';
                                // $creationData['admin_screen_message'] = 'Insufficient balance';
                                // $creationData['upline_commission'] = 0;
                                // $transaction = Transaction::create($creationData);

                                $transaction->update([
                                    'status'               => -1,
                                    'balance_before'        => $user_details->main_wallet,
                                    'balance_after'        => $user_details->main_wallet,
                                    'user_screen_message'  => "Insufficient wallet balance",
                                    'admin_screen_message' => 'Insufficient wallet balance'
                                ]);


                                $walletLog['user_id'] = $user_id;
                                $walletLog['transaction_category'] = 'DATA_FROM_MAIN_WALLET';
                                $walletLog['balance_before'] = $user_details->main_wallet;
                                $walletLog['balance_after'] = $user_details->main_wallet;
                                $walletLog['transaction_id'] = $transaction->id;
                                $walletLog['action_by'] = $user_id;           
                                $walletLog['description'] = 'Data Purchase from main wallet';
                                $this->log_wallet_transactions($walletLog);

                                DB::commit();                                

                                // $status = -1;
                                return $this->res([
                                    'id'             => $transaction->id ?? null,
                                    'txn_reference'  => $txn_reference,
                                    'status'         => -1,
                                    'status_code'    => 404,
                                
                                    'message'        => "Insufficient wallet balance",
                                    'user_message'   => "Insufficient wallet balance",
                                    'admin_message'  => "Insufficient wallet balance",
                                
                                    'balance_before' => $user_details->main_wallet ?? null,
                                    'balance_after'  => $user_details->main_wallet ?? null,
                                
                                    'plan'           => $plan_details->api_id ?? null,
                                    'plan_network'   => Network::where('id',$network_id)->value('network_name'),
                                    'plan_name'      => $plan_details->product_plan_name ?? null,
                                    'plan_amount'    => $amount ?? null,
                                
                                    'data'           => $display_results ?? [],
                                ]);

                            }
                    
                            //TODO: candidate for separation:
                            for($i = 0; $i < count($phone_numbers_array); $i++ ){
                                
                                $dataa['coupon'] = $coupon ?? 0;
                                $dataa['phone_number'] = $phone_number;
                                $dataa['automation_details'] = $user_unique_plan_automation;//this is now from the user plan automation relation
                                $dataa['network_id'] = $network_id;
                                $dataa['plan_id'] = $product_plan_id;
                                $dataa['validatephonenetwork'] = $validatephonenetwork;
                                $sell_data = AutomationLogic::initiateDataPurchase($dataa);
                                $set_for_manual = $sell_data['set_for_manual'] ?? 0;
                                $upline_commission = $sell_data['upline_commission'] ?? 0;

                                // logger('DATAAA SERVICE: '.json_encode($sell_data));
                                $coupon_count  = 0;
                
                                if($sell_data['status'] == 1){
                                    $coupon_count  = 1;
                                    $success++;
                                    $status = 1;
                                    $wallet_before = User::where('id',$user_id)->first()->main_wallet;
                                    $wallet_after = $wallet_before - $service_charge;
                                }else{
                                    //it might be processing or it failed
                                    $coupon_count  = 0;

                                    $status = -1;
                                    $failure++;
                                    $wallet_before = User::where('id',$user_id)->first()->main_wallet;
                                    $wallet_after = $wallet_before;
                                }

                                //simulate success
                                $user_message = $sell_data['user_message'];
                                $admin_message = $sell_data['admin_message'];
                                $display_results[$i] = array(
                                    'message' => $user_message,
                                    'admin_message' => $admin_message,
                                    'status' => $status
                                );
                               
                    
                    
                                //this should not run though because it has already been checked
                                if($wallet_after <= 0){
                                    $status = -1;
                                    $user_message = 'Failed due to insufficient balance...';
                                    $admin_message = 'Failed due to insufficient balance...';
                                    $failure++;
                                    $display_results[$i] = array(
                                        'message' => $user_message,
                                        'admin_message' => $admin_message,
                                        'status' => $status
                                    );
                                }
                        
                                //one code per time
                                //  if($remaining_slots != NULL && $coupon != NULL){
                                //     CouponCode::where('id',$coupon)->update([
                                //         'slots_remaining' => $remaining_slots - $coupon_count
                                //     ]);
                                //     UsedUserCouponCode::create([
                                //         'coupon_code_id' => $coupon,
                                //         'user_id' => $user_id,
                                //     ]);
                                // }else{
                                //     // logger('no coupon used here...');
                                // }

                                // $description = 'Purchase of data';
                                // $creationData['transaction_category'] = 'data';
                                // $creationData['transaction_route'] = 'api';
                                // $creationData['user_id'] = $user_id;
                                // $creationData['txn_reference'] = $txn_reference;
                                // $creationData['retry_count'] = $retry_count ?? 0;
                                // $creationData['set_for_manual'] = $set_for_manual ?? 0;
                                // $creationData['wallet_category'] = $wallet_category;
                                // $creationData['product_plan_id'] = $product_plan_id;
                                // $creationData['phone_number'] = $phone_numbers_array[$i];
                                // $creationData['amount'] = 0;
                                // $creationData['service_charge'] = $service_charge ?? 10;
                                // $creationData['coupon_code_id'] = null;
                                // $creationData['discounted_amount'] = $amount;
                                // $creationData['status'] = $status;
                                // $creationData['balance_before'] = $wallet_before;
                                // $creationData['balance_after'] = $wallet_after;
                                // $creationData['description'] = $description;
                                // $creationData['user_screen_message'] = $user_message;
                                // $creationData['admin_screen_message'] = $admin_message;
                                // $creationData['upline_commission'] = $upline_commission ?? 0;
                                // $transaction = Transaction::create($creationData);


                                $transaction->update([
                                    'status'               => $status,
                                    'balance_before'        => $user_details->main_wallet,
                                    'balance_after'        => $user_details->main_wallet - $service_charge,
                                    'user_screen_message'  =>$user_message,
                                    'admin_screen_message' => $admin_message,
                                    'phone_number' => $phone_numbers_array[$i]
                                ]);

                                $walletLog['user_id'] = $user_id;
                                $walletLog['transaction_category'] = 'DATA_FROM_MAIN_WALLET';
                                $walletLog['balance_before'] = $wallet_before;
                                $walletLog['balance_after'] = $wallet_after;
                                $walletLog['transaction_id'] = $transaction->id;
                                $walletLog['action_by'] = $user_id;           
                                $walletLog['description'] = 'Data Purchase from main wallet';
                                $this->log_wallet_transactions($walletLog);
                                
                    
                                User::where('id',$user_id)->update([
                                    'main_wallet' => $wallet_after
                                ]);
                    
                            }

                            DB::commit();
                    
                            // if($failure > 0){
                            //     return [ 
                            //     'status'=>2, 
                            //     'user_message' => $user_message,
                            //     'admin_message' => $admin_message,
                            //     'message'=>" $failure issue(s) found. Check transaction history", 
                            //     'data' => $display_results
                            //   ];  

                            // }


                            // return [
                            //     'id'=>$transaction->id,
                            //     'txn_reference'=>$txn_reference,
                            //     'status'=>$status,
                            //     'actual_status' => $status,
                            //     'message' => $user_message,
                            //     'apiresponse' => $user_message,
                            //     'user_message' => $user_message,
                            //     'admin_message' => $admin_message,
                            //     "balance_before" => $wallet_before,
                            //     "balance_after" => $wallet_after,
                            //     "plan" => $plan_details->api_id,
                            //     "Status" => match($status) {
                            //         "1"   => "successful",
                            //         "2"  => "refunded",
                            //         "-1"  => "failed",
                            //         1   => "successful",
                            //         2  => "refunded",
                            //         -1  => "failed",
                            //         default => "unknown"
                            //     },
                            //     "plan_network" => Network::where('id',$network_id)->value('network_name'),
                            //     "plan_name" => $plan_details->product_plan_name,
                            //     'plan_amount'=>$amount, 
                            //     'create_date'=>date('Y-m-d H:i:s'), 
                            //     'data' => $display_results
                            // ];

                            return $this->res([
                                'id'             => $transaction->id ?? null,
                                'txn_reference'  => $txn_reference,
                                'status'         => $status,
                                'status_code'    => 200, //confirm
                            
                                'message'        => $user_message,
                                'user_message'   => $user_message,
                                'admin_message'  => $admin_message,
                            
                                'balance_before' => $user_details->main_wallet ?? null,
                                'balance_after'  => $user_details->wallet_after ?? null,
                            
                                'plan'           => $plan_details->api_id ?? null,
                                'plan_network'   => Network::where('id',$network_id)->value('network_name'),
                                'plan_name'      => $plan_details->product_plan_name ?? null,
                                'plan_amount'    => $amount ?? null,
                            
                                'data'           => $display_results ?? [],
                            ]);
                    
                        } 


                        if($wallet_category == 'data_wallet'){
                            return [
                                'status' => -1,
                                'message' => 'not available at the moment'
                            ];
                            $get_bulk_data_wallet_details = UserBulkDataWallet::where('user_id',$user_id)->where('product_plan_category_id',$product_plan_category_id)->first();
                            
                            if(! $get_bulk_data_wallet_details ){
                                $bulk_wallet_balance_before = 0;
                            }
                            $bulk_wallet_balance_before = $get_bulk_data_wallet_details->bulk_wallet_balance_mb;

                            $total_value_to_buy_in_mb = $phone_numbers_count * $data_value_mb;
                            if($total_value_to_buy_in_mb > $bulk_wallet_balance_before){
                                return ['status'=>'-1', 'message'=>'Insufficient data in wallet balance' ];
                            }
                    
                            //calling the actual vending via the automation:
                            // $automation_details = Automation::where('id',$automation_id)->first();
                    
                            //TODO: candidate for separation
                            for($i = 0; $i < count($phone_numbers_array); $i++ ){
                            
                                //vend data
                                //HERE the endpoint of the automation service is called
                                if($automation_details->slug == 'megasubplug'){
                                    $sell_data = (new MegaSubVendData($phone_numbers_array[$i],$product_plan_id,$validatephonenetwork))->buyData();
                                } 
                                else if($automation_details->automation_group == 'msorg'){
                                    $data_msorg['automation_id'] = $automation_details->id;
                                    $data_msorg['network_id'] = $network_id;
                                    $data_msorg['plan_id'] = $product_plan_id;
                                    $data_msorg['mobile_number'] = $phone_numbers_array[$i];
                                    $data_msorg['token'] = $automation_details->api_public_key;
                                    $data_msorg['url'] = $automation_details->data_url;
                                    $sell_data = (new MsOrgGroupAutomation($data_msorg))->buyData();
                                 
                                }
                                // else if($automation_details->slug == 'ogdams' || $automation_details->slug == 'ogdamsv2'){
                                //     $sell_data = (new OgdamsVendData($phone_numbers_array[$i],$product_plan_id))->buyData();
                                // }
                                else{
                                    //this will be like this until other automations are processed
                                    $sell_data['status'] = -1;
                                    $sell_data['user_message'] = 'Bulk data processing failed.';
                                    $sell_data['admin_message'] = 'Bulk data processing failed.';
                                }

                                if($sell_data['status'] == 1){
                                    $success++;
                                    $status = 1;
                                    $get_bulk_data_wallet_details = UserBulkDataWallet::where('user_id',$user_id)->where('product_plan_category_id',$product_plan_category_id)->first();
                                    $bulk_wallet_balance_before = $get_bulk_data_wallet_details->bulk_wallet_balance_mb;
                                    $bulk_wallet_balance_after = $bulk_wallet_balance_before - $data_value_mb; 
                                }else{
                                    //it might be processing or it failed
                                    $status = -1;
                                    $failure++;
                                    $get_bulk_data_wallet_details = UserBulkDataWallet::where('user_id',$user_id)->where('product_plan_category_id',$product_plan_category_id)->first();
                                    $bulk_wallet_balance_before = $get_bulk_data_wallet_details->bulk_wallet_balance_mb;
                                    $bulk_wallet_balance_after = $bulk_wallet_balance_before;
                                }
                                //simulate success

                                $user_message = $sell_data['user_message'];
                                $admin_message = $sell_data['admin_message'];
                                $display_results[$i] = array(
                                    'message' => $user_message,
                                    'admin_message' => $admin_message,
                                    'status' => $status
                                );


                                if($bulk_wallet_balance_after <= 0){
                                    $status = -1;
                                    $message = 'Failed due to insufficient balance via bulk data wallet';
                                    $failure++;
                                    $display_results[$i] = array(
                                        'message' => $user_message,
                                        'admin_message' => $admin_message,
                                        'status' => $status
                                    );
                                }

                                UserBulkDataWallet::where('user_id',$user_id)
                                ->where('product_plan_category_id',$product_plan_category_id)
                                ->update([
                                    'bulk_wallet_balance_mb' => $bulk_wallet_balance_after
                                ]);
                        
                                $description = 'Purchase of data via data wallet';
                                $creationData['transaction_category'] = 'data';
                                $creationData['txn_reference'] = $txn_reference;
                                $creationData['user_id'] = $user_id;
                                $creationData['wallet_category'] = $wallet_category;
                                $creationData['product_plan_id'] = $product_plan_id;
                                $creationData['phone_number'] = $phone_numbers_array[$i];
                                $creationData['amount'] = $amount;
                                $creationData['status'] = $status;
                                $creationData['balance_before'] = $bulk_wallet_balance_before;
                                $creationData['balance_after'] = $bulk_wallet_balance_after;
                                $creationData['description'] = $description;
                                $creationData['user_screen_message'] = $user_message;
                                $creationData['admin_screen_message'] = $admin_message;
                                $transaction = Transaction::create($creationData); 

                                $walletLog['user_id'] = $user_id;
                                $walletLog['transaction_category'] = 'DATA_FROM_DATA_WALLET';
                                $walletLog['balance_before'] = $bulk_wallet_balance_before;
                                $walletLog['balance_after'] = $bulk_wallet_balance_after;
                                $walletLog['transaction_id'] = $transaction->id;
                                $walletLog['action_by'] = $user_id;
                                $walletLog['description'] = 'Data Purchase from data wallet';
                                $this->log_wallet_transactions($walletLog);
                                          
                            }
    
                            DB::commit();
                            if($failure > 0){
                                return ['status'=>2, 'message'=>" $failure issue(s) found. Check transaction history", 'data' => $display_results  ];   
                            }
                            // return ['status'=>1, 'message'=>'Bulk data transaction was successfully processed', 'data' => $display_results  ];
                        }


        }catch(Exception $exception){
            DB::rollBack();
            logger($exception->getMessage().' on line: '. $exception->getLine());

            $transaction->update([
                'status'               => $status,
                'balance_before'        => $user_details->main_wallet ?? null,
                'balance_after'        => $user_details->main_wallet ?? null,
                'user_screen_message'  =>$user_message,
                'admin_screen_message' => $admin_message,
                'phone_number' => $phone_number ?? null
            ]);

            // $description = 'Purchase of data';
            // $creationData['transaction_category'] = 'data';
            // $creationData['transaction_route'] = 'api';
            // $creationData['txn_reference'] = $txn_reference;
            // $creationData['user_id'] = $user_id;
            // $creationData['set_for_manual'] = $set_for_manual ?? 0;
            // $creationData['wallet_category'] = $wallet_category;
            // $creationData['product_plan_id'] = $product_plan_id;
            // $creationData['phone_number'] = $phone_number ?? NULL;
            // $creationData['amount'] = $amount;
            // $creationData['service_charge'] = 0;
            // $creationData['coupon_code_id'] = $coupon ?? NULL;
            // $creationData['discounted_amount'] = $amount;
            // $creationData['status'] = -1;
            // $creationData['balance_before'] =$user_details->main_wallet;
            // $creationData['balance_after'] = $user_details->main_wallet;
            // $creationData['description'] = $description ?? 'Purchase of data';
            // $creationData['user_screen_message'] = $check_purchase_limit['message'] ?? null;
            // $creationData['admin_screen_message'] = $check_purchase_limit['message'] ?? null;
            // $transaction = Transaction::create($creationData);


            // $walletLog['user_id'] = $user_id;
            // $walletLog['transaction_category'] = 'DATA_FROM_MAIN_WALLET';
            // $walletLog['balance_before'] = $user_details->main_wallet;
            // $walletLog['balance_after'] = $user_details->main_wallet;
            // $walletLog['transaction_id'] = $transaction->id;
            // $walletLog['action_by'] = $user_id;           
            // $walletLog['description'] = 'Data Purchase from main wallet';
            // $this->log_wallet_transactions($walletLog);


            // return [
            //     'id'=>$transaction->id,
            //     'txn_reference'=>$txn_reference ?? NULL,
            //     'status'=>-1,
            //     'actual_status' => -1,
            //     'status_code' => 500,
            //     'message' => $user_message ?? 'Transaction failed',
            //     'apiresponse' => $user_message ?? 'Transaction failed',
            //     'user_message' => $user_message ?? 'Transaction failed',
            //     'admin_message' => $admin_message ?? 'Transaction failed',
            //     "balance_before" => $user_details->main_wallet ?? NULL,
            //     "balance_after" =>  $user_details->main_wallet ?? NULL,
            //     "plan" => $plan_details->api_id,
            //     "Status" => match($status) {
            //         "1"   => "successful",
            //         "2"  => "refunded",
            //         "-1"  => "failed",
            //         1   => "successful",
            //         2  => "refunded",
            //         -1  => "failed",
            //         default => "failed"
            //     },
            //     "plan_network" => Network::where('id',$network_id)->value('network_name'),
            //     "plan_name" => $plan_details->product_plan_name ?? NULL,
            //     'plan_amount'=>$amount ?? NULL, 
            //     'create_date'=>date('Y-m-d H:i:s'), 
            //     'data' => $display_results ?? NULL
            // ];

            // return ['status'=>'-1', 'message'=>'Something went wrong... Please try again', 'data'=>[]];


            return $this->res([
                'id'             => $transaction->id ?? null,
                'txn_reference'  => $txn_reference,
                'status'         => -1,
                'status_code'    => 500, //confirm
            
                'message'        => $user_message,
                'user_message'   => $user_message,
                'admin_message'  => $admin_message,
            
                'balance_before' => $user_details->main_wallet ?? null,
                'balance_after'  => $user_details->main_wallet ?? null,
            
                'plan'           => $plan_details->api_id ?? null,
                'plan_network'   => Network::where('id',$network_id)->value('network_name'),
                'plan_name'      => $plan_details->product_plan_name ?? null,
                'plan_amount'    => $amount ?? null,
            
                'data'           => $display_results ?? [],
            ]);

        }

    }

    private function res(array $data = [])
   {
    $status = $data['status'] ?? -1;
    $message = $data['message'] ?? 'Transaction failed';

    return [
        'id'               => $data['id'] ?? null,
        'txn_reference'    => $data['txn_reference'] ?? null,
        'status'           => $status,
        'actual_status'    => $status,
        'status_code'      => $data['status_code'] ?? 200,

        'message'          => $message,
        'apiresponse'      => $message,
        'user_message'     => $data['user_message'] ?? $message,
        'admin_message'    => $data['admin_message'] ?? $message,

        'balance_before'   => $data['balance_before'] ?? 0,
        'balance_after'    => $data['balance_after'] ?? 0,

        'plan'             => $data['plan'] ?? null,
        'plan_network'     => $data['plan_network'] ?? null,
        'plan_name'        => $data['plan_name'] ?? null,
        'plan_amount'      => $data['plan_amount'] ?? null,

        'Status' => match((string)$status) {
            '1'  => 'successful',
            '2'  => 'refunded',
            '-1' => 'failed',
            default => 'unknown'
        },

        'create_date'      => now()->format('Y-m-d H:i:s'),
        // 'data'             => $data['data'] ?? [],
    ];
}

    public function buy_airtime_service($data){

        $network_id = $data['network_id'];
        $product_plan_category_id = $data['product_plan_category_id'];
        
        $pin = $data['pin'];
        $phone_number = $data['phone_number'];
        $product_plan_id = $data['product_plan_id'];
        $amount = $data['amount'];//not needed
        $actual_amount = $data['actual_amount'];
        $validatephonenetwork = $data['validatephonenetwork'];
        $user_id = $data['user_id'];//this is required
        $wallet_category = 'main_wallet';//this is required
        $txn_reference = $data['reference'] ?? NULL;

        if($txn_reference == NULL){
            //generate a unique one
            $txn_reference = $this->generateTxnReference('AIRTIME',$user_id);
        }

        $user_details = User::where('id',$user_id)->first();
        if($user_details->pin != $pin){
            return ['status'=>-1, 'message'=>'Pin mismatch found'];

        }

        $user_plan_id = $user_details->user_plan_id;
        $user_id = $user_details->id;
        $user_level = UserPlan::select('plan_level')->where('id',$user_plan_id)->first();
        $plan_level = $user_level->plan_level;

                
        $plan_details = ProductPlan::with('product_plan_category')
        ->where('visibility',1)
        ->where('id',$product_plan_id)->first();


        $user_level_selling = "user_level_".$plan_level."_selling_price";
        $purchase_discount =  $plan_details->$user_level_selling;
        $actual_discount_value = ceil(($purchase_discount/100) * $actual_amount); 
         
        //below forms the new amount to sell to the user
        $amount = $actual_discount_value < 0 || $actual_discount_value > $actual_amount ? $actual_amount : ($actual_amount - $actual_discount_value);
        

        $success = 0;
        $failure = 0;
        $status = -1;
        $message = 'Pending';
        $display_results = [];




        $data1['days_count'] = [1,7,30];
        $data1['user_id'] = auth()->id();
        $check_purchase_limit =  ProductsService::check_purchase_limit($data1);
        if($check_purchase_limit['status'] == -1){
            // return ['status'=>'-1', 'message'=>$check_purchase_limit['message'] ];
            $description = 'Purchase of airtime';
            $creationData['transaction_category'] = 'airtime';
            $creationData['transaction_route'] = 'api';
            $creationData['txn_reference'] = $txn_reference;
            $creationData['user_id'] = $user_id;
            $creationData['set_for_manual'] = $set_for_manual ?? 0;
            $creationData['wallet_category'] = $wallet_category;
            $creationData['product_plan_id'] = $product_plan_id;
            $creationData['phone_number'] = $phone_number ?? NULL;
            $creationData['amount'] = $actual_amount;
            $creationData['coupon_code_id'] = $coupon ?? NULL;
            $creationData['discounted_amount'] = $amount;
            $creationData['status'] = -1;
            $creationData['balance_before'] =$user_details->main_wallet;
            $creationData['balance_after'] = $user_details->main_wallet;
            $creationData['description'] = $description ?? 'Purchase of airtime';
            $creationData['user_screen_message'] = $check_purchase_limit['message']  ?? 'Transaction failed.';
            $creationData['admin_screen_message'] = $check_purchase_limit['message']  ?? 'Transaction failed.';
            $transaction = Transaction::create($creationData);


       
            $status = -1;
                return [
                    'id'=>$transaction->id,
                    'txn_reference'=>$txn_reference ?? NULL,
                    'status'=> -1,
                    'actual_status' => -1,
                    'status_code' => 503,
                    'message' => $check_purchase_limit['message'] ?? 'Transaction failed.',
                    'apiresponse' => $check_purchase_limit['message'] ?? 'Transaction failed.',
                    'user_message' => $check_purchase_limit['message'] ?? 'Transaction failed.',
                    'admin_message' => $check_purchase_limit['message'] ?? 'Transaction failed.',
                    "balance_before" => $user_details->main_wallet ?? NULL,
                    "balance_after" =>  $user_details->main_wallet ?? NULL,
                    "plan" => $plan_details->api_id,
                    "Status" => match($status) {
                        "1"   => "successful",
                        "2"  => "refunded",
                        "-1"  => "failed",
                        1   => "successful",
                        2  => "refunded",
                        -1  => "failed",
                        default => "failed"
                    },
                    "plan_network" => Network::where('id',$network_id)->value('network_name'),
                    "plan_name" => $plan_details->product_plan_name ?? NULL,
                    'plan_amount'=>$amount ?? NULL, 
                    'create_date'=>date('Y-m-d H:i:s'), 
                    'data' => $display_results ?? NULL
                ];
        }

      

        if($actual_amount < 50){
            // return ['status'=>'-1', 'message'=>'Insufficient wallet balance','data' => ''];
            $description = 'Purchase of airtime';
            $creationData['transaction_category'] = 'airtime';
            $creationData['transaction_route'] = 'api';
            $creationData['txn_reference'] = $txn_reference;
            $creationData['user_id'] = $user_id;
            $creationData['set_for_manual'] = $set_for_manual ?? 0;
            $creationData['wallet_category'] = $wallet_category;
            $creationData['product_plan_id'] = $product_plan_id;
            $creationData['phone_number'] = $phone_number ?? NULL;
            $creationData['amount'] = $actual_amount;
            $creationData['coupon_code_id'] = $coupon ?? NULL;
            $creationData['discounted_amount'] = $amount;
            $creationData['status'] = -1;
            $creationData['balance_before'] =$user_details->main_wallet;
            $creationData['balance_after'] = $user_details->main_wallet;
            $creationData['description'] = $description ?? 'Purchase of airtime';
            $creationData['user_screen_message'] = 'Amount cannot be less than 50';
            $creationData['admin_screen_message'] = 'Amount cannot be less than 50';
            $transaction = Transaction::create($creationData);


       
            $status = -1;
                return [
                    'id'=>$transaction->id,
                    'txn_reference'=>$txn_reference ?? NULL,
                    'status'=> -1,
                    'actual_status' => -1,
                    'status_code' => 503,
                    'message' => 'Amount cannot be less than 50',
                    'apiresponse' => 'Amount cannot be less than 50',
                    'user_message' => 'Amount cannot be less than 50',
                    'admin_message' => 'Amount cannot be less than 50',
                    "balance_before" => $user_details->main_wallet ?? NULL,
                    "balance_after" =>  $user_details->main_wallet ?? NULL,
                    "plan" => $plan_details->api_id,
                    "Status" => match($status) {
                        "1"   => "successful",
                        "2"  => "refunded",
                        "-1"  => "failed",
                        1   => "successful",
                        2  => "refunded",
                        -1  => "failed",
                        default => "failed"
                    },
                    "plan_network" => Network::where('id',$network_id)->value('network_name'),
                    "plan_name" => $plan_details->product_plan_name ?? NULL,
                    'plan_amount'=>$amount ?? NULL, 
                    'create_date'=>date('Y-m-d H:i:s'), 
                    'data' => $display_results ?? NULL
                ];
        }

   
        



        $user_id = $user_details->id;
        $phone_numbers = $phone_number;
        $phone_numbers = trim($phone_numbers);
        $phone_numbers_array = explode(',',$phone_numbers);
        $phone_numbers_count = count($phone_numbers_array);

        if($phone_numbers_count == 1){
            $phone_number = $phone_numbers;
            $validate_phone = (new UtilService())->phoneNumberValidation($phone_number);
            $validated_phone_number = $validate_phone['validated_phone_number'];
            if($validate_phone['status'] != 1){
                return ['status'=>-1, 'message'=>$validate_phone['message'].' Number is: '.$validated_phone_number  ];
            }
        }

        DB::beginTransaction();
        try{
                        // ////validate wallet
                        if($wallet_category == 'main_wallet'){
                            $wallet_before = $user_details->main_wallet;
                            $total_amount = $phone_numbers_count * $amount;
                            $actual_total_amount = $phone_numbers_count * $actual_amount;
                            if($total_amount > $wallet_before || $wallet_before < 0){
                                // return ['status'=>'-1', 'message'=>'Insufficient wallet balance' ];
                                // return ['status'=>'-1', 'message'=>'Insufficient wallet balance','data' => ''];
                                
                                $description = 'Purchase of airtime';
                                $creationData['transaction_category'] = 'airtime';
                                $creationData['transaction_route'] = 'api';
                                $creationData['txn_reference'] = $txn_reference;
                                $creationData['user_id'] = $user_id;
                                $creationData['set_for_manual'] = $set_for_manual ?? 0;
                                $creationData['wallet_category'] = $wallet_category;
                                $creationData['product_plan_id'] = $product_plan_id;
                                $creationData['phone_number'] = $phone_number ?? NULL;
                                $creationData['amount'] = $actual_amount;
                                $creationData['coupon_code_id'] = $coupon ?? NULL;
                                $creationData['discounted_amount'] = $amount;
                                $creationData['status'] = -1;
                                $creationData['balance_before'] =$user_details->main_wallet;
                                $creationData['balance_after'] = $user_details->main_wallet;
                                $creationData['description'] = $description;
                                $creationData['user_screen_message'] = 'Insufficient wallet balance';
                                $creationData['admin_screen_message'] = 'Insufficient wallet balance';
                                $transaction = Transaction::create($creationData);
                                DB::commit();


                                $status = -1;
                                    return [
                                        'id'=>$transaction->id,
                                        'txn_reference'=>$txn_reference ?? NULL,
                                        'status'=> -1,
                                        'actual_status' => -1,
                                        'status_code' => 503,
                                        'message' => 'Insufficient wallet balance',
                                        'apiresponse' => 'Insufficient wallet balance',
                                        'user_message' => 'Insufficient wallet balance',
                                        'admin_message' => 'Insufficient wallet balance',
                                        "balance_before" => $user_details->main_wallet ?? NULL,
                                        "balance_after" =>  $user_details->main_wallet ?? NULL,
                                        "plan" => $plan_details->api_id,
                                        "Status" => match($status) {
                                            "1"   => "successful",
                                            "2"  => "refunded",
                                            "-1"  => "failed",
                                            1 => "successful",
                                            2 => "refunded",
                                            -1 => "failed",
                                            default => "failed"
                                        },
                                        "plan_network" => Network::where('id',$network_id)->value('network_name'),
                                        "plan_name" => $plan_details->product_plan_name ?? NULL,
                                        'plan_amount'=>$total_amount ?? NULL, 
                                        'create_date'=>date('Y-m-d H:i:s'), 
                                        'data' => $display_results ?? NULL
                                    ];
                            }
                    
                            //calling the actual vending via the automation:
                            // $automation_details = Automation::where('id',$automation_id)->first();            
                            //TODO: candidate for separation
                            for($i = 0; $i < count($phone_numbers_array); $i++ ){
                                sleep(2); //add throttle here

                                $phone_number = $phone_numbers_array[$i];
                                $validate_phone = (new UtilService())->phoneNumberValidation($phone_number);
                                $validated_phone_number = $validate_phone['validated_phone_number'];
                               
                                
                                $wallet_before = User::where('id',$user_id)->first()->main_wallet;
                                $wallet_after = $wallet_before - $amount;

                           
                                if($validate_phone['status'] != 1){
                                     //something when wrong
                                     $status = -1;
                                     $user_message = 'This number is not a valid number: '.$phone_number;
                                     $admin_message = 'This number is not a valid number: '.$phone_number;
                                     $failure++;

                                }
                                //always check the wallet balance after every loop:
                                else if($wallet_before <= 0 || $wallet_after <= 0){
                                   
                                     $status = -1;
                                     $user_message = 'Airtime transaction failed.';
                                     $admin_message = 'Airtime transaction failed.';
                                     $failure++;

                                }else{
                                     $status = 0; //pending    
                                     $user_message = 'Airtime transaction pending.';
                                     $admin_message = 'pending_airtime_transaction';
                                     $success++;
                                }

           
                                $display_results[$i] = array(
                                    'message' => $user_message,
                                    'admin_message' => $admin_message,
                                    'status' => $status
                                );
                                       
                    
                                $description = 'Purchase of airtime';
                                $creationData['transaction_category'] = 'airtime';
                                $creationData['transaction_route'] = 'api';
                                $creationData['txn_reference'] = $txn_reference;
                                $creationData['user_id'] = $user_id;
                                $creationData['wallet_category'] = $wallet_category;
                                $creationData['product_plan_id'] = $product_plan_id;
                                $creationData['phone_number'] = $phone_numbers_array[$i];
                                $creationData['amount'] = $actual_amount;
                                $creationData['discounted_amount'] = $amount;
                                $creationData['status'] = $status;
                                $creationData['balance_before'] = $wallet_before;
                                $creationData['balance_after'] = $wallet_after;
                                $creationData['description'] = $description;
                                $creationData['user_screen_message'] = $user_message;
                                $creationData['admin_screen_message'] = $admin_message;
                                $transaction =  Transaction::create($creationData);

                                //log only pending transactions
                                if($status == 0){
                                    $walletLog['user_id'] = $user_id;
                                    $walletLog['transaction_category'] = 'AIRTIME';
                                    $walletLog['balance_before'] = $wallet_before;
                                    $walletLog['balance_after'] = $wallet_after;
                                    $walletLog['transaction_id'] = $transaction->id;
                                    $walletLog['action_by'] = $user_id;
                                    $walletLog['description'] = 'Airtime Purchase from main wallet';
                                    $this->log_wallet_transactions($walletLog);  
                                    
                                    User::where('id',$user_id)->update([
                                        'main_wallet' => $wallet_after
                                    ]);
                                }  
                            }

                            DB::commit();

                            $status = $status == 0 ? 1 : -1; //if its pending, then show the user as success so the backend can now process
                            return [
                                'id'=>$transaction->id,
                                'txn_reference'=>$txn_reference,
                                'status'=>$status,
                                'actual_status' => $status,
                                'message' => $user_message,
                                'apiresponse' => $user_message,
                                'user_message' => $user_message,
                                'admin_message' => $admin_message,
                                "balance_before" => $wallet_before,
                                "balance_after" => $wallet_after,
                                "plan" => $plan_details->api_id,
                                "Status" => match($status) {
                                    "0"   => "pending",
                                    "1"   => "successful",
                                    "2"  => "refunded",
                                    "-1"  => "failed",
                                    0   => "pending",
                                    1   => "successful",
                                    2  => "refunded",
                                    -1  => "failed",
                                    default => "unknown"
                                },
                                "plan_network" => Network::where('id',$network_id)->value('network_name'),
                                "plan_name" => $plan_details->product_plan_name,
                                'plan_amount'=>$amount, 
                                'create_date'=>date('Y-m-d H:i:s'), 
                                'data' => $display_results
                            ];

                    
                        } else{
                            return ['status'=>-1, 'message'=>'Wrong wallet selection', 'data'=>[]];
                        }



        }catch(Exception $exception){
            logger($exception->getMessage().' on line: '. $exception->getLine());
            DB::rollBack();
            return ['status'=>-1, 'message'=>'Something went wrong... Please try again', 'data'=>[]];
        }

    }

    public function buy_electricity_service($data){
        $metre_number = $data['metre_number'];
        $validation_extra_info = $data['validation_extra_info'];
        $electricity_product_plan_category_id = $data['electricity_product_plan_category_id'];
        $electricity_product_plan_id = $data['electricity_product_plan_id'];
        $no_of_slots = $data['no_of_slots'];
        $amount = $data['amount']; 
        // $amount = $data['actual_amount'];
        $pin = $data['pin'] ?? '';
        $user_id = $data['user_id'];
        $wallet_category = $data['wallet_category'];
        $txn_reference = $data['reference'] ?? NULL;
        $validated_address = $data['validated_address'] ?? 'nil';

        if($txn_reference == NULL){
            //generate a unique one
            $txn_reference = $this->generateTxnReference('ELECTRICITY',$user_id);
        }

        /////////////////////TO BE REVAMPED
        if($amount < 200){
            return ['status'=> -1, 'message'=>'Amount cannot be less than 200'];
        }

        $data1['days_count'] = [1,7,30];
        $data1['user_id'] = $user_id;
        $data1['product'] = 'utility_bills';
        $check_purchase_limit =  ProductsService::check_purchase_limit($data1);
        if($check_purchase_limit['status'] == -1){
            return [ 'status'=> -1, 'message' =>$check_purchase_limit['message'] ];
        }

        $success = 0;
        $failure = 0;
        $status = 0;
        $message = 'Pending';
        $display_results = [];

        $plan_details = ProductPlan::where('id',$electricity_product_plan_id)
        ->where('visibility',1)
        ->first();

        if(! $plan_details){
            return [ 'status'=> -1,'message' => 'plan details not found' ];
        }

        $user_details = User::where('id',$user_id)->first();
        if(! $user_details){
            return [ 'status'=> -1, 'message'=>'User record not found' ];
        }

        // if($user_details->pin != $pin){
        //     return ['status'=>'-1', 'message'=>'Incorrect PIN' ];
        // }

        $automation_id = $plan_details->automation_id;
       
        $plan_category_details = ProductPlanCategory::where('id',$electricity_product_plan_category_id)->first();
        if(! $plan_category_details){
            return ['status'=> -1, 'message'=>'plan category details not found' ];
        }

      
        $user_plan_id = $user_details->user_plan_id;
        $user_level = UserPlan::select('plan_level')->where('id',$user_plan_id)->first();
        $plan_level = $user_level->plan_level;
        $user_plan_selling_price = 'user_level_'.$plan_level.'_selling_price';

       
        //////////////////////    
        $automation_id = $plan_details->automation_id;
        $product_plan_category = $plan_details->product_plan_category;
        $actual_amount = abs($amount);

        $user_level_selling = "user_level_".$plan_level."_selling_price";
        $purchase_discount =  $plan_details->$user_level_selling;
        $actual_discount_value = ceil(($purchase_discount/100) * $actual_amount);  
        $amount = $actual_discount_value < 0 || $actual_discount_value > $actual_amount ? $actual_amount : ($actual_amount - $actual_discount_value);

     

        DB::beginTransaction();
        try{

              ////validate wallet
                        if($wallet_category == 'main_wallet'){
                            $wallet_before = $user_details->main_wallet;
                            $total_amount =  $no_of_slots * $amount;
                            if($total_amount > $wallet_before || $wallet_before < 0){
                              
                                $description = 'Purchase of electricity subscription';
                                $creationData['transaction_category'] = 'utility_bills';
                                $creationData['user_id'] = $user_id;
                                $creationData['txn_reference'] = $txn_reference;
                                $creationData['wallet_category'] = $wallet_category;
                                $creationData['product_plan_id'] = $electricity_product_plan_id;
                                $creationData['phone_number'] =  NULL;
                                $creationData['metre_number'] = $metre_number;
                                $creationData['validation_address'] = $validated_address;
                                // $creationData['electricity_tv_slots'] = 1;
                                $creationData['amount'] = $actual_amount;
                                $creationData['discounted_amount'] = $amount;
                                $creationData['status'] = -1;
                                $creationData['balance_before'] = $wallet_before;
                                $creationData['balance_after'] = $wallet_before;
                                $creationData['description'] = $description;
                                $creationData['user_screen_message'] = 'Insufficient wallet balance';
                                $creationData['admin_screen_message'] = 'Insufficient wallet balance';
                                $transaction = Transaction::create($creationData);


                                $walletLog['user_id'] = $user_id;
                                $walletLog['transaction_category'] = 'BILLS';
                                $walletLog['balance_before'] = $wallet_before;
                                $walletLog['balance_after'] = $wallet_before;
                                $walletLog['transaction_id'] = $transaction->id;
                                $walletLog['action_by'] = $user_details->id;
                                $walletLog['description'] = 'UTILITY BILLS Purchase from main wallet with transaction_id';
                                $this->log_wallet_transactions($walletLog);
                    
                                User::where('id',$user_id)->update([
                                    'main_wallet' => $wallet_before
                                ]);

                                DB::commit();
                          
                                return [
                                    'id'=>$transaction->id,
                                    'txn_reference'=>$txn_reference,
                                    'status'=> -1,
                                    'actual_status' => -1,
                                    'message' => 'Insufficient wallet balance',
                                    'apiresponse' =>'Insufficient wallet balance',
                                    'user_message' =>'Insufficient wallet balance',
                                    'admin_message' =>'Insufficient wallet balance',
                                    "balance_before" => $wallet_before,
                                    "balance_after" => $wallet_before,
                                    "plan" => $plan_details->api_id,
                                    "Status" => match($status) {
                                        "0"   => "pending",
                                        "1"   => "successful",
                                        "2"  => "refunded",
                                        "-1"  => "failed",
                                        0   => "pending",
                                        1   => "successful",
                                        2  => "refunded",
                                        -1  => "failed",
                                        default => "unknown"
                                    },
                                    "plan_network" => null,
                                    "plan_name" => $plan_details->product_plan_name,
                                    'plan_amount'=>$amount, 
                                    'create_date'=>date('Y-m-d H:i:s'), 
                                    'data' => []
                                ];

                            }
                    
                            //calling the actual vending via the automation:
                            $automation_details = Automation::where('id',$automation_id)->first();            
                            //TODO: candidate for separation
                       
                             //TODO: candidate for separation
                             for($i = 1; $i <= $no_of_slots; $i++ ){
                          
                                $data['automation_details'] = $automation_details;
                                $data['metre_number'] = $metre_number;
                                $data['plan_id'] = $electricity_product_plan_id;
                                $data['total_amount'] = $total_amount;
                                $data['slots'] = $no_of_slots;
                                $data['validation_extra_info'] = $validation_extra_info;
                                $data['product_plan_category_name'] = $plan_category_details->product_plan_category_name;
                                $data['phone_number'] = $user_details->phone_number;
                                $data['user_id'] = $user_id;
                                $buy_electricity_subscription = AutomationLogic::initiateElectricityPurchase($data);
                                logger('ELECTRICITY SERVICE: '.json_encode($buy_electricity_subscription));
                                $extra_info = $buy_electricity_subscription['extra_info'] ?? 'nil';
                                $token = $buy_electricity_subscription['token'] ?? 'nil';
                               
                                if($buy_electricity_subscription['status'] == 1){
                                    $success++;
                                    $status = 1;
                                    $wallet_before = User::select('main_wallet')->where('id',$user_id)->first()->main_wallet;
                                    $wallet_after = $wallet_before - $amount;
                                }else{
                                    //it might be processing or it failed
                                    $status = -1;
                                    $failure++;
                                    $wallet_before = User::select('main_wallet')->where('id',$user_id)->first()->main_wallet;
                                    $wallet_after = $wallet_before;
                                }
                                //simulate success

                                $user_message = $buy_electricity_subscription['user_message'];
                                $admin_message = $buy_electricity_subscription['admin_message'];
                                $display_results[] = array(
                                    'message' => $user_message,
                                    'admin_message' => $admin_message,
                                    'status' => $status
                                );
                               
                                // $validate_metre_name = (new MegaSubElectricity(metre_number: $metre_number, plan_id: $electricity_product_plan_id, user_id: $user_id))->validateMetreNumber();
                                // $validated_name = $validate_metre_name['name'] ?? '';
                                // $validated_address = $validate_metre_name['address'] ?? 'Nil';
                    
                                //this should not run though because it has already been checked
                                if($wallet_after <= 0){
                                    $status = -1;
                                    $user_message = 'Failed due to insufficient balance';
                                    $admin_message = 'Failed due to insufficient balance';
                                    $failure++;
                                    $display_results[] = array(
                                        'message' => $user_message,
                                        'admin_message' => $admin_message,
                                        'status' => $status
                                    );
                                }

                               
                               
    
                                $description = 'Purchase of electricity subscription';
                                $creationData['transaction_category'] = 'utility_bills';
                                $creationData['user_id'] = $user_id;
                                $creationData['electricity_token'] = $token;
                                $creationData['txn_reference'] = $txn_reference;
                                $creationData['wallet_category'] = $wallet_category;
                                $creationData['product_plan_id'] = $electricity_product_plan_id;
                                $creationData['phone_number'] =  NULL;
                                $creationData['metre_number'] = $metre_number;
                                $creationData['validation_address'] = $validated_address;
                                // $creationData['electricity_tv_slots'] = 1;
                                $creationData['amount'] = $actual_amount;
                                $creationData['discounted_amount'] = $amount;
                                $creationData['status'] = $status;
                                $creationData['balance_before'] = $wallet_before;
                                $creationData['balance_after'] = $wallet_after;
                                $creationData['description'] = $description;
                                $creationData['user_screen_message'] = $user_message;
                                $creationData['admin_screen_message'] = $admin_message;
                                $transaction = Transaction::create($creationData);


                                $walletLog['user_id'] = $user_id;
                                $walletLog['transaction_category'] = 'BILLS';
                                $walletLog['balance_before'] = $wallet_before;
                                $walletLog['balance_after'] = $wallet_after;
                                $walletLog['transaction_id'] = $transaction->id;
                                $walletLog['action_by'] = $user_details->id;
                                $walletLog['description'] = 'UTILITY BILLS Purchase from main wallet with transaction_id';
                                $this->log_wallet_transactions($walletLog);
                    
                                User::where('id',$user_id)->update([
                                    'main_wallet' => $wallet_after
                                ]);

                            }

                            DB::commit();

                            
                    
                            if($failure > 1){
                              return ['status'=>2,'user_message' => $user_message,'admin_message' => $admin_message, 'extra_info' => $extra_info, 'token' => $token,'validation_address' => $validated_address,'message'=>"$user_message. $failure issue(s) found. Check transaction history", 'data' => $display_results  ];   
                            }else{
                                return [
                                    'id'=>$transaction->id,
                                    'txn_reference'=>$txn_reference,
                                    'status'=>$status,
                                    'actual_status' => $status,
                                    'message' => $user_message,
                                    'apiresponse' =>$user_message,
                                    'user_message' =>$user_message,
                                    'admin_message' =>$admin_message,
                                    "balance_before" => $wallet_before,
                                    "balance_after" => $wallet_after,
                                    "plan" => $plan_details->api_id,
                                    "Status" => match($status) {
                                        "0"   => "pending",
                                        "1"   => "successful",
                                        "2"  => "refunded",
                                        "-1"  => "failed",
                                        0   => "pending",
                                        1   => "successful",
                                        2  => "refunded",
                                        -1  => "failed",
                                        default => "unknown"
                                    },
                                    "plan_network" => null,
                                    "plan_name" => $plan_details->product_plan_name,
                                    'plan_amount'=>$amount, 
                                    'create_date'=>date('Y-m-d H:i:s'), 
                                    'data' => []
                                ];
                            }

                            // return ['status'=>1, 'user_message' => $user_message,'admin_message' => $admin_message,'extra_info' => $extra_info, 'token' => $token,'validation_address' => $validated_address,'message'=>'Transaction was successfully processed', 'data' => $display_results  ];
                    
                        } else{
                            return ['status'=> -1, 'message'=>'Wrong wallet selection', 'data'=>[]];
                        }



        }catch(Exception $exception){
            logger($exception->getMessage().' on line: '. $exception->getLine());
            DB::rollBack();
            // return ['status'=> -1, 'message'=>'Something went wrong... Please try again', 'data'=>[]];
            return [
                'id'=>$transaction->id ?? rand(1000,9999),
                'txn_reference'=>$txn_reference,
                'status'=> -1,
                'actual_status' =>  -1,
                'message' => $user_message,
                'apiresponse' =>$user_message,
                'user_message' =>$user_message,
                'admin_message' =>$admin_message,
                "balance_before" => $wallet_before,
                "balance_after" => $wallet_after,
                "plan" => $plan_details->api_id,
                "Status" => match($status) {
                    "0"   => "pending",
                    "1"   => "successful",
                    "2"  => "refunded",
                    "-1"  => "failed",
                    0   => "pending",
                    1   => "successful",
                    2  => "refunded",
                    -1  => "failed",
                    default => "unknown"
                },
                "plan_network" => null,
                "plan_name" => $plan_details->product_plan_name,
                'plan_amount'=>$amount, 
                'create_date'=>date('Y-m-d H:i:s'), 
                'data' => []
            ];
        }
    }

    public function buy_cable_service($data){
        $smart_card_number = $data['smart_card_number'];
        $validation_customer_name = $data['validation_customer_name'];
        $cable_product_plan_category_id = $data['cable_product_plan_category_id'];
        $cable_product_plan_id = $data['cable_product_plan_id'];
        $pin = $data['pin'];
        $user_id = $data['user_id'];//this is required
        $user = $data['user'];//this is required
        $no_of_slots = $data['no_of_slots'];
        $wallet_category = $data['wallet_category'];

        $txn_reference = $data['reference'] ?? NULL;
        if($txn_reference == NULL){
            //generate a unique one
            $txn_reference = $this->generateTxnReference('CABLE',$user_id);
        }

        
        $success = 0;
        $failure = 0;
        $status = 0;
        $message = 'Pending';
        $display_results = [];
       
        $data1['days_count'] = [1,7,30];
        $data1['user_id'] = $user_id;
        // $data1['product'] = 'cable_subscription';
        $user_details = User::where('id',$user_id)->first();
        // if(! $user_details){
        //     return ['status'=>'-1', 'message'=>'please logout and login again' ];
        // }

        // if($user_details->pin != $pin){
        //     return ['status'=>'-1', 'message'=>'Incorrect PIN' ];
        // }


        $plan_details = ProductPlan::where('id',$cable_product_plan_id)->where('visibility',1)->first();
        if(! $plan_details){
            return ['status'=>'-1', 'message'=>'plan details not found' ];
        }

        $user_plan_id = $user_details->user_plan_id;
        $user_level = UserPlan::select('plan_level')->where('id',$user_plan_id)->first();
        $plan_level = $user_level->plan_level;
        $user_plan_selling_price = 'user_level_'.$plan_level.'_selling_price';
        $amount = abs($plan_details->$user_plan_selling_price);

        $check_purchase_limit =  ProductsService::check_purchase_limit($data1);
        if($check_purchase_limit['status'] == -1){
            // return ['status'=>'-1', 'message'=>$check_purchase_limit['message'] ];
            $limitmess = $check_purchase_limit['message'] ?? 'limit reached';
            $description = 'Purchase of cable subscription';
            $creationData['transaction_category'] = 'cable_subscription';
            $creationData['user_id'] = $user_id;
            $creationData['txn_reference'] = $txn_reference;
            $creationData['wallet_category'] = $wallet_category;
            $creationData['product_plan_id'] = $cable_product_plan_id;
            $creationData['phone_number'] =  NULL;
            $creationData['smart_card_number'] = $smart_card_number;
            $creationData['cable_tv_slots'] = 1;
            $creationData['amount'] = $amount;
            $creationData['discounted_amount'] = $amount;
            $creationData['status'] = -1;
            $creationData['balance_before'] = $user_details->main_wallet;
            $creationData['balance_after'] = $user_details->main_wallet;
            $creationData['description'] = $description;
            $creationData['user_screen_message'] = $limitmess;
            $creationData['admin_screen_message'] = $limitmess;
            $transaction = Transaction::create($creationData);

            $walletLog['user_id'] = $user_id;
            $walletLog['transaction_category'] = 'CABLE';
            $walletLog['balance_before'] = $user_details->main_wallet;
            $walletLog['balance_after'] =$user_details->main_wallet;
            $walletLog['transaction_id'] = $transaction->id;
            $walletLog['action_by'] =  $user_id;        
            $walletLog['description'] = 'CABLE Purchase from main wallet';
            $this->log_wallet_transactions($walletLog);


            User::where('id',$user_id)->update([
                'main_wallet' => $user_details->main_wallet
            ]);

            DB::commit();

      
            return [
                'id'=>$transaction->id,
                'txn_reference'=>$txn_reference,
                'status'=> -1,
                'actual_status' => -1,
                'message' => $limitmess,
                'apiresponse' =>$limitmess,
                'user_message' =>$limitmess,
                'admin_message' =>$limitmess,
                "balance_before" => $user_details->main_wallet,
                "balance_after" => $user_details->main_wallet,
                "plan" => $plan_details->api_id,
                "Status" => match($status) {
                    "0"   => "pending",
                    "1"   => "successful",
                    "2"  => "refunded",
                    "-1"  => "failed",
                    0   => "pending",
                    1   => "successful",
                    2  => "refunded",
                    -1  => "failed",
                    default => "unknown"
                },
                "plan_network" => null,
                "plan_name" => $plan_details->product_plan_name,
                'plan_amount'=>$amount, 
                'create_date'=>date('Y-m-d H:i:s'), 
                'data' => []
            ];
        }

       
        $automation_id = $plan_details->automation_id;
        // $data_value_mb = $plan_details->data_size_in_mb ?? 0;

        $plan_category_details = ProductPlanCategory::where('id',$cable_product_plan_category_id)->first();
        if(! $plan_category_details){
            return ['status'=>'-1', 'message'=>'plan category details not found' ];
        }
     
        DB::beginTransaction();
        try{

              ////validate wallet
                        if($wallet_category == 'main_wallet'){
                            $wallet_before = $user_details->main_wallet;
                            $total_amount =  $no_of_slots * $amount;
                            if($total_amount > $wallet_before || $wallet_before < 0){
                                // return ['status'=>'-1', 'message'=>'Insufficient wallet balance' ];
                                $description = 'Purchase of cable subscription';
                                $creationData['transaction_category'] = 'cable_subscription';
                                $creationData['user_id'] = $user_id;
                                $creationData['txn_reference'] = $txn_reference;
                                $creationData['wallet_category'] = $wallet_category;
                                $creationData['product_plan_id'] = $cable_product_plan_id;
                                $creationData['phone_number'] =  NULL;
                                $creationData['smart_card_number'] = $smart_card_number;
                                $creationData['cable_tv_slots'] = 1;
                                $creationData['amount'] = $amount;
                                $creationData['discounted_amount'] = $amount;
                                $creationData['status'] = -1;
                                $creationData['balance_before'] = $wallet_before;
                                $creationData['balance_after'] = $wallet_before;
                                $creationData['description'] = $description;
                                $creationData['user_screen_message'] = 'Insufficient wallet balance';
                                $creationData['admin_screen_message'] = 'Insufficient wallet balance';
                                $transaction = Transaction::create($creationData);

                                $walletLog['user_id'] = $user_id;
                                $walletLog['transaction_category'] = 'CABLE';
                                $walletLog['balance_before'] = $wallet_before;
                                $walletLog['balance_after'] = $wallet_before;
                                $walletLog['transaction_id'] = $transaction->id;
                                $walletLog['action_by'] =  $user_id;        
                                $walletLog['description'] = 'CABLE Purchase from main wallet';
                                $this->log_wallet_transactions($walletLog);
                    
                    
                                User::where('id',$user_id)->update([
                                    'main_wallet' => $wallet_before
                                ]);

                                DB::commit();

                          
                                return [
                                    'id'=>$transaction->id,
                                    'txn_reference'=>$txn_reference,
                                    'status'=> -1,
                                    'actual_status' => -1,
                                    'message' => 'Insufficient wallet balance',
                                    'apiresponse' =>'Insufficient wallet balance',
                                    'user_message' =>'Insufficient wallet balance',
                                    'admin_message' =>'Insufficient wallet balance',
                                    "balance_before" => $wallet_before,
                                    "balance_after" => $wallet_before,
                                    "plan" => $plan_details->api_id,
                                    "Status" => match($status) {
                                        "0"   => "pending",
                                        "1"   => "successful",
                                        "2"  => "refunded",
                                        "-1"  => "failed",
                                        0   => "pending",
                                        1   => "successful",
                                        2  => "refunded",
                                        -1  => "failed",
                                        default => "unknown"
                                    },
                                    "plan_network" => null,
                                    "plan_name" => $plan_details->product_plan_name,
                                    'plan_amount'=>$amount, 
                                    'create_date'=>date('Y-m-d H:i:s'), 
                                    'data' => []
                                ];

                            }
                    
                            //calling the actual vending via the automation:
                            $automation_details = Automation::where('id',$automation_id)->first();            
                            //TODO: candidate for separation
                       
                             //TODO: candidate for separation
                             for($i = 1; $i <= $no_of_slots; $i++ ){
                              

                                $dataa['automation_details'] = $automation_details;
                                $dataa['token'] = $automation_details->api_public_key;
                                $dataa['smart_card_number'] = $smart_card_number;
                                $dataa['plan_id'] = $cable_product_plan_id;
                                $dataa['total_amount'] = $total_amount;
                                $dataa['slots'] = 1;
                                $dataa['validation_customer_name'] = $validation_customer_name;
                                $dataa['validation_extra_info'] = $validation_customer_name;
                                $dataa['product_plan_category_name'] = $plan_category_details->product_plan_category_name;
                                $dataa['user_id'] = $user_id;
                                $buy_cable_subscription = AutomationLogic::initiateCablePurchase($dataa);
                                logger('CABLEEE SERVICE: '.json_encode($buy_cable_subscription));



                                if($buy_cable_subscription['status'] == 1){
                                    $success++;
                                    $status = 1;
                                    $wallet_before = User::where('id',$user_id)->first()->main_wallet;
                                    $wallet_after = $wallet_before - $amount;
                                }else{
                                    //it might be processing or it failed
                                    $status = -1;
                                    $failure++;
                                    $wallet_before = User::where('id',$user_id)->first()->main_wallet;
                                    $wallet_after = $wallet_before;
                                }
                                //simulate success

                                $user_message = $buy_cable_subscription['user_message'];
                                $admin_message = $buy_cable_subscription['admin_message'];
                                $display_results[$i] = array(
                                    'message' => $user_message,
                                    'admin_message' => $admin_message,
                                    'status' => $status
                                );
                               
                    
                    
                                //this should not run though because it has already been checked
                                if($wallet_after <= 0){
                                    $status = -1;
                                    $user_message = 'Failed due to insufficient balance';
                                    $admin_message = 'Failed due to insufficient balance';
                                    $failure++;
                                    $display_results[$i] = array(
                                        'message' => $user_message,
                                        'admin_message' => $admin_message,
                                        'status' => $status
                                    );
                                }
                        
                                $description = 'Purchase of cable subscription';
                                $creationData['transaction_category'] = 'cable_subscription';
                                $creationData['user_id'] = $user_id;
                                $creationData['txn_reference'] = $txn_reference;
                                $creationData['wallet_category'] = $wallet_category;
                                $creationData['product_plan_id'] = $cable_product_plan_id;
                                $creationData['phone_number'] =  NULL;
                                $creationData['smart_card_number'] = $smart_card_number;
                                $creationData['cable_tv_slots'] = 1;
                                $creationData['amount'] = $amount;
                                $creationData['discounted_amount'] = $amount;
                                $creationData['status'] = $status;
                                $creationData['balance_before'] = $wallet_before;
                                $creationData['balance_after'] = $wallet_after;
                                $creationData['description'] = $description;
                                $creationData['user_screen_message'] = $user_message;
                                $creationData['admin_screen_message'] = $admin_message;
                                $transaction = Transaction::create($creationData);

                                $walletLog['user_id'] = $user_id;
                                $walletLog['transaction_category'] = 'CABLE';
                                $walletLog['balance_before'] = $wallet_before;
                                $walletLog['balance_after'] = $wallet_after;
                                $walletLog['transaction_id'] = $transaction->id;
                                $walletLog['action_by'] =  $user_id;        
                                $walletLog['description'] = 'CABLE Purchase from main wallet';
                                $this->log_wallet_transactions($walletLog);
                    
                                User::where('id',$user_id)->update([
                                    'main_wallet' => $wallet_after
                                ]);
                    
                            }

                            DB::commit();
                    
                            if($failure > 1){
                              return ['status'=>2, 'user_message' => $user_message,'admin_message' => $admin_message,'message'=>" $failure issue(s) found. Check transaction history", 'data' => $display_results  ];   
                            }else{
                              
                                    return [
                                        'id'=>$transaction->id,
                                        'txn_reference'=>$txn_reference,
                                        'status'=>$status,
                                        'actual_status' => $status,
                                        'message' => $user_message,
                                        'apiresponse' =>$user_message,
                                        'user_message' =>$user_message,
                                        'admin_message' =>$admin_message,
                                        "balance_before" => $wallet_before,
                                        "balance_after" => $wallet_after,
                                        "plan" => $plan_details->api_id,
                                        "Status" => match($status) {
                                            "0"   => "pending",
                                            "1"   => "successful",
                                            "2"  => "refunded",
                                            "-1"  => "failed",
                                            0   => "pending",
                                            1   => "successful",
                                            2  => "refunded",
                                            -1  => "failed",
                                            default => "unknown"
                                        },
                                        "plan_network" => null,
                                        "plan_name" => $plan_details->product_plan_name,
                                        'plan_amount'=>$amount, 
                                        'create_date'=>date('Y-m-d H:i:s'), 
                                        'data' => []
                                    ];
                            }
                            
                            // return ['status'=>1,'user_message' => $user_message,'admin_message' => $admin_message, 'message'=>'Transaction was successfully processed', 'data' => $display_results  ];
                    
                        } else{
                            return ['status'=>'-1','admin_message' => 'Sorry transaction failed', 'message'=>'Wrong wallet selection', 'data'=>[]];
                        }



        }catch(Exception $exception){
            logger($exception->getMessage().' on line: '. $exception->getLine());
            DB::rollBack();
            // return ['status'=>'-1', 'message'=>'Something went wrong... Please try again', 'data'=>[]];
            $description = 'Purchase of cable subscription';
            $creationData['transaction_category'] = 'cable_subscription';
            $creationData['user_id'] = $user_id;
            $creationData['txn_reference'] = $txn_reference;
            $creationData['wallet_category'] = $wallet_category;
            $creationData['product_plan_id'] = $cable_product_plan_id;
            $creationData['phone_number'] =  NULL;
            $creationData['smart_card_number'] = $smart_card_number;
            $creationData['cable_tv_slots'] = -1;
            $creationData['amount'] = $amount;
            $creationData['discounted_amount'] = $amount;
            $creationData['status'] = -1;
            $creationData['balance_before'] = $user_details->main_wallet;
            $creationData['balance_after'] = $user_details->main_wallet;
            $creationData['description'] = $description;
            $creationData['user_screen_message'] = 'Unsuccesful. Please try again';
            $creationData['admin_screen_message'] =  'Unsuccesful. Please try again '.$exception->getMessage();
            $transaction = Transaction::create($creationData);

            $walletLog['user_id'] = $user_id;
            $walletLog['transaction_category'] = 'CABLE';
            $walletLog['balance_before'] = $user_details->main_wallet;
            $walletLog['balance_after'] =$user_details->main_wallet;
            $walletLog['transaction_id'] = $transaction->id;
            $walletLog['action_by'] =  $user_id;        
            $walletLog['description'] = 'CABLE Purchase from main wallet';
            $this->log_wallet_transactions($walletLog);


            User::where('id',$user_id)->update([
                'main_wallet' => $user_details->main_wallet
            ]);

            DB::commit();

            return [
                'id'=>$transaction->id,
                'txn_reference'=>$txn_reference,
                'status'=> -1,
                'actual_status' => -1,
                'message' => 'Unsuccesful. Please try again',
                'apiresponse' =>'Unsuccesful. Please try again',
                'user_message' =>'Unsuccesful. Please try again',
                'admin_message' =>'Unsuccesful. Please try again '.$exception->getMessage(),
                "balance_before" => $user_details->main_wallet,
                "balance_after" => $user_details->main_wallet,
                "plan" => $plan_details->api_id,
                "Status" => match($status) {
                    "0"   => "pending",
                    "1"   => "successful",
                    "2"  => "refunded",
                    "-1"  => "failed",
                    0   => "pending",
                    1   => "successful",
                    2  => "refunded",
                    -1  => "failed",
                    default => "unknown"
                },
                "plan_network" => null,
                "plan_name" => $plan_details->product_plan_name,
                'plan_amount'=>$amount, 
                'create_date'=>date('Y-m-d H:i:s'), 
                'data' => []
            ];

        }

    }

    static public function check_purchase_limit($data){

        $days_count = $data['days_count'] ;
        $user_id = $data['user_id'] ?? NULL; // null should not happen

        for($i=0; $i < count($days_count); $i++){
            if($days_count[$i] == 1){
                $start_date = date('Y-m-d');
                $end_date = date('Y-m-d');
                $product_purchase_limit = Setting::where('field_name','product_purchase_limit_daily')->first()->field_value ?? 1000000000;
                $day_variable = 'today';
    
            }else if($days_count[$i] == 7){
                $end_date = date('Y-m-d');
                $start_date =  date('Y-m-d', strtotime('-'.$days_count[$i].' days'));
                $product_purchase_limit = Setting::where('field_name','product_purchase_limit_last_7_days')->first()->field_value ?? 1000000000;
                $day_variable = 'the last 7 days';
            }else if($days_count[$i] == 30){
                $end_date = date('Y-m-d');
                $start_date =  date('Y-m-d', strtotime('-'.$days_count[$i].' days'));
                $product_purchase_limit = Setting::where('field_name','product_purchase_limit_last_30_days')->first()->field_value ?? 1000000000;
                $day_variable = 'the last 30 days';
            }else{
                $product_purchase_limit = 1000000000;
            }
    
    
            $check_transaction_sum = Transaction::where('user_id',$user_id)->where('status',1)->whereDate('created_at','>=',$start_date)->whereDate('created_at','<=',$end_date)->sum('amount');
            if($check_transaction_sum >= $product_purchase_limit){
                return [
                    'status' => -1,
                    'message' => 'Sorry, transaction limit has been reached for '.$day_variable.'. Reach out to our Support team  via whatsapp to increase limit. Thank you.'
                    // 'message' => $check_transaction_sum
                ];
            }
    
        }
     
        return [
            'status' => 1,
            'message' => 'Good. User can still carry out transaction'
        ];

        
      

        // $days_count = $data['days_count'];
        // $product = $data['product'];
        // $user_id = $data['user_id'] ?? NULL; // null should not happen

        // for($i=0; $i < count($days_count); $i++){
        //     if($days_count[$i] == 1){
        //         $start_date = date('Y-m-d');
        //         $end_date = date('Y-m-d');
        //         $product_purchase_limit = Product::where('slug',$product)->first()->maximum_product_purchase_day ?? 1000000000;
        //         $day_variable = 'today';
        //     }else if($days_count[$i] == 7){
        //         $start_date = date('Y-m-d');
        //         $end_date = date('Y-m-d');
        //         $product_purchase_limit = Product::where('slug',$product)->first()->maximum_product_purchase_7_days ?? 1000000000;
        //         $day_variable = 'the last 7 days';
        //     }else if($days_count[$i] == 30){
        //         $start_date = date('Y-m-d');
        //         $end_date = date('Y-m-d');
        //         $product_purchase_limit = Product::where('slug',$product)->first()->maximum_product_purchase_30_days ?? 1000000000;
        //         $day_variable = 'the last 30 days';
        //     }else{
        //         $product_purchase_limit = 1000000000;
        //     }
    
    
        //     $check_transaction_sum = Transaction::where('user_id',$user_id)->where('status',1)
        //     ->whereDate('created_at','>=',$start_date)
        //     ->whereDate('created_at','<=',$end_date)
        //     ->sum('amount');
        //     if($check_transaction_sum >= $product_purchase_limit){
        //         return [
        //             'status' => -1,
        //             'message' => 'Sorry, transaction limit has been reached for '.$day_variable.'. Reach out to our Support team  via whatsapp to increase limit. Thank you.'
        //             // 'message' => $check_transaction_sum
        //         ];
        //     }
    
        // }
     
        // return [
        //     'status' => 1,
        //     'message' => 'Good. User can still carry out transaction'
        // ];   
    }
}
