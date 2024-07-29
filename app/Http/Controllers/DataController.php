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
use App\Models\UserBulkDataWallet;
use Illuminate\Support\Facades\DB;
use App\Models\ProductPlanCategory;
use App\Models\BulkDataProductPlans;
use App\Models\UserBulkDataPurchase;
use Illuminate\Support\Facades\Validator;
use App\Services\Automation\MegaSubPlugAutomation\VendData;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubVendData;

class DataController extends Controller
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
    public function buy_data()
    {

        $networks = Network::all();
        $product = Product::where('slug','data')->first(); //TODO: have enums that gets the id later
        $data['networks'] = $networks;
        $data['product'] = $product;

        $user_details = auth()->user();
        $user_id = $user_details->id;
        // dd($user_id);

        //data txns list
        $data_transactions = Transaction::with('user')->where('transaction_category','data')
        ->where('user_id',$user_id)
        ->latest()
        ->get();
        $data['data_transactions'] = $data_transactions;
        $data['user_details'] = $user_details;

        // dd($data);
        return view('user.data.buy_data')->with($data);
    }


     /**
     * Show the form for creating a new resource.
     */
    public function buy_bulk_data()
    {
        $bulk_data_wallets = UserBulkDataWallet::with('product_plan_category')->where('user_id',auth()->id())->get();
        $user_bulk_data_purchases = UserBulkDataPurchase::with('product_plan_category')->where('user_id',auth()->id())->latest()->paginate(50);
        
        $data['bulk_data_wallets'] = $bulk_data_wallets;
        $data['user_bulk_data_purchases'] = $user_bulk_data_purchases;

        // dd($data);
        return view('user.bulk_data.buy_bulk_data')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function buy_data_action(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network_id' => 'required',
            'phone_number' => 'required',
            'product_plan_category_id' => 'required',
            'product_plan_id' => 'required',
            'pin' => ['required','digits:4'],
            'wallet_category'=>['required',Rule::in(['main_wallet','data_wallet'])],
        ]);
        
        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json(['status'=>'-1', 'message'=>$validator->errors()->first(),'data' => $request->all() ]);
        }

        $success = 0;
        $failure = 0;
        $status = 0;
        $message = 'Pending';
        $display_results = [];

        $plan_details = ProductPlan::where('id',$request->product_plan_id)->first();
        $automation_id = $plan_details->automation_id;
        $data_value_mb = $plan_details->data_size_in_mb ?? 0;

        $user_plan_id = auth()->user()->user_plan_id;
        $user_level = UserPlan::select('plan_level')->where('id',$user_plan_id)->first();
        $plan_level = $user_level->plan_level;
        $user_plan_selling_price = 'user_level_'.$plan_level.'_selling_price';
        $amount = abs($plan_details->$user_plan_selling_price);
        $user_details = auth()->user();
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

        DB::beginTransaction();
        try{

              ////validate wallet
                        if($request->wallet_category == 'main_wallet'){
                            $wallet_before = $user_details->main_wallet;
                            $total_amount = $phone_numbers_count * $amount;
                            if($total_amount > $wallet_before){
                                return response()->json(['status'=>'-1', 'message'=>'Insufficient wallet balance' ]);
                            }
                    
                            //calling the actual vending via the automation:
                            $automation_details = Automation::where('id',$automation_id)->first();
                    
                            //TODO: candidate for separation
                            for($i = 0; $i < count($phone_numbers_array); $i++ ){
                            
                                //vend data
                                //HERE the endpoint of the automation service is called:
                                
                                //this is for megasubplug
                                
                                if($automation_details->slug == 'megasubplug'){
                                    $sell_data = (new MegaSubVendData($phone_numbers_array[$i],$request->product_plan_id))->buyData();
                                }else{
                                    //this will be like this until other automations are processed
                                    $sell_data['status'] = 1;
                                    $sell_data['user_message'] = 'Data was successfully processed.';
                                    $sell_data['admin_message'] = 'Data was successfully processed.';
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
                                $creationData['wallet_category'] = $request->wallet_category;
                                $creationData['product_plan_id'] = $request->product_plan_id;
                                $creationData['phone_number'] = $phone_numbers_array[$i];
                                $creationData['amount'] = $amount;
                                $creationData['status'] = $status;
                                $creationData['balance_before'] = $wallet_before;
                                $creationData['balance_after'] = $wallet_after;
                                $creationData['description'] = $description;
                                $creationData['user_screen_message'] = $user_message;
                                $creationData['admin_screen_message'] = $admin_message;
                                Transaction::create($creationData);
                    
                                User::where('id',$user_id)->update([
                                    'main_wallet' => $wallet_after
                                ]);
                    
                            }

                            DB::commit();
                    
                            if($failure > 0){
                              return response()->json(['status'=>2, 'message'=>" $failure issue(s) found. Check transaction history", 'data' => $display_results  ]);   
                            }
                            return response()->json(['status'=>1, 'message'=>'Transaction was successfully processed', 'data' => $display_results  ]);
                    
                        } 

                        if($request->wallet_category == 'data_wallet'){
                            $get_bulk_data_wallet_details = UserBulkDataWallet::where('user_id',$user_id)->where('product_plan_category_id',$request->product_plan_category_id)->first();
                            
                            if(! $get_bulk_data_wallet_details ){
                                $bulk_wallet_balance_before = 0;
                            }
                            $bulk_wallet_balance_before = $get_bulk_data_wallet_details->bulk_wallet_balance_mb;

                            $total_value_to_buy_in_mb = $phone_numbers_count * $data_value_mb;
                            if($total_value_to_buy_in_mb > $bulk_wallet_balance_before){
                                return response()->json(['status'=>'-1', 'message'=>'Insufficient data in wallet balance' ]);
                            }
                    
                            //calling the actual vending via the automation:
                            $automation_details = Automation::where('id',$automation_id)->first();
                    
                            //TODO: candidate for separation
                            for($i = 0; $i < count($phone_numbers_array); $i++ ){
                            
                                //vend data
                                //HERE the endpoint of the automation service is called
                                //simulate success
                                $success++;
                                $message = 'Successfully processed via bulk data wallet';
                                $status = 1;
                                $display_results[$i] = array(
                                    'message' => $message,
                                    'status' => $status
                                );
                                $get_bulk_data_wallet_details = UserBulkDataWallet::where('user_id',$user_id)->where('product_plan_category_id',$request->product_plan_category_id)->first();
                                $bulk_wallet_balance_before = $get_bulk_data_wallet_details->bulk_wallet_balance_mb;
                               
                                $bulk_wallet_balance_after = $bulk_wallet_balance_before - $data_value_mb;
                    
                    
                                //this should not run though because it has already been checked
                                if($bulk_wallet_balance_after <= 0){
                                    $status = -1;
                                    $message = 'Failed due to insufficient balance via bulk data wallet';
                                    $failure++;
                                    $display_results[$i] = array(
                                        'message' => $message,
                                        'status' => $status
                                    );
                                }
                        
                                $description = 'Purchase of data via data wallet';
                                $creationData['transaction_category'] = 'data';
                                $creationData['user_id'] = $user_id;
                                $creationData['wallet_category'] = $request->wallet_category;
                                $creationData['product_plan_id'] = $request->product_plan_id;
                                $creationData['phone_number'] = $phone_numbers_array[$i];
                                $creationData['amount'] = $amount;
                                $creationData['status'] = $status;
                                $creationData['balance_before'] = $bulk_wallet_balance_before;
                                $creationData['balance_after'] = $bulk_wallet_balance_after;
                                $creationData['description'] = $description;
                                $creationData['user_screen_message'] = $message;
                                $creationData['admin_screen_message'] = $message;
                                Transaction::create($creationData);
                    
                                UserBulkDataWallet::where('user_id',$user_id)
                                                    ->where('product_plan_category_id',$request->product_plan_category_id)
                                                    ->update([
                                                        'bulk_wallet_balance_mb' => $bulk_wallet_balance_after
                                                    ]);
                    
                            }
    
                            DB::commit();
                            return response()->json(['status'=>1, 'message'=>'Transaction was successfully processed', 'data' => $display_results  ]);
                    


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
    public function buy_bulk_data_action(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bulk_data_plan_id' => 'required|exists:bulk_data_product_plans,id',
            'bulk_data_wallet_id' => 'required|exists:user_bulk_data_wallets,id',
            'pin' => ['required','digits:4'],
        ]);
        
        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json(['status'=>'-1', 'message'=>$validator->errors()->first(),'data' => $request->all() ]);
        }

        $success = 0;
        $failure = 0;
        $status = 0;
        $message = 'Pending';
        $display_results = [];

        $user_bulk_data_wallet = UserBulkDataWallet::where('id',$request->bulk_data_wallet_id)->first();
        if(! $user_bulk_data_wallet){
            return response()->json(['status'=>'-1', 'message'=>'Wallet not found' ]);
        }


        $bulk_data_plan = BulkDataProductPlans::where('id',$request->bulk_data_plan_id)->first();
        if(! $bulk_data_plan){
            return response()->json(['status'=>'-1', 'message'=>'bulk data product not found' ]);
        }


        $user_details = auth()->user();
        if(! $user_details){
            //end session and redirect to login
            redirect(url('/login'));
            return response()->json(['status'=>'-1', 'message'=>'please logout and login again' ]);
        }

        
        if( $user_details->pin != $request->pin){
            return response()->json(['status'=>'-1', 'message'=>'Incorrect PIN entered' ]);
        }

        $user_plan_id = auth()->user()->user_plan_id;

        $main_wallet = auth()->user()->main_wallet;

       
        $user_level = UserPlan::select('plan_level')->where('id',$user_plan_id)->first();

        $plan_level = $user_level->plan_level;

        $user_plan_selling_price = 'user_level_'.$plan_level.'_selling_price';

      
        $price = $bulk_data_plan->$user_plan_selling_price;


        $wallet_before = $main_wallet;


        ////validate wallet
        if($price > $wallet_before){
            return response()->json(['status'=>'-1', 'message'=>'Insufficient wallet balance' ]);
        }

        DB::beginTransaction();

        try{
    //now, lets do actual txn
            $wallet_after = $wallet_before - abs($price);

            $data_in_mb = $bulk_data_plan->data_value_mb;

            $former_data_wallet_balance = $user_bulk_data_wallet->bulk_wallet_balance_mb; 

            $former_alltime_data_wallet_balance = $user_bulk_data_wallet->alltime_bulk_wallet_balance_mb; 
            $new_data_wallet_balance =  $former_data_wallet_balance + $data_in_mb;
            $new_alltime_data_wallet_balance =  $former_alltime_data_wallet_balance + $data_in_mb;

        
            //update user wallet 
            $dataaa['bulk_data_wallet_id'] = $user_bulk_data_wallet->id;
            $dataaa['bulk_data_plan_name'] = $bulk_data_plan->bulk_data_plan_name;
            $dataaa['user_id'] = $user_details->id;
            $dataaa['main_wallet_before'] = $wallet_before;
            $dataaa['main_wallet_after'] = $wallet_after;
            $dataaa['wallet_data_balance_before'] = $former_data_wallet_balance;
            $dataaa['wallet_data_balance_after'] = $new_data_wallet_balance;
            $dataaa['bulk_data_product_plan_id'] = $bulk_data_plan->id;
            $dataaa['plan_category_id'] = $bulk_data_plan->product_plan_category_id;
            $dataaa['data_value_mb'] = $bulk_data_plan->data_value_mb;
            $dataaa['data_value_gb'] = $bulk_data_plan->data_value_gb;
            $dataaa['data_value_tb'] = $bulk_data_plan->data_value_tb;
            $dataaa['amount_spent'] = $price;
            $dataaa['mb_data_measurement'] =$bulk_data_plan->mb_data_measurement;
            // return response()->json(['status'=>'-1', 'message'=>json_encode($dataaa)]);

            $create = UserBulkDataPurchase::create($dataaa);

            $user = User::where('id',$user_details->id)->update([
                'main_wallet' => $wallet_after
            ]);

            $bulk_wallet_update = UserBulkDataWallet::where('id',$user_bulk_data_wallet->id)
            ->update([
                'bulk_wallet_balance_mb' => $new_data_wallet_balance,
                'alltime_bulk_wallet_balance_mb' => $new_alltime_data_wallet_balance,
            ]);

            DB::commit();
            return response()->json(['status'=>1, 'message'=>'Bulk data  was successfully processed', 'data' => $dataaa  ]);
        
        }catch(\Exception $exception){
            logger($exception->getMessage().' on line '.$exception->getLine());
            DB::rollBack();
            return response()->json(['status'=>-1, 'message'=>$exception->getMessage() .' on line '.$exception->getLine(), 'data' => $dataaa  ]);

        }

     
    }


    public function get_single_bulk_data_wallet($plan_id){
        $plan_details = ProductPlan::where('id',$plan_id)->first();
        $plan_category_id = $plan_details->product_plan_category_id;
        $user_id = auth()->id();
        $get_user_wallet_details = UserBulkDataWallet::with('product_plan_category')->where('user_id',$user_id)
                                   ->where('product_plan_category_id',$plan_category_id)
                                   ->first();
        if(! $get_user_wallet_details){
            return response()->json(['status'=>-1,'message' => 'single bulk wallet could not be fetched' ,'data' => [], 'wallet' => 0  ]);

        }

        return response()->json(['status'=>1,'message' => 'single bulk wallet successfullly fetched' ,'data' => $get_user_wallet_details, 'wallet' => number_format($get_user_wallet_details->bulk_wallet_balance_mb) .' MB'  ]);

    }


    //TODO: move to a separate class
    public function validateUserWallet($user_id,$wallet_before,$phone_numbers_count,$amount){
        
        return true;
    }


     /**
     * Get all the products plans categories.
     */
    public function fetch_bulk_data_plans(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bulk_data_wallet_id' => 'required',
        ]);
          

        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json(['status'=>'-1', 'message'=>'Bulk data wallet should be selected','data' => $request->all() ]);
        }

        $bulk_data_wallet_id = $request->bulk_data_wallet_id;
        $bulk_wallet_details = UserBulkDataWallet::where('id',$bulk_data_wallet_id)->first();
        if($bulk_wallet_details == NULL){
            return response()->json(['status'=>'-1', 'message'=>'Bulk data wallet could not be found','data' => [] ]);
        }
        $product_plans_category_id = $bulk_wallet_details->product_plan_category_id;
        $bulk_data_plans_for_this_wallet = BulkDataProductPlans::where('product_plan_category_id',$product_plans_category_id)->get();
        if( count($bulk_data_plans_for_this_wallet) <= 0){
            return response()->json(['status'=>'-1', 'message'=>'No bulk data plan found for this wallet at the moment','data' => [] ]);
        }
        
        return response()->json(['status'=>'1', 'message'=>'Product plans for this wallet fetched','data' => $bulk_data_plans_for_this_wallet ]);

    }

    /**
     * Get all the products plans categories.: this works for all product: NEEDS REVAMP
     */
    public function fetch_product_plan_categories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network_id' => 'required',
            'product_slug' => 'required'
        ]);
          
        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json(['status'=>'-1', 'message'=>'network is required','data' => $request->all() ]);
        }

        $network = $request->network_id;
        $product_id = Product::where('slug',$request->product_slug)->first()->id;
        $product_plans_categories = ProductPlanCategory::where('network_id',$network)->where('product_id',$product_id)->get();
        
        return response()->json(['status'=>'1', 'message'=>'Product plans categories fetched','data' => $product_plans_categories ]);

    }

     /**
     * Get all the products plans.
     */
    public function fetch_product_plans(Request $request)
    {
        $network_id = $request->network_id ?? '';
        $plan_category_id = $request->plan_category_id ?? '';
        $product_slug = $request->product_slug ?? ''; //this is required
        
        $product_id = Product::where('slug',$product_slug)->first()->id;
         
        if($plan_category_id == ''){
            $product_plan_categories = ProductPlanCategory::select('id','automation_id')->where('product_id',$product_id)->where('network_id',$network_id)->get();
        }else{
            $product_plan_categories = ProductPlanCategory::when(!empty($network_id), function($query) use ($network_id) {
                $query->where('network_id',$network_id);
            })->select('id','automation_id')
            ->where('product_id',$product_id)
            ->where('id',$plan_category_id)
            ->get();
        }


       
        $product_planss = [];
        $counter =0;

       //TODO: 
        $user_details = auth()->user();
        $user_plan_id = $user_details->user_plan_id;
        $user_id = $user_details->id;
        $user_level = UserPlan::select('plan_level')->where('id',$user_plan_id)->first();
        $plan_level = $user_level->plan_level;


        foreach($product_plan_categories as $key=>$product_plan_category){
            //get the automation id
            //get the product_category_id 
            $product_plans = ProductPlan::where('product_plan_category_id',$product_plan_category->id)
            ->where('automation_id',$product_plan_category->automation_id)
            ->get();
            if(count($product_plans) > 0){
                foreach($product_plans as $product_plan){

                    $user_level_selling = "user_level_".$plan_level."_selling_price";
                    // $user_level_selling = "{user_level_$user_level_selling_price}";
                    $selling_price = $product_plan->$user_level_selling;
                    if($product_plan){
                        $counter++;
                        $product_planss[$counter]['product_plan_id'] = $product_plan->id;
                        $product_planss[$counter]['selling_price'] = $selling_price;
                        $product_planss[$counter]['product_plan_name'] = $product_plan->product_plan_name;
                        $product_planss[$counter]['data_size_in_mb'] = $product_plan->data_size_in_mb;
                        $product_planss[$counter]['validity_in_days'] = $product_plan->validity_in_days;    
                        $product_planss[$counter]['automation_id'] = $product_plan->automation_id;    
                    }
                }
            }    
        }
        
           
        return response()->json(['status'=>'1','user_level'=>$plan_level ,'message'=>'Product plans fetched','counter' =>count($product_planss),'data' => $product_planss ]);
        // return response()->json(['status'=>'1','user_level'=>$user_plan_id ,'message'=>'Product plans fetched','counter' =>count($product_planss),'data' => $product_planss ]);

    }

    
     /**
     * Get bul the products plans.
     */
    public function fetch_bulk_data_plan_details(Request $request)
    {
        $bulk_data_plan_id = $request->bulk_data_plan_id ?? '';
        $bulk_data_product_plan = BulkDataProductPlans::where('id',$bulk_data_plan_id)->first();
        
       //TODO: 
        $user_details = auth()->user();
        $user_plan_id = $user_details->user_plan_id;
        $user_level = UserPlan::select('plan_level')->where('id',$user_plan_id)->first();
        $plan_level = $user_level->plan_level;
        $user_selling_price = "user_level_".$plan_level."_selling_price";
        $bulk_data_product_plan->selling_price = $bulk_data_product_plan->$user_selling_price;
           
        return response()->json(['status'=>'1','user_level'=>$plan_level ,'message'=>'Bulk data plans fetched','data' => $bulk_data_product_plan ]);
        // return response()->json(['status'=>'1','user_level'=>$user_plan_id ,'message'=>'Product plans fetched','counter' =>count($product_planss),'data' => $product_planss ]);

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
