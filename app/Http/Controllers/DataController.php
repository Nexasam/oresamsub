<?php

namespace App\Http\Controllers;

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
use App\Models\ProductPlanCategory;
use Illuminate\Support\Facades\Validator;

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
        ->get();
        $data['data_transactions'] = $data_transactions;

        // dd($data);
        return view('user.data.buy_data')->with($data);
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
        $user_id = $user_details->id;
        $phone_numbers = $request->phone_number;
        $phone_numbers = trim($phone_numbers);
        $phone_numbers_array = explode(',',$phone_numbers);
        $phone_numbers_count = count($phone_numbers_array);

        ////validate wallet
        $wallet_before = $request->wallet_category == 'main_wallet' ? $user_details->main_wallet : $user_details->data_wallet;
        $total_amount = $phone_numbers_count * $amount;
        if($total_amount > $wallet_before){
            return response()->json(['status'=>'-1', 'message'=>'Insufficient wallet balance' ]);
        }
        //validate user wallet

        //validate the user wallet
        // $wallet_after = $wallet_before - $total_amount;

        //calling the actual vending via the automation:
        $automation_details = Automation::where('id',$automation_id)->first();

        //TODO: candidate for separation
        for($i = 0; $i < count($phone_numbers_array); $i++ ){
         
            //vend data
            //HERE the endpoint of the automation service is called
            //simulate success
            $success++;
            $message = 'Successfully processed';
            $status = 1;
            $display_results[$i] = array(
                'message' => $message,
                'status' => $status
            );
            $wallet_before = User::where('id',$user_id)->first()->main_wallet;
            $wallet_after = $wallet_before - $amount;


            //this should not run though because it has already been checked
            if($wallet_after <= 0){
                $status = -1;
                $message = 'Failed due to insufficient balance';
                $failure++;
                $display_results[$i] = array(
                    'message' => $message,
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
            $creationData['user_screen_message'] = $message;
            $creationData['admin_screen_message'] = $message;
            Transaction::create($creationData);

            User::where('id',$user_id)->update([
                'main_wallet' => $wallet_after
            ]);

        }

        // if($success > 0 && $success >= $phone_numbers_count){
        //     //no error
        // }else if($success > 0 && $success < $phone_numbers_count){
        //     //some errors and some success
        // }else{
        //     // everything failed
        // }
        return response()->json(['status'=>1, 'message'=>'Transaction was successfully processed', 'data' => $display_results  ]);

        

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
        $product_plans_categories = ProductPlanCategory::where('network_id',$network)->get();
        
        return response()->json(['status'=>'1', 'message'=>'Product plans categories fetched','data' => $product_plans_categories ]);

    }

     /**
     * Get all the products plans.
     */
    public function fetch_product_plans(Request $request)
    {
        $network_id = $request->network_id ?? '';
        $plan_category_id = $request->plan_category_id ?? '';



        if($plan_category_id == ''){
            $product_plan_categories = ProductPlanCategory::select('id','automation_id')->where('network_id',$network_id)->get();
        }else{
            $product_plan_categories = ProductPlanCategory::select('id','automation_id')
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
