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


        $product_plan_categories = ProductPlanCategory::where('product_id',$product->id)->get(); //TODO: have enums that gets the id later
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
        $data['plan_category'] = $plan_category;
        $data['product_plans'] = $product_planss;
        
        // dd($data);
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
        $amount = $request->amount;

        $user_level_selling = "user_level_".$plan_level."_selling_price";
        $purchase_discount =  $plan_details->$user_level_selling;
        $actual_discount_value = ceil(($purchase_discount/100) * $amount);  
        //below forms the new amount to sell to the user
        $amount = ceil($amount - abs($actual_discount_value));
        
        
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
                                    // logger($buy_airtime);
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
        ->with(['user','product_plan'])->latest()->limit($limit)->get();
        // return $data;


      return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('DT_RowIndex',function($data){
        return $data->id;
        })
        ->addColumn('user_id',function($data){
            $first_name = $data->user->first_name  ?? 'nil';
            $last_name = $data->user->last_name  ?? 'nil';
            $phone_number = $data->user->phone_number  ?? 'nil';
            $user_details =  $first_name.'<br>'.$last_name.'<br>'.$phone_number.'<br>';     
            return $user_details;
        })
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
        ->addColumn('response',function($data){
            return  '<span style="white-space: normal;word-wrap: break-word;word-break: normal;width:auto">'.$data->user_screen_message.'</span>';
            // return $user_screen_message;
        })
        ->addColumn('phone_number',function($data){
            return $data->phone_number;
        }) 
       ->addColumn('amount',function($data){
        return '&#8358;'.(number_format($data->amount,2));
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
            $route = '#';
            // $route = route('transaction_details',$data->id);
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
