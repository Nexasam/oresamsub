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
use App\Services\Automation\MegaSubPlugAutomation\MegaSubElectricity;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubelectricityTV;

class ElectricitySubscriptionController extends Controller
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
    public function buy_electricity_subscription()
    {

       
        $product = Product::where('slug','utility_bills')->first(); //TODO: have enums that gets the id later
        $data['product'] = $product;

        $product_plan_categories = ProductPlanCategory::where('product_id',$product->id)->get(); //TODO: have enums that gets the id later
        $data['product_plan_categories'] = $product_plan_categories;
        

        $user_details = auth()->user();
        $user_id = $user_details->id;
        // dd($user_id);

        //data txns list
        $electricity_transactions = Transaction::with('user')->where('transaction_category','utility_bills')
        ->where('user_id',$user_id)
        ->latest()
        ->get();
        $data['electricity_transactions'] = $electricity_transactions;
        $data['user_details'] = $user_details;

        // dd($data);
        return view('user.electricity.buy_electricity')->with($data);
    }

    public function validate_metre_number(Request $request){
        //call the automation involved
        $plan_details = ProductPlan::with('product_plan_category','automation')->where('id',$request->plan_id)->first();
      
        if(! $plan_details){
            return [
                'status' => -1,
                'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
                'admin_message' => 'Wrong plan Id',
            ];
        }

        $automation_slug = $plan_details->automation->slug;
        if($automation_slug == 'megasubplug'){
            $validate_metre_number = (new MegaSubElectricity(metre_number: $request->smart_card_number, plan_id: $request->plan_id))->validateMetreNumber();
            return $validate_metre_number;
      
        }
    }


       /**
     * Show the form for creating a new resource.
     */
    public function buy_electricity_subscription_by_plan_category($id)
    {

        $plan_category = ProductPlanCategory::with('product','network','automation')->where('id',$id)->first();
        
        $product_plans = ProductPlan::where('automation_id',$plan_category->automation->id)->where('product_plan_category_id',$id)->get();
        
        $user_details = auth()->user();
        $user_id = $user_details->id;
        $user_plan_id = $user_details->user_plan_id;
       
        $product_planss = [];
        $counter = 0;

        $user_level = UserPlan::select('plan_level')->where('id',$user_plan_id)->first();
        $plan_level = $user_level->plan_level;
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


        $user_details = auth()->user();
        $user_id = $user_details->id;
        // dd($user_id);

        //data txns list
        $electricity_transactions = Transaction::with('user')->where('transaction_category','utility_bills')
        ->where('user_id',$user_id)
        ->latest()
        ->get();
        $data['electricity_transactions'] = $electricity_transactions;
        $data['user_details'] = $user_details;
        $data['plan_category'] = $plan_category;
        $data['product_plans'] = $product_planss;
        

        // dd($data);
        return view('user.electricity.buy_electricity_by_category')->with($data);
    }

    


    /**
     * Store a newly created resource in storage.
     */
    public function buy_electricity_subscription_action(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'metre_number' => 'required',
            'validation_extra_info' => 'required',
            'electricity_product_plan_category_id' => 'required',
            'electricity_product_plan_id' => 'required',
            'wallet_category' => 'required',
            'no_of_slots' => 'required',
            'pin' => ['required','digits:4'],
        ]);
        
        // return response()->json(['status'=>'-1', 'message'=>$validator->errors()->first(),'data' => $request->electricity_product_plan_category_id ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json(['status'=>'-1', 'message'=>$validator->errors()->first(),'data' => $request->all() ]);
        }

        $success = 0;
        $failure = 0;
        $status = 0;
        $message = 'Pending';
        $display_results = [];
        $no_of_slots = $request->no_of_slots; //to be adjusted later

        $plan_details = ProductPlan::where('id',$request->electricity_product_plan_id)->first();
        if(! $plan_details){
            //end session and redirect to login
            redirect(url('/login'));
            return response()->json(['status'=>'-1', 'message'=>'plan details not found' ]);
        }
        $automation_id = $plan_details->automation_id;
        // $data_value_mb = $plan_details->data_size_in_mb ?? 0;

        $plan_category_details = ProductPlanCategory::where('id',$request->electricity_product_plan_category_id)->first();
        if(! $plan_category_details){
            //end session and redirect to login
            redirect(url('/login'));
            return response()->json(['status'=>'-1', 'message'=>'plan category details not found' ]);
        }

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
        $metre_number = $request->metre_number;
     

        DB::beginTransaction();
        try{

              ////validate wallet
                        if($request->wallet_category == 'main_wallet'){
                            $wallet_before = $user_details->main_wallet;
                            $total_amount =  $no_of_slots * $amount;
                            if($total_amount > $wallet_before){
                                return response()->json(['status'=>'-1', 'message'=>'Insufficient wallet balance' ]);
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
                                 
                                    $buy_electricity_subscription = (new MegaSubElectricity($metre_number,$request->electricity_product_plan_id,$total_amount,$request->validation_extra_info,1,$plan_category_details->product_plan_category_name,$user_details->phone_number))->buyElectricity();
                                    // return response()->json(['status'=>'-1', 'message'=>$buy_electricity_subscription ]);
                               
                                }else{
                                    //this will be like this until other automations are processed
                                    $buy_electricity_subscription['status'] = 1;
                                    $buy_electricity_subscription['user_message'] = 'Electricity subscription was successfully processed.';
                                    $buy_electricity_subscription['admin_message'] = 'Electricity subscription was successfully processed.';
                                }
                                // logger(json_encode($buy_electricity_subscription_megasub));
                                // dd($buy_electricity_subscription_megasub);

                                if($buy_electricity_subscription['status'] == 1){
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

                                $user_message = $buy_electricity_subscription['user_message'];
                                $admin_message = $buy_electricity_subscription['admin_message'];
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
                        
                                $description = 'Purchase of electricity subscription';
                                $creationData['transaction_category'] = 'utility_bills';
                                $creationData['user_id'] = $user_id;
                                $creationData['wallet_category'] = $request->wallet_category;
                                $creationData['product_plan_id'] = $request->electricity_product_plan_id;
                                $creationData['phone_number'] =  NULL;
                                $creationData['smart_card_number'] = $request->metre_number;
                                // $creationData['electricity_tv_slots'] = 1;
                                $creationData['amount'] = $amount;
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
                                $walletLog['action_by'] = auth()->user()->id;
                                $walletLog['description'] = 'UTILITY BILLS Purchase from main wallet with transaction_id';
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
        $product_id = Product::where('slug','utility_bills')->first()->id;
        $product_plans_categories = ProductPlanCategory::whereIn('product_id',$product_id)->where('network_id',$network)->get();
        
        return response()->json(['status'=>'1', 'message'=>'Product plans categories fetched','data' => $product_plans_categories ]);

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
