<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Network;
use App\Models\Product;
use App\Models\UserPlan;
use App\Models\Automation;
use App\Models\ProductPlan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use App\Models\UserBulkDataWallet;
use Illuminate\Support\Facades\DB;
use App\Models\ProductPlanCategory;
use App\Services\Utils\UtilService;
use App\Models\BulkDataProductPlans;
use App\Models\UserBulkDataPurchase;
use Illuminate\Support\Facades\Mail;
use App\Mail\WalletFundingNotification;
use Illuminate\Support\Facades\Validator;
use App\Services\Automation\MegaSubPlugAutomation\VendData;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubVendData;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubVendAirtime;

class AirtimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function buy_airtime()
    {

        $networks = Network::all();
        $product = Product::where('slug','airtime')->first(); //TODO: have enums that gets the id later
        $data['networks'] = $networks;
        $data['product'] = $product;


        $product_plan_categories = ProductPlanCategory::where('product_id',$product->id)
        ->where('visibility',1)
        ->get(); //TODO: have enums that gets the id later
       
        $data['product_plan_categories'] = $product_plan_categories;

        $user_details = auth()->user();
        $user_id = $user_details->id;
        // dd($user_id);

        //data txns list
        $airtime_transactions = Transaction::with('user')->where('transaction_category','airtime')
        ->where('user_id',$user_id)
        ->latest()
        ->get();
        $data['airtime_transactions'] = $airtime_transactions;
        $data['user_details'] = $user_details;



        // dd($data);
        return view('user.airtime.buy_airtime')->with($data);
    }


        /**
     * Show the form for creating a new resource.
     */
    public function buy_airtime_by_plan_category($id)
    {

        $plan_category = ProductPlanCategory::with('product','network','automation')->where('id',$id)->first();
        // dd($plan_category->id);
        $data['plan_category'] = $plan_category;
        $data['plan_category_idd'] = $id;

        
        $product_plans = ProductPlan::where('automation_id',$plan_category->automation->id)
        ->where('visibility',1)
        ->where('product_plan_category_id',$id)->get();
        
        $amount = 50; //minimum set

        $user_details = auth()->user();
        $user_id = $user_details->id;
        $user_plan_id = $user_details->user_plan_id;

        $product_planss = [];
        $counter =0;

        $user_level = UserPlan::select('plan_level')->where('id',$user_plan_id)->first();
        $plan_level = $user_level->plan_level;
       
        foreach($product_plans as $product_plan){

            $user_level_selling = "user_level_".$plan_level."_selling_price";
        
            $purchase_discount = $product_plan->$user_level_selling;
            $actual_discount_value = ceil(($purchase_discount/100) * $amount);  
            $discounted_selling_price = $amount - abs($actual_discount_value);
            $selling_price = 0; //this is from the system, not applicable for airtime
           
            if($product_plan){
                $counter++;
                $product_planss[$counter]['product_plan_id'] = $product_plan->id;
                $product_planss[$counter]['amount'] = $amount;
                $product_planss[$counter]['selling_price'] = $discounted_selling_price;
                $product_planss[$counter]['product_plan_name'] = $product_plan->product_plan_name;
                $product_planss[$counter]['data_size_in_mb'] = $product_plan->data_size_in_mb;
                $product_planss[$counter]['validity_in_days'] = $product_plan->validity_in_days;    
                $product_planss[$counter]['automation_id'] = $product_plan->automation_id;    
            }
        }

        $product = Product::where('slug','airtime')->first(); //TODO: have enums that gets the id later
        $product_plan_categories = ProductPlanCategory::where('product_id',$product->id)->get(); //TODO: have enums that gets the id later
        $data['product_plan_categories'] = $product_plan_categories;

      
        //data txns list
        $airtime_transactions = Transaction::with('user')->where('transaction_category','airtime')
        ->where('user_id',$user_id)
        ->latest()
        ->get();
        $data['airtime_transactions'] = $airtime_transactions;
        $data['user_details'] = $user_details;
        $data['amount'] = $amount;
        $data['product_plans'] = $product_planss;
        
        // return $data;
        return view('user.airtime.buy_airtime_by_plan_category')->with($data);
    }


    public function fetch_single_airtime_plan(Request $request){
        
        $plan_category = ProductPlanCategory::with('product','network','automation')->where('id',$request->plan_category_id)->first();
        
        $product_plans = ProductPlan::where('automation_id',$plan_category->automation->id)
        ->where('visibility',1)
        ->where('product_plan_category_id',$request->plan_category_id)->get();
        
        $amount = $request->amount; //minimum set

        $user_details = auth()->user();
        $user_id = $user_details->id;
        $user_plan_id = $user_details->user_plan_id;

        $product_planss = [];
        $counter =0;

        $user_level = UserPlan::select('plan_level')->where('id',$user_plan_id)->first();
        $plan_level = $user_level->plan_level;
       
        foreach($product_plans as $product_plan){

            $user_level_selling = "user_level_".$plan_level."_selling_price";
            $purchase_discount = $product_plan->$user_level_selling;
            $actual_discount_value = ceil(($purchase_discount/100) * $amount);  
            $discounted_selling_price = $amount - abs($actual_discount_value);
            $selling_price = 0; //this is from the system, not applicable for airtime
           
            if($product_plan){
                $counter++;
                $product_planss[$counter]['product_plan_id'] = $product_plan->id;
                $product_planss[$counter]['amount'] = $amount;
                $product_planss[$counter]['selling_price'] = $discounted_selling_price;
                $product_planss[$counter]['product_plan_name'] = $product_plan->product_plan_name;
                $product_planss[$counter]['data_size_in_mb'] = $product_plan->data_size_in_mb;
                $product_planss[$counter]['validity_in_days'] = $product_plan->validity_in_days;    
                $product_planss[$counter]['automation_id'] = $product_plan->automation_id;    
            }
        }

        //data txns list
        // $data['product_plans'] = $product_planss;
        return response()->json(['status'=>'1','user_level'=>$plan_level ,'message'=>'Product plans fetched','counter' =>count($product_planss),'data' => $product_planss ]);
       
    }

    public function process_pending_airtime_transactions(Request $request){
            $pending_transactions = Transaction::with('user')->where('admin_screen_message','pending_airtime_transaction')
                                    ->where('transaction_category','airtime')
                                    ->where('status',0) 
                                    ->get();

            if(count($pending_transactions) > 0){
                foreach($pending_transactions as $pending_transaction){
                    $user_balance = $pending_transaction->user->main_wallet;
                    $email = $pending_transaction->user->email;
                    $user_id = $pending_transaction->user_id;
                    $created_at = $pending_transaction->created_at;
                    $phone_number = $pending_transaction->phone_number;
                    $product_plan_id = $pending_transaction->product_plan_id;
                    $amount = $pending_transaction->amount;
                    $discounted_amount = $pending_transaction->discounted_amount;
                    
                    $fetch_duplicate_timestamp = Transaction::where('user_id',$user_id)->where('created_at',$created_at)->count();
                   
                    if($fetch_duplicate_timestamp > 1){
                        User::where('id',$user_id)->update([
                            'email' => $email."_fraud_".rand(111111,999999)
                        ]);
                        Transaction::where('user_id',$user_id)
                                    ->where('created_at',$created_at)
                                    ->update(['status' => -1]);
                        logger('User with email: '.$email.' BLOCKED... Transactions with same timestamps detected for txn: '. $pending_transaction->id);
                                    
                    } 

                    else if($user_balance < 0){
                        User::where('id',$user_id)->update([
                           'email' => $email."_likely_fraud_".rand(111111,999999)
                        ]);
                        $pending_transaction->update(['status' => -1]);
                        logger('User with email: '.$email.' BLOCKED... User has a negative balance for txn: '. $pending_transaction->id);

                   } 
                   
                   else{
                        //carry out the transaction flow now
                        $plan_details = ProductPlan::where('id',$product_plan_id)->first();
                        $automation_id = $plan_details->automation_id ?? NULL;
                        $automation_details = Automation::where('id',$automation_id)->first();            
                        
                        if($plan_details == NULL || $automation_id == NULL || $automation_details == NULL){
                            logger('This should never run actually... something is wrong with plan and or automation setting on txn: '. $pending_transaction->id);
                        }
                        else if($automation_details->slug == 'megasubplug'){
                            $buy_airtime = (new MegaSubVendAirtime($phone_number,$product_plan_id,$amount,0))->buyAirtime();
                            if($buy_airtime['status'] == 1){
                                 //this will be like this until other automations are processed
                                 $user_message = $buy_airtime['user_message'];
                                 $admin_message = $buy_airtime['admin_message'];
                               
                                 //update to refunded here for now
                                 Transaction::where('id',$pending_transaction->id)->update([
                                     'status' => 1,
                                     'user_screen_message' => $user_message,
                                     'admin_screen_message' => $admin_message,
                                 ]);

                                 logger('Transaction successfully processed for txn: '. $pending_transaction->id);
                            
                            }else{
                                //Transaction failed
                                $user_message = $buy_airtime['user_message'];
                                $admin_message = $buy_airtime['admin_message'];
                                $new_amount = $user_balance + $discounted_amount;
                                
                                //transaction failed... return the users amount
                                User::where('id',$user_id)->update([
                                    'main_wallet' => $new_amount
                                ]);

                                //update to refunded here for now
                                Transaction::where('id',$pending_transaction->id)->update([
                                    'status' => -1,
                                    'user_screen_message' => $user_message,
                                    'admin_screen_message' => $admin_message,
                                ]);

                                logger('Transaction FAILED for txn: '. $pending_transaction->id);

                            }                                      
                        }else{
                              //this will be like this until other automations are processed
                              $user_message = 'Airtime transaction refunded.';
                              $admin_message = 'Airtime transaction refunded... Automation not yet implemented';
                              $new_amount = $user_balance + $discounted_amount;
                              
                              //refund the users amount
                              User::where('id',$user_id)->update([
                                'main_wallet' => $new_amount
                              ]);

                              //update to refunded here for now
                              Transaction::where('id',$pending_transaction->id)->update([
                                'status' => 2,
                                'user_screen_message' => $user_message,
                                'admin_screen_message' => $admin_message,
                              ]);

                              logger('Transaction REFUNDED because the automation has not been implemented for txn: '. $pending_transaction->id);

                        }

                    }

                }
            }else{
                echo 'No pending airtime transactions at the moment';
                logger('No pending airtime transactions at the moment');
            }
    }

    


    /**
     * Store a newly created resource in storage.
     */
    public function buy_airtime_action(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network_id' => 'required',
            'phone_number' => 'required',
            'product_plan_category_id' => 'nullable',
            'product_plan_id' => 'required',
            'pin' => ['required','digits:4'],
            'amount' => 'required|numeric|gt:0',
            'validatephonenetwork'=>['required',Rule::in([0,1])],
        ]);

        
        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json(['status'=>'-1', 'message'=>$validator->errors()->first(),'data' => $request->all() ]);
        }


        if($request->amount < 50){
            return response()->json(['status'=>'-1', 'message'=>'amount cannot be less than 50','data' => ''  ]);
        }

        $success = 0;
        $failure = 0;
        $status = -1;
        $message = 'Pending';
        $display_results = [];

        $user_details = auth()->user();
        $user_plan_id = $user_details->user_plan_id;
        $user_id = $user_details->id;
        $user_level = UserPlan::select('plan_level')->where('id',$user_plan_id)->first();
        $plan_level = $user_level->plan_level;

        
        $plan_details = ProductPlan::with('product_plan_category')
        ->where('visibility',1)
        ->where('id',$request->product_plan_id)->first();
        $automation_id = $plan_details->automation_id;
        $product_plan_category = $plan_details->product_plan_category;
        $actual_amount = abs($request->amount);

        $user_level_selling = "user_level_".$plan_level."_selling_price";
        $purchase_discount =  $plan_details->$user_level_selling;
        $actual_discount_value = ceil(($purchase_discount/100) * $actual_amount); 
         
        //below forms the new amount to sell to the user
        $amount = $actual_discount_value < 0 || $actual_discount_value > $actual_amount ? $actual_amount : ($actual_amount - $actual_discount_value);
        
        
        if(! $user_details){
            //end session and redirect to login
    
            redirect(url('/login'));
            return response()->json(['status'=>'-1', 'message'=>'please logout and login again' ]);
        }


        if($user_details->pin != $request->pin){
            //end session and redirect to login
           
            return response()->json(['status'=>'-1', 'message'=>'Incorrect PIN' ]);
        }

        $user_id = $user_details->id;
        $phone_numbers = $request->phone_number;
        $phone_numbers = trim($phone_numbers);
        $phone_numbers_array = explode(',',$phone_numbers);
        $phone_numbers_count = count($phone_numbers_array);

        if($phone_numbers_count == 1){
            $phone_number = $phone_numbers;
            $validate_phone = (new UtilService())->phoneNumberValidation($phone_number);
            $validated_phone_number = $validate_phone['validated_phone_number'];
            if($validate_phone['status'] != 1){
                return response()->json(['status'=>'-1', 'message'=>$validate_phone['message'].' Number is: '.$validated_phone_number  ]);
            }
        }

        DB::beginTransaction();
        try{

                        // ////validate wallet
                        if($request->wallet_category == 'main_wallet'){
                            $wallet_before = $user_details->main_wallet;
                            $total_amount = $phone_numbers_count * $amount;
                            if($total_amount > $wallet_before || $wallet_before < 0){
                                return response()->json(['status'=>'-1', 'message'=>'Insufficient wallet balance' ]);
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
                                $creationData['wallet_category'] = $request->wallet_category;
                                $creationData['product_plan_id'] = $request->product_plan_id;
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
                                    $walletLog['action_by'] = auth()->user()->id;
                                    $walletLog['description'] = 'Airtime Purchase from main wallet';
                                    $this->log_wallet_transactions($walletLog);  
                                    
                                    User::where('id',$user_id)->update([
                                        'main_wallet' => $wallet_after
                                    ]);
                                }  
                            }

                            DB::commit();
                    
                            if($failure > 0){
                              return response()->json(['status'=>2, 'message'=>" $failure issue(s) found. Check transaction history", 'data' => $display_results  ]);   
                            }
                            return response()->json(['status'=>1, 'message'=>'Transaction was successfully processed', 'data' => $display_results  ]);
                    
                        } else{
                            return response()->json(['status'=>'-1', 'message'=>'Wrong wallet selection', 'data'=>[]]);
                        }



        }catch(Exception $exception){
            logger($exception->getMessage().' on line: '. $exception->getLine());
            DB::rollBack();
            return response()->json(['status'=>'-1', 'message'=>'Something went wrong... Please try again', 'data'=>[]]);
        }

      
       
        

    }


    /**
     * Store a newly created resource in storage.
     */
    public function buy_airtime_actionBACKUP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network_id' => 'required',
            'phone_number' => 'required',
            'product_plan_category_id' => 'nullable',
            'product_plan_id' => 'required',
            'pin' => ['required','digits:4'],
            'amount' => 'required|numeric|gt:0',
            'validatephonenetwork'=>['required',Rule::in([0,1])],
        ]);

        
        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json(['status'=>'-1', 'message'=>$validator->errors()->first(),'data' => $request->all() ]);
        }


        if($request->amount < 50){
            return response()->json(['status'=>'-1', 'message'=>'amount cannot be less than 50','data' => ''  ]);
        }

        $success = 0;
        $failure = 0;
        $status = 0;
        $message = 'Pending';
        $display_results = [];

        $user_details = auth()->user();
        $user_plan_id = $user_details->user_plan_id;
        $user_id = $user_details->id;
        $user_level = UserPlan::select('plan_level')->where('id',$user_plan_id)->first();
        $plan_level = $user_level->plan_level;

        
        $plan_details = ProductPlan::with('product_plan_category')
        ->where('visibility',1)
        ->where('id',$request->product_plan_id)->first();
        $automation_id = $plan_details->automation_id;
        $product_plan_category = $plan_details->product_plan_category;
        $actual_amount = abs($request->amount);

        $user_level_selling = "user_level_".$plan_level."_selling_price";
        $purchase_discount =  $plan_details->$user_level_selling;
        $actual_discount_value = ceil(($purchase_discount/100) * $actual_amount); 
         
        //below forms the new amount to sell to the user
        $amount = $actual_discount_value < 0 || $actual_discount_value > $actual_amount ? $actual_amount : ($actual_amount - $actual_discount_value);
        
        
        if(! $user_details){
            //end session and redirect to login
    
            redirect(url('/login'));
            return response()->json(['status'=>'-1', 'message'=>'please logout and login again' ]);
        }


        if($user_details->pin != $request->pin){
            //end session and redirect to login
           
            return response()->json(['status'=>'-1', 'message'=>'Incorrect PIN' ]);
        }

        $user_id = $user_details->id;
        $phone_numbers = $request->phone_number;
        $phone_numbers = trim($phone_numbers);
        $phone_numbers_array = explode(',',$phone_numbers);
        $phone_numbers_count = count($phone_numbers_array);

        if($phone_numbers_count == 1){
            $phone_number = $phone_numbers;
            $validate_phone = (new UtilService())->phoneNumberValidation($phone_number);
            $validated_phone_number = $validate_phone['validated_phone_number'];
            if($validate_phone['status'] != 1){
                return response()->json(['status'=>'-1', 'message'=>$validate_phone['message'].' Number is: '.$validated_phone_number  ]);
            }
        }

        DB::beginTransaction();
        try{
                   
                        // ////validate wallet
                        if($request->wallet_category == 'main_wallet'){
                            $wallet_before = $user_details->main_wallet;
                            $total_amount = $phone_numbers_count * $amount;
                            if($total_amount > $wallet_before || $wallet_before < 0){
                                return response()->json(['status'=>'-1', 'message'=>'Insufficient wallet balance' ]);
                            }
                    
                            //calling the actual vending via the automation:
                            $automation_details = Automation::where('id',$automation_id)->first();            
                            //TODO: candidate for separation
                            for($i = 0; $i < count($phone_numbers_array); $i++ ){
                                sleep(2); //add throttle here

                                $phone_number = $phone_numbers_array[$i];
                                $validate_phone = (new UtilService())->phoneNumberValidation($phone_number);
                                $validated_phone_number = $validate_phone['validated_phone_number'];
                                
                                //vend data
                                //HERE the endpoint of the automation service is called:
                                //this is for megasubplug
                                

                                if($validate_phone['status'] != 1){
                                    //something when wrong
                                    $sell_data['status'] = -1;
                                    $sell_data['user_message'] = 'This number is not a valid number: '.$phone_number;
                                    $sell_data['admin_message'] = 'This number is not a valid number: '.$phone_number;
                                }

                                //always check the wallet balance after every loop:
                                else if($wallet_before < 0){
                                     //this will be like this until other automations are processed
                                     $buy_airtime['status'] = -1;
                                     $buy_airtime['user_message'] = 'Airtime transaction failed.';
                                     $buy_airtime['admin_message'] = 'Airtime transaction failed...';
                                    // return response()->json(['status'=>'-1', 'message'=>'Insufficient wallet balance' ]);
                                }else{
                                    //vend data
                                    //HERE the endpoint of the automation service is called:
                                    //this is for megasubplug: vend for Airtime

                                    if($automation_details->slug == 'megasubplug'){
                                      $buy_airtime = (new MegaSubVendAirtime($phone_numbers_array[$i],$request->product_plan_id,$actual_amount,$request->validatephonenetwork))->buyAirtime();
                                     // logger($buy_airtime);
                                    }else{
                                        //this will be like this until other automations are processed
                                        $buy_airtime['status'] = -1;
                                        $buy_airtime['user_message'] = 'Airtime transaction failed.';
                                        $buy_airtime['admin_message'] = 'Airtime transaction failed...';
                                    }
                                    // logger(json_encode($buy_airtime_megasub));
                                    // dd($buy_airtime_megasub);
                                }

                               

                                if($buy_airtime['status'] == 1){
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
                                $user_message = $buy_airtime['user_message'];
                                $admin_message = $buy_airtime['admin_message'];
                                $display_results[$i] = array(
                                    'message' => $user_message,
                                    'admin_message' => $admin_message,
                                    'status' => $status
                                );
                                       
                    
                                //this should not run though because it has already been checked
                                // if($wallet_after <= 0){
                                //     $status = -1;
                                //     $user_message = 'Failed due to insufficient balance';
                                //     $admin_message = 'Failed due to insufficient balance';
                                //     $failure++;
                                //     $display_results[$i] = array(
                                //         'message' => $user_message,
                                //         'admin_message' => $admin_message,
                                //         'status' => $status
                                //     );
                                // }
                        
                                $description = 'Purchase of airtime';
                                $creationData['transaction_category'] = 'airtime';
                                $creationData['user_id'] = $user_id;
                                $creationData['wallet_category'] = $request->wallet_category;
                                $creationData['product_plan_id'] = $request->product_plan_id;
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

                                $walletLog['user_id'] = $user_id;
                                $walletLog['transaction_category'] = 'AIRTIME';
                                $walletLog['balance_before'] = $wallet_before;
                                $walletLog['balance_after'] = $wallet_after;
                                $walletLog['transaction_id'] = $transaction->id;
                                $walletLog['action_by'] = auth()->user()->id;
                                $walletLog['description'] = 'Airtime Purchase from main wallet';
                                $this->log_wallet_transactions($walletLog);
                    
                                User::where('id',$user_id)->update([
                                    'main_wallet' => $wallet_after
                                ]);
                    
                            }

                            DB::commit();
                    
                            if($failure > 0){
                              return response()->json(['status'=>2, 'message'=>" $failure issue(s) found. Check transaction history", 'data' => $display_results  ]);   
                            }
                            return response()->json(['status'=>1, 'message'=>'Transaction was successfully processed', 'data' => $display_results  ]);
                    
                        } else{
                            return response()->json(['status'=>'-1', 'message'=>'Wrong wallet selection', 'data'=>[]]);
                        }



        }catch(Exception $exception){
            logger($exception->getMessage().' on line: '. $exception->getLine());
            DB::rollBack();
            return response()->json(['status'=>'-1', 'message'=>'Something went wrong... Please try again', 'data'=>[]]);
        }
    }



    //TODO: move to a separate class
    public function validateUserWallet($user_id,$wallet_before,$phone_numbers_count,$amount){    
        return true;
    }


    /**
     * Get all the products plans categories.
     */
    public function fetch_product_plan_categories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network_id' => 'required',
        ]);
          
        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json(['status'=>'-1', 'message'=>'network is required','data' => $request->all() ]);
        }

        $network = $request->network_id;
        $product_id = Product::where('slug','airtime')->first()->id;
        $product_plans_categories = ProductPlanCategory::whereIn('product_id',$product_id)->where('visibility',1)->where('network_id',$network)->get();
        
        return response()->json(['status'=>'1', 'message'=>'Product plans categories fetched','data' => $product_plans_categories ]);

    }


  
    public function fetch_airtime_transactions(Request $request){

        
        $date_from = $request->date_from ?? date('Y-m-d', strtotime('-10 days'));
        $date_to= $request->date_to ?? date('Y-m-d');

        $product_plan_category_filter = $request->product_plan_category_filter ?? '';
        
        $phone = $request->phone_recharged ?? '';
        
        $limit = $request->limit ?? 2000;

       
        $data = Transaction::when(!empty($date_from) && !empty($date_to) , function ($query) use ($date_from,$date_to){
            $date_to = date('Y-m-d', strtotime('+1 day', strtotime($date_to)));
            $query->where('created_at','>=',$date_from)->where('created_at','<=',$date_to);
        })->when(!empty($product_plan_category_filter) , function ($query) use ($product_plan_category_filter){
            $product_plan_ids = ProductPlan::where('product_plan_category_id',$product_plan_category_filter)->pluck('id');
            $query->whereIn('product_plan_id',$product_plan_ids);
        })->when(!empty($phone) , function ($query) use ($phone){
          $query->where('phone_number',$phone);
        })
        ->where('transaction_category','airtime')
        ->where('user_id',auth()->id())
        ->with(['user','product_plan'])->latest()->limit($limit)->get();
        // return $data;


      return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('DT_RowIndex',function($data){
        return $data->id;
        })
        // ->addColumn('user_id',function($data){
        //     $first_name = $data->user->first_name  ?? 'nil';
        //     $last_name = $data->user->last_name  ?? 'nil';
        //     $phone_number = $data->user->phone_number  ?? 'nil';
        //     $user_details =  $first_name.'<br>'.$last_name.'<br>'.$phone_number.'<br>';     
        //     return $user_details;
        // })
        ->addColumn('wallet_category',function($data){
            $wallet_category = $data->wallet_category == 'main_wallet' ?  'MAIN' : 'DATA_WALLET';
            return $wallet_category;
        })
        ->addColumn('plan_details',function($data){
            if($data->product_plan != NULL){
               
                $dataa =  $data->product_plan->product_plan_name.'<br>';
                $dataa .=  $data->product_plan->product_plan_category->product_plan_category_name.'<br>';
                if($data->transaction_category == 'cable_subscription'){
                    $dataa .=  'Smart Card No: '.$data->smart_card_number.'<br>';
                }
                if($data->transaction_category == 'utility_bills'){
                    $dataa .=  'Metre No: '.$data->metre_number.'o<br>';
                }
                if($data->transaction_category == 'data'){
                    $dataa .= number_format($data->product_plan->data_size_in_mb ?? '0') .' MB';
                }

            }else{
                $dataa = 'NIL';
            }
            return $dataa;
        })
     
        ->addColumn('transaction_category',function($data){
            $transaction_category = $data->transaction_category;
            return $transaction_category;
        })
        // ->addColumn('response',function($data){
        //     return  '<span style="white-space: normal;word-wrap: break-word;word-break: normal;width:auto">'.$data->user_screen_message.'</span>';
        //     // return $user_screen_message;
        // })
        ->addColumn('phone_number',function($data){
            return $data->phone_number;
        }) 
       ->addColumn('amount',function($data){
        return '&#8358;'.(number_format($data->amount,2));
        }) 
        ->addColumn('discounted_amount',function($data){
            return '&#8358;'.(number_format($data->discounted_amount,2));
        }) 
        ->addColumn('balance_before',function($data){
            return $data->wallet_category == 'main_wallet' ? '₦'.number_format($data->balance_before,2) : number_format($data->balance_before).'MB';

        })  
        ->addColumn('data_size',function($data){
         $data_size = number_format($data->product_plan->data_size_in_mb ?? '0') .' MB';
         return $data_size;
        })  
        ->addColumn('balance_after',function($data){
        return $data->wallet_category == 'main_wallet' ? '₦'.number_format($data->balance_after,2) : number_format($data->balance_after).'MB';
        })  
        ->addColumn('status',function($data){
            if($data->status == 1){
                $status_display = '<span class="badge bg-success text-white">Success</span>';
            }
            elseif($data->status == -1){
                $status_display = '<span class="badge bg-danger text-white">Failed</span>';
            }
            elseif($data->status == 0){
                $status_display = '<span class="badge bg-warning text-white">Pending</span>';
            }
            elseif($data->status == 2){
                $status_display = '<span class="badge bg-primary text-white">Refunded</span>';
            }
            elseif($data->status == 3){
                $status_display = '<span class="badge bg-gray text-white">Processing</span>';
            }else{
                $status_display = '<span class="badge bg-gray text-white">Unknown</span>';
            }
           return $status_display;  
        }) 
        ->addColumn('created_at',function($data){
            return $data->created_at;
        }) 
        ->addColumn('action',function($data){
            $route = route('transactions.transaction_details',$data->id);
            $actionBtn = '<a href="'.$route.'" type="button" class="hs-dropdown-toggle ti-btn ti-btn-primary" data-hs-overlay="#hs-vertically-centered-scrollable-modal'.$data->email.'">
            Details
            </a>';
            return $actionBtn;
        })
        
        ->escapeColumns([])
        ->make(true);


       
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
