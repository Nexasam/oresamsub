<?php
namespace App\Http\Services\Api\v1\VendorUsersApi\Products;

use Exception;
use App\Models\User;
use App\Models\Product;
use App\Models\Setting;
use App\Models\UserPlan;
use App\Models\Automation;
use App\Models\ProductPlan;
use App\Models\Transaction;
use App\Models\UserBulkDataWallet;
use Illuminate\Support\Facades\DB;
use App\Models\ProductPlanCategory;
use App\Services\Utils\UtilService;
use App\Traits\WalletTransactionLogs;
use App\Services\Api\Automation\OgdamsAutomation\OgdamsVendData;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubCableTV;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubVendData;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubElectricity;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubVendAirtime;

class ProductsService{
    use WalletTransactionLogs;

    public function fetch_product_plans($data){
        $network_id = $data['network_id'];
        $amount = $data['amount'];
        $plan_category_id = $data['plan_category_id'];
        $product_slug = $data['product_slug'];//this is required
        $user_id = $data['user_id'];//this is required
        
        $product_id = Product::where('slug',$product_slug)->first()->id;
        logger($plan_category_id);
         
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
        $user_details = User::where('id',$user_id)->first();

        $user_plan_id = $user_details->user_plan_id;
        $user_id = $user_details->id;
        $user_level = UserPlan::select('plan_level')->where('id',$user_plan_id)->first();
        $plan_level = $user_level->plan_level;

        
        foreach($product_plan_categories as $key=>$product_plan_category){
            //get the automation id
            //get the product_category_id 

            if($product_slug == 'airtime'){
                $product_plans = ProductPlan::where('product_plan_category_id',$product_plan_category->id)
                ->where('automation_id',$product_plan_category->automation_id)
                ->where('visibility',1)
                ->limit(1)
                ->get();
            }else{
                $product_plans = ProductPlan::where('product_plan_category_id',$product_plan_category->id)
                ->where('visibility',1)
                ->where('automation_id',$product_plan_category->automation_id)
                ->orderBy('data_size_in_mb')
                ->get();
            }

            if(count($product_plans) > 0){
                foreach($product_plans as $product_plan){

                    $user_level_selling = "user_level_".$plan_level."_selling_price";
                    // $user_level_selling = "{user_level_$user_level_selling_price}";
                    $selling_price = $product_plan->$user_level_selling;
                    
                    if( ( $product_slug == 'airtime' || $product_slug == 'utility_bills' ) && $amount != ''){
                          $purchase_discount = $product_plan->$user_level_selling;
                          $actual_discount_value = ceil(($purchase_discount/100) * $amount);  
                          $discounted_selling_price = $amount - abs($actual_discount_value);
                          $selling_price = 0; //this is from the system, not applicable for airtime
                    }else{
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
                ->where('product_id',$product_id)->get();
            }else{
                $product_plans_categories = ProductPlanCategory::with('product')
                ->where('visibility',1)
                ->where('product_id',$product_id)->get();
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

    public function buy_data_service($data){
       
        $network_id = $data['network_id'];
        $phone_number = $data['phone_number'];
        $product_plan_category_id = $data['product_plan_category_id'];
        $product_plan_id = $data['product_plan_id'];
        $pin = $data['pin'];
        $wallet_category = $data['wallet_category'];
        $validatephonenetwork = $data['validatephonenetwork'];
        $user_id = $data['user_id'];//this is required

     

        $success = 0;
        $failure = 0;
        $status = 0;
        $message = 'Pending';
        $display_results = [];

        $data1['days_count'] = [1,7,30];
        $data1['user_id'] = $user_id;
        $data1['product'] = 'data';
        $check_purchase_limit =  ProductsService::check_purchase_limit($data1);
    

        $plan_details = ProductPlan::where('id',$product_plan_id)->where('visibility',1)->first();
        $automation_id = $plan_details->automation_id;
        $data_value_mb = $plan_details->data_size_in_mb ?? 0;

        $user_details = User::where('id',$user_id)->first();
        if(! $user_details){
            //end session and redirect to login
            return ['status'=>'-1', 'message'=>'User record not found' ];
        }





        $user_plan_id = $user_details->user_plan_id;
        if($user_plan_id == NULL){
            //end session and redirect to login
            return ['status'=>'-1', 'message'=>'User plan ID is null' ];
        }

        $user_level = UserPlan::select('plan_level')->where('id',$user_plan_id)->first();
        $plan_level = $user_level->plan_level;
        $user_plan_selling_price = 'user_level_'.$plan_level.'_selling_price';
        $amount = abs($plan_details->$user_plan_selling_price);

        if($check_purchase_limit['status'] == -1){
            
            $description = 'Purchase of data';
            $creationData['transaction_category'] = 'data';
            $creationData['user_id'] = $user_id;
            $creationData['wallet_category'] = $wallet_category;
            $creationData['product_plan_id'] = $product_plan_id;
            $creationData['phone_number'] = $phone_number;
            $creationData['amount'] = $amount;
            $creationData['discounted_amount'] = $amount;
            $creationData['status'] = -1;
            $creationData['balance_before'] = $user_details->main_wallet;
            $creationData['balance_after'] = $user_details->main_wallet;
            $creationData['description'] = $description;
            $creationData['user_screen_message'] = 'Sorry, something went wrong';
            $creationData['admin_screen_message'] =$check_purchase_limit['message'];
            $transaction = Transaction::create($creationData);


            $walletLog['user_id'] = $user_id;
            $walletLog['transaction_category'] = 'DATA_FROM_MAIN_WALLET';
            $walletLog['balance_before'] = $user_details->main_wallet;
            $walletLog['balance_after'] = $user_details->main_wallet;
            $walletLog['transaction_id'] = $transaction->id;
            $walletLog['action_by'] = $user_id;           
            $walletLog['description'] = 'Data Purchase from main wallet';
            $this->log_wallet_transactions($walletLog);
            return ['status'=>'-1', 'message'=>$check_purchase_limit['message']  ];
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

              ////validate wallet
                        if($wallet_category == 'main_wallet'){
                            $wallet_before = $user_details->main_wallet;
                            $total_amount = $phone_numbers_count * $amount;
                            if($total_amount > $wallet_before || $wallet_before < 0){
                                return ['status'=>'-1', 'message'=>'Insufficient wallet balance...' ];
                            }
                    
                            //calling the actual vending via the automation:
                            $automation_details = Automation::where('id',$automation_id)->first();
                    
                            //TODO: candidate for separation:
                            for($i = 0; $i < count($phone_numbers_array); $i++ ){
                            
                                //vend data
                                //HERE the endpoint of the automation service is called:
                                
                                //this is for megasubplug
                                
                                if($automation_details->slug == 'megasubplug'){
                                    $sell_data = (new MegaSubVendData($phone_numbers_array[$i],$product_plan_id,$validatephonenetwork))->buyData();
                                }
                                // else if($automation_details->slug == 'ogdams' || $automation_details->slug == 'ogdamsv2'){
                                //     $sell_data = (new OgdamsVendData($phone_numbers_array[$i],$product_plan_id))->buyData();
                                // }
                                else{
                                    //this will be like this until other automations are processed
                                    $sell_data['status'] = -1;
                                    $sell_data['user_message'] = 'Data processing failed.';
                                    $sell_data['admin_message'] = 'Data processing failed.';
                                }
                                // logger(json_encode($sell_data_megasub));
                                // dd($sell_data_megasub);

                                if($sell_data['status'] == 1){
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
                                    $user_message = 'Failed due to insufficient balance';
                                    $admin_message = 'Failed due to insufficient balance';
                                    $failure++;
                                    $display_results[$i] = array(
                                        'message' => $user_message,
                                        'admin_message' => $admin_message,
                                        'status' => $status
                                    );
                                }
                        
                                $description = 'Purchase of data';
                                $creationData['transaction_category'] = 'data';
                                $creationData['user_id'] = $user_id;
                                $creationData['wallet_category'] = $wallet_category;
                                $creationData['product_plan_id'] = $product_plan_id;
                                $creationData['phone_number'] = $phone_numbers_array[$i];
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
                    
                            if($failure > 0){
                              return ['status'=>2, 'user_message' => $user_message,'admin_message' => $admin_message,'message'=>" $failure issue(s) found. Check transaction history", 'data' => $display_results];   
                            }
                            return ['status'=>1, 'message'=>'Transaction was successfully processed', 'data' => $display_results ];
                    
                        } 

                        if($wallet_category == 'data_wallet'){
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
                            $automation_details = Automation::where('id',$automation_id)->first();
                    
                            //TODO: candidate for separation
                            for($i = 0; $i < count($phone_numbers_array); $i++ ){
                            
                                //vend data
                                //HERE the endpoint of the automation service is called
                                if($automation_details->slug == 'megasubplug'){
                                    $sell_data = (new MegaSubVendData($phone_numbers_array[$i],$product_plan_id,$validatephonenetwork))->buyData();
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
                            return ['status'=>1, 'message'=>'Bulk data transaction was successfully processed', 'data' => $display_results  ];
                        }


        }catch(Exception $exception){
            DB::rollBack();
            logger($exception->getMessage().' on line: '. $exception->getLine());

            return ['status'=>'-1', 'message'=>'Something went wrong... Please try again', 'data'=>[]];
        }

    }

    public function buy_airtime_service($data){
        $network_id = $data['network_id'];
        $product_plan_category_id = $data['product_plan_category_id'];
        
        $pin = $data['pin'];
        $phone_number = $data['phone_number'];
        $product_plan_id = $data['product_plan_id'];
        $amount = $data['amount'];
        $validatephonenetwork = $data['validatephonenetwork'];
        $user_id = $data['user_id'];//this is required
        $wallet_category = 'main_wallet';//this is required


        $data1['days_count'] = [1,7,30];
        $data1['user_id'] = auth()->id();
        $check_purchase_limit =  ProductsService::check_purchase_limit($data1);
        if($check_purchase_limit['status'] == -1){
            return ['status'=>'-1', 'message'=>$check_purchase_limit['message'] ];
        }


        if($amount < 50){
            return ['status'=>'-1', 'message'=>'amount cannot be less than 50','data' => ''];
        }

        $success = 0;
        $failure = 0;
        $status = -1;
        $message = 'Pending';
        $display_results = [];

        $user_details = User::where('id',$user_id)->first();
        if($user_details->pin != $pin){
            return ['status'=>'-1', 'message'=>'Pin mismatch found','data' => ''];
        }

        $user_plan_id = $user_details->user_plan_id;
        $user_id = $user_details->id;
        $user_level = UserPlan::select('plan_level')->where('id',$user_plan_id)->first();
        $plan_level = $user_level->plan_level;

        
        $plan_details = ProductPlan::with('product_plan_category')
        ->where('visibility',1)
        ->where('id',$product_plan_id)->first();
        $automation_id = $plan_details->automation_id;
        $product_plan_category = $plan_details->product_plan_category;
        $actual_amount = abs($amount);

        $user_level_selling = "user_level_".$plan_level."_selling_price";
        $purchase_discount =  $plan_details->$user_level_selling;
        $actual_discount_value = ceil(($purchase_discount/100) * $actual_amount); 
         
        //below forms the new amount to sell to the user
        $amount = $actual_discount_value < 0 || $actual_discount_value > $actual_amount ? $actual_amount : ($actual_amount - $actual_discount_value);
        
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
                return ['status'=>'-1', 'message'=>$validate_phone['message'].' Number is: '.$validated_phone_number  ];
            }
        }

        DB::beginTransaction();
        try{

                        // ////validate wallet
                        if($wallet_category == 'main_wallet'){
                            $wallet_before = $user_details->main_wallet;
                            $total_amount = $phone_numbers_count * $amount;
                            if($total_amount > $wallet_before || $wallet_before < 0){
                                return ['status'=>'-1', 'message'=>'Insufficient wallet balance' ];
                            }
                    
                            //calling the actual vending via the automation:
                            $automation_details = Automation::where('id',$automation_id)->first();            
                            //TODO: candidate for separation
                            for($i = 0; $i < count($phone_numbers_array); $i++ ){
                                sleep(2); //add throttle here

                                $phone_number = $phone_numbers_array[$i];
                                $validate_phone = (new UtilService())->phoneNumberValidation($phone_number);
                                $validated_phone_number = $validate_phone['validated_phone_number'];
                               
                                
                                $wallet_before = User::where('id',$user_id)->first()->main_wallet;
                                $wallet_after = $wallet_before - $amount;

                                
                                //vend data
                                //HERE the endpoint of the automation service is called:
                                //this is for megasubplug
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
                    
                            if($failure > 0){
                              return ['status'=>2, 'message'=>" $failure issue(s) found. Check transaction history", 'data' => $transaction  ];   
                            }
                            return ['status'=>1, 'message'=>'Transaction was successfully processed', 'data' => $transaction  ];
                    
                        } else{
                            return ['status'=>'-1', 'message'=>'Wrong wallet selection', 'data'=>[]];
                        }



        }catch(Exception $exception){
            logger($exception->getMessage().' on line: '. $exception->getLine());
            DB::rollBack();
            return ['status'=>'-1', 'message'=>'Something went wrong... Please try again', 'data'=>[]];
        }

      
       
















       

    
        // if($amount < 50){
        //     return ['status'=>'-1', 'message'=>'amount cannot be less than 50','data' => []  ];
        // }

        // $data1['days_count'] = [1,7,30];
        // $data1['user_id'] = $user_id;
        // $data1['product'] = 'airtime';
        // $check_purchase_limit =  ProductsService::check_purchase_limit($data1);
        // if($check_purchase_limit['status'] == -1){
        //     return ['status'=>'-1', 'message'=>$check_purchase_limit['message']  ];
        // }

      

        // $success = 0;
        // $failure = 0;
        // $status = 0;
        // $message = 'Pending';
        // $display_results = [];

        // $user_details = User::where('id',$user_id)->first();
        // if(! $user_details){
        //     //end session and redirect to login
        //     return ['status'=>'-1', 'message'=>'User records not found' ];
        // }
        // $user_plan_id = $user_details->user_plan_id;
        // $user_id = $user_details->id;
        // $user_level = UserPlan::select('plan_level')->where('id',$user_plan_id)->first();
        // $plan_level = $user_level->plan_level;

        
        // $plan_details = ProductPlan::where('visibility',1)
        //                             ->where('product_plan_category_id',$product_plan_category_id)
        //                             ->first();
        
        // if(! $plan_details){
        //     return ['status'=>'-1', 'message'=>'plan details not found or available','data' => []  ];
        // }

        // $product_plan_id = $plan_details->id;
        
        // $automation_id = $plan_details->automation_id;
        // // $product_plan_category = $plan_details->product_plan_category;
        // $actual_amount = abs($amount);

        // $user_level_selling = "user_level_".$plan_level."_selling_price";
        // $purchase_discount =  $plan_details->$user_level_selling;
        // $actual_discount_value = ceil(($purchase_discount/100) * $actual_amount); 
         
        // //below forms the new amount to sell to the user
        // $amount = $actual_discount_value < 0 || $actual_discount_value > $actual_amount ? $actual_amount : ($actual_amount - $actual_discount_value);
        

        // // if($user_details->pin != $pin){
        // //     //end session and redirect to login 
        // //     return ['status'=>'-1', 'message'=>'Incorrect PIN' ];
        // // }

        // $user_id = $user_details->id;
        // $phone_numbers = $phone_number;
        // $phone_numbers = trim($phone_numbers);
        // $phone_numbers_array = explode(',',$phone_numbers);
        // $phone_numbers_count = count($phone_numbers_array);

        // if($phone_numbers_count == 1){
        //     $phone_number = $phone_numbers;
        //     $validate_phone = (new UtilService())->phoneNumberValidation($phone_number);
        //     $validated_phone_number = $validate_phone['validated_phone_number'];
        //     if($validate_phone['status'] != 1){
        //         return ['status'=>'-1', 'message'=>$validate_phone['message'].' Number is: '.$validated_phone_number  ];
        //     }
        // }

        // DB::beginTransaction();
        // try{

        //       ////validate wallet
        //                 if($wallet_category == 'main_wallet'){
        //                     $wallet_before = $user_details->main_wallet;
        //                     $total_amount = $phone_numbers_count * $amount;
        //                     if($total_amount > $wallet_before || $wallet_before < 0){
        //                         return ['status'=>'-1', 'message'=>'Insufficient wallet balance' ];
        //                     }
                    
        //                     //calling the actual vending via the automation:
        //                     $automation_details = Automation::where('id',$automation_id)->first();            
        //                     //TODO: candidate for separation
        //                     for($i = 0; $i < count($phone_numbers_array); $i++ ){
        //                         sleep(2); //add throttle here

        //                         $phone_number = $phone_numbers_array[$i];
        //                         $validate_phone = (new UtilService())->phoneNumberValidation($phone_number);
        //                         $validated_phone_number = $validate_phone['validated_phone_number'];
                                
        //                         //vend data
        //                         //HERE the endpoint of the automation service is called:
        //                         //this is for megasubplug
                                

        //                         if($validate_phone['status'] != 1){
        //                             //something when wrong
        //                             $sell_data['status'] = -1;
        //                             $sell_data['user_message'] = 'This number is not a valid number: '.$phone_number;
        //                             $sell_data['admin_message'] = 'This number is not a valid number: '.$phone_number;
        //                         }

        //                         //always check the wallet balance after every loop:
        //                         else if($wallet_before < 0){
        //                              //this will be like this until other automations are processed
        //                              $buy_airtime['status'] = -1;
        //                              $buy_airtime['user_message'] = 'Airtime transaction failed.';
        //                              $buy_airtime['admin_message'] = 'Airtime transaction failed...';
        //                             // return response()->json(['status'=>'-1', 'message'=>'Insufficient wallet balance' ]);
        //                         }else{
        //                             //vend data
        //                             //HERE the endpoint of the automation service is called:
        //                             //this is for megasubplug: vend for Airtime

        //                             if($automation_details->slug == 'megasubplug'){
        //                               $buy_airtime = (new MegaSubVendAirtime($phone_numbers_array[$i],$product_plan_id,$actual_amount,$validatephonenetwork))->buyAirtime();
        //                              // logger($buy_airtime);
        //                             }else{
        //                                 //this will be like this until other automations are processed
        //                                 $buy_airtime['status'] = -1;
        //                                 $buy_airtime['user_message'] = 'Airtime transaction failed.';
        //                                 $buy_airtime['admin_message'] = 'Airtime transaction failed...';
        //                             }
        //                             // logger(json_encode($buy_airtime_megasub));
        //                             // dd($buy_airtime_megasub);
        //                         }

                               

        //                         if($buy_airtime['status'] == 1){
        //                             $success++;
        //                             $status = 1;
        //                             $wallet_before = User::where('id',$user_id)->first()->main_wallet;
        //                             $wallet_after = $wallet_before - $amount;
        //                         }else{
        //                             //it might be processing or it failed
        //                             $status = -1;
        //                             $failure++;
        //                             $wallet_before = User::where('id',$user_id)->first()->main_wallet;
        //                             $wallet_after = $wallet_before;
        //                         }
        //                         //simulate success

        //                         $user_message = $buy_airtime['user_message'];
        //                         $admin_message = $buy_airtime['admin_message'];
        //                         $display_results[$i] = array(
        //                             'message' => $user_message,
        //                             'admin_message' => $admin_message,
        //                             'status' => $status
        //                         );
                                       
                    
        //                         //this should not run though because it has already been checked
        //                         // if($wallet_after <= 0){
        //                         //     $status = -1;
        //                         //     $user_message = 'Failed due to insufficient balance';
        //                         //     $admin_message = 'Failed due to insufficient balance';
        //                         //     $failure++;
        //                         //     $display_results[$i] = array(
        //                         //         'message' => $user_message,
        //                         //         'admin_message' => $admin_message,
        //                         //         'status' => $status
        //                         //     );
        //                         // }
                        
        //                         $description = 'Purchase of airtime';
        //                         $creationData['transaction_category'] = 'airtime';
        //                         $creationData['user_id'] = $user_id;
        //                         $creationData['wallet_category'] = $wallet_category;
        //                         $creationData['product_plan_id'] = $product_plan_id;
        //                         $creationData['phone_number'] = $phone_numbers_array[$i];
        //                         $creationData['amount'] = $actual_amount;
        //                         $creationData['discounted_amount'] = $amount;
        //                         $creationData['status'] = $status;
        //                         $creationData['balance_before'] = $wallet_before;
        //                         $creationData['balance_after'] = $wallet_after;
        //                         $creationData['description'] = $description;
        //                         $creationData['user_screen_message'] = $user_message;
        //                         $creationData['admin_screen_message'] = $admin_message;
        //                         $transaction =  Transaction::create($creationData);

        //                         $walletLog['user_id'] = $user_id;
        //                         $walletLog['transaction_category'] = 'AIRTIME';
        //                         $walletLog['balance_before'] = $wallet_before;
        //                         $walletLog['balance_after'] = $wallet_after;
        //                         $walletLog['transaction_id'] = $transaction->id;
        //                         $walletLog['action_by'] = $user_id;
        //                         $walletLog['description'] = 'Airtime Purchase from naira wallet';
        //                         $this->log_main_wallet_transactions($walletLog);

        //                         User::where('id',$user_id)->update([
        //                             'main_wallet' => $wallet_after
        //                         ]);
                    
        //                     }

        //                     DB::commit();
                    
        //                     if($failure > 0){
        //                       return ['status'=>2, 'message'=>" $failure issue(s) found. Check transaction history", 'data' => $display_results  ];   
        //                     }
        //                     return ['status'=>1, 'message'=>'Transaction was successfully processed', 'data' => $display_results  ];
                    
        //                 } else{
        //                     return ['status'=>'-1', 'message'=>'Wrong wallet selection', 'data'=>[]];
        //                 }



        // }catch(Exception $exception){
        //     logger($exception->getMessage().' on line: '. $exception->getLine());
        //     DB::rollBack();
        //     return ['status'=>'-1', 'message'=>'Something went wrong... Please try again', 'data'=>[]];
        // }

      


    }

    public function buy_electricity_service($data){
        $metre_number = $data['metre_number'];
        $validation_extra_info = $data['validation_extra_info'];
        $electricity_product_plan_category_id = $data['electricity_product_plan_category_id'];
        $electricity_product_plan_id = $data['electricity_product_plan_id'];
        $no_of_slots = $data['no_of_slots'];
        $amount = $data['amount'];
        $pin = $data['pin'];
        // $pin = $data['pin'];
        $user_id = $data['user_id'];//this is required
        $wallet_category = $data['wallet_category'];//this is required

        /////////////////////TO BE REVAMPED
        if($amount < 0){
            return ['status'=>'-1', 'message'=>'amount cannot be less than 0','data' => ''  ];
        }

        $data1['days_count'] = [1,7,30];
        $data1['user_id'] = $user_id;
        $data1['product'] = 'utility_bills';
        $check_purchase_limit =  ProductsService::check_purchase_limit($data1);
        if($check_purchase_limit['status'] == -1){
            return ['status'=>'-1', 'message'=>$check_purchase_limit['message']  ];
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
            return ['status'=>'-1', 'message'=>'plan details not found' ];
        }

        $user_details = User::where('id',$user_id)->first();
        if(! $user_details){
            return ['status'=>'-1', 'message'=>'User record not found' ];
        }

        if($user_details->pin != $pin){
            return ['status'=>'-1', 'message'=>'Incorrect PIN' ];
        }


        $automation_id = $plan_details->automation_id;
       
        $plan_category_details = ProductPlanCategory::where('id',$electricity_product_plan_category_id)->first();
        if(! $plan_category_details){
            return ['status'=>'-1', 'message'=>'plan category details not found' ];
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
                                return ['status'=>'-1', 'message'=>'Insufficient wallet balance' ];
                            }
                    
                            //calling the actual vending via the automation:
                            $automation_details = Automation::where('id',$automation_id)->first();            
                            //TODO: candidate for separation
                       
                             //TODO: candidate for separation
                             for($i = 1; $i <= $no_of_slots; $i++ ){
                                //vend data
                                //HERE the endpoint of the automation service is called:
                                //this is for megasubplug: vend for Airtime
                                
                                if($automation_details->slug == 'megasubplug'){
                                    $duplication_check = 1;
                                 
                                    $buy_electricity_subscription = (new MegaSubElectricity($metre_number,$electricity_product_plan_id,$total_amount,$validation_extra_info,1,$plan_category_details->product_plan_category_name,$user_details->phone_number, user_id: $user_id))->buyElectricity();
                            
                                }else{
                                    //this will be like this until other automations are processed
                                    $buy_electricity_subscription['status'] = -1;
                                    $buy_electricity_subscription['user_message'] = 'Electricity subscription failed...';
                                    $buy_electricity_subscription['admin_message'] = 'Electricity subscription failed...';
                                }
                               

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
                                $creationData['wallet_category'] = $wallet_category;
                                $creationData['product_plan_id'] = $electricity_product_plan_id;
                                $creationData['phone_number'] =  NULL;
                                $creationData['metre_number'] = $metre_number;
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
                    
                            if($failure > 0){
                              return ['status'=>2,'user_message' => $user_message,'admin_message','message'=>" $failure issue(s) found. Check transaction history", 'data' => $display_results  ];   
                            }
                            return ['status'=>1, 'user_message' => $user_message,'admin_message','message'=>'Transaction was successfully processed', 'data' => $display_results  ];
                    
                        } else{
                            return ['status'=>'-1', 'message'=>'Wrong wallet selection', 'data'=>[]];
                        }



        }catch(Exception $exception){
            logger($exception->getMessage().' on line: '. $exception->getLine());
            DB::rollBack();
            return ['status'=>'-1', 'message'=>'Something went wrong... Please try again', 'data'=>[]];
        }
    }

    public function buy_cable_service($data){
        $smart_card_number = $data['smart_card_number'];
        $validation_customer_name = $data['validation_customer_name'];
        $cable_product_plan_category_id = $data['cable_product_plan_category_id'];
        $cable_product_plan_id = $data['cable_product_plan_id'];
        $pin = $data['pin'];
        $user_id = $data['user_id'];//this is required
        $no_of_slots = $data['no_of_slots'];
        $wallet_category = $data['wallet_category'];

        
        $success = 0;
        $failure = 0;
        $status = 0;
        $message = 'Pending';
        $display_results = [];
       
        $data1['days_count'] = [1,7,30];
        $data1['user_id'] = $user_id;
        // $data1['product'] = 'cable_subscription';
        $check_purchase_limit =  ProductsService::check_purchase_limit($data1);
        if($check_purchase_limit['status'] == -1){
            return ['status'=>'-1', 'message'=>$check_purchase_limit['message'] ];
        }

        $plan_details = ProductPlan::where('id',$cable_product_plan_id)->where('visibility',1)->first();
        if(! $plan_details){
            return ['status'=>'-1', 'message'=>'plan details not found' ];
        }
        $automation_id = $plan_details->automation_id;
        // $data_value_mb = $plan_details->data_size_in_mb ?? 0;

        $plan_category_details = ProductPlanCategory::where('id',$cable_product_plan_category_id)->first();
        if(! $plan_category_details){
            return ['status'=>'-1', 'message'=>'plan category details not found' ];
        }

        $user_details = User::where('id',$user_id)->first();
        if(! $user_details){
            return ['status'=>'-1', 'message'=>'please logout and login again' ];
        }

        if($user_details->pin != $pin){
            return ['status'=>'-1', 'message'=>'Incorrect PIN' ];
        }

        $user_plan_id = $user_details->user_plan_id;
        $user_level = UserPlan::select('plan_level')->where('id',$user_plan_id)->first();
        $plan_level = $user_level->plan_level;
        $user_plan_selling_price = 'user_level_'.$plan_level.'_selling_price';
        $amount = abs($plan_details->$user_plan_selling_price);
     

        DB::beginTransaction();
        try{

              ////validate wallet
                        if($wallet_category == 'main_wallet'){
                            $wallet_before = $user_details->main_wallet;
                            $total_amount =  $no_of_slots * $amount;
                            if($total_amount > $wallet_before || $wallet_before < 0){
                                return ['status'=>'-1', 'message'=>'Insufficient wallet balance' ];
                            }
                    
                            //calling the actual vending via the automation:
                            $automation_details = Automation::where('id',$automation_id)->first();            
                            //TODO: candidate for separation
                       
                             //TODO: candidate for separation
                             for($i = 1; $i <= $no_of_slots; $i++ ){
                                //vend data
                                //HERE the endpoint of the automation service is called:
                                //this is for megasubplug: vend for Airtime
                                
                                if($automation_details->slug == 'megasubplug'){
                                    $duplication_check = 1;
                                    // $smart_card_number,$plan_id,$amount,$validation_customer_name,$no_of_slots,$product_plan_category_name
                                    // return ['status'=>'-1', 'message'=>$smart_card_number ]);

                                    $buy_cable_subscription = (new MegaSubCableTV($smart_card_number,$cable_product_plan_id,$total_amount,$validation_customer_name,1,$plan_category_details->product_plan_category_name, user_id: $user_id))->buyCable();
                                }else{
                                    //this will be like this until other automations are processed
                                    $buy_cable_subscription['status'] = -1;
                                    $buy_cable_subscription['user_message'] = 'Cable subscription failed.';
                                    $buy_cable_subscription['admin_message'] = 'Cable subscription failed.';
                                }
                                // logger(json_encode($buy_cable_subscription_megasub));
                                // dd($buy_cable_subscription_megasub);

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
                    
                            if($failure > 0){
                              return ['status'=>2, 'user_message' => $user_message,'admin_message' => $admin_message,'message'=>" $failure issue(s) found. Check transaction history", 'data' => $display_results  ];   
                            }
                            return ['status'=>1,'user_message' => $user_message,'admin_message' => $admin_message, 'message'=>'Transaction was successfully processed', 'data' => $display_results  ];
                    
                        } else{
                            return ['status'=>'-1','admin_message' => 'Sorry transaction failed', 'message'=>'Wrong wallet selection', 'data'=>[]];
                        }



        }catch(Exception $exception){
            logger($exception->getMessage().' on line: '. $exception->getLine());
            DB::rollBack();
            return ['status'=>'-1', 'message'=>'Something went wrong... Please try again', 'data'=>[]];
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
