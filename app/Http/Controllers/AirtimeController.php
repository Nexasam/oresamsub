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
     * Store a newly created resource in storage.
     */
    public function buy_airtime_action(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network_id' => 'required',
            'phone_number' => 'required',
            'product_plan_category_id' => 'required',
            'product_plan_id' => 'required',
            'pin' => ['required','max:4'],
            'amount' => 'required|numeric|gt:0',
            // 'wallet_category'=>['required',Rule::in(['main_wallet'])],
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
        // $data_value_mb = $plan_details->data_size_in_mb ?? 0;

        $user_plan_id = auth()->user()->user_plan_id;
        $user_level = UserPlan::select('plan_level')->where('id',$user_plan_id)->first();
        $plan_level = $user_level->plan_level;
        $user_plan_selling_price = 'user_level_'.$plan_level.'_selling_price';
        $amount = abs($request->amount);
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
                                //this is for megasubplug: vend for Airtime
                                
                                if($automation_details->slug == 'megasubplug'){
                                    $buy_airtime = (new MegaSubVendAirtime($phone_numbers_array[$i],$request->product_plan_id,$amount))->buyAirtime();
                                }else{
                                    //this will be like this until other automations are processed
                                    $buy_airtime['status'] = 1;
                                    $buy_airtime['user_message'] = 'Airtime was successfully processed.';
                                    $buy_airtime['admin_message'] = 'Airtime was successfully processed.';
                                }
                                // logger(json_encode($buy_airtime_megasub));
                                // dd($buy_airtime_megasub);

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
                        
                                $description = 'Purchase of airtime';
                                $creationData['transaction_category'] = 'airtime';
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
        $product_plans_categories = ProductPlanCategory::whereIn('product_id',$product_id)->where('network_id',$network)->get();
        
        return response()->json(['status'=>'1', 'message'=>'Product plans categories fetched','data' => $product_plans_categories ]);

    }

     /**
     * Get all the products plans.
     */
    public function fetch_product_plans(Request $request)
    {
        $network_id = $request->network_id ?? '';
        $plan_category_id = $request->plan_category_id ?? '';
        $product_id = Product::where('slug','airtime')->first()->id;



        if($plan_category_id == ''){
            $product_plan_categories = ProductPlanCategory::select('id','automation_id')->where('product_id',$product_id)->where('network_id',$network_id)->get();
        }else{
            $product_plan_categories = ProductPlanCategory::select('id','automation_id')
            ->where('product_id',$product_id)
            ->where('network_id',$network_id)
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
