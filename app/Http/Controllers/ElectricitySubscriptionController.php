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
use App\Models\SiteTemplate;
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
use App\Services\Automation\AutomationLogic;
use App\Traits\Dashboard\UserDashboardDataTrait;
use App\Services\Automation\MegaSubPlugAutomation\VendData;
use App\Http\Services\Api\v1\VendorUsersApi\Products\ProductsService;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubElectricity;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubelectricityTV;

class ElectricitySubscriptionController extends Controller
{
    use UserDashboardDataTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    public function fetch_electricity_transactions(Request $request){
   
        $date_from = $request->date_from ?? date('Y-m-d', strtotime('-10 days'));
        $date_to= $request->date_to ?? date('Y-m-d');

        $product_plan_category_filter = $request->product_plan_category_filter ?? '';
        
        $metre = $request->metre_number ?? '';
        
        $limit = $request->limit ?? 2000;

       
        $data = Transaction::when(!empty($date_from) && !empty($date_to) , function ($query) use ($date_from,$date_to){
            $date_to = date('Y-m-d', strtotime('+1 day', strtotime($date_to)));
            $query->where('created_at','>=',$date_from)->where('created_at','<=',$date_to);
        })->when(!empty($product_plan_category_filter) , function ($query) use ($product_plan_category_filter){
            $product_plan_ids = ProductPlan::where('product_plan_category_id',$product_plan_category_filter)->pluck('id');
            $query->whereIn('product_plan_id',$product_plan_ids);
        })->when(!empty($metre) , function ($query) use ($metre){
          $query->where('metre_number',$metre);
        })
        ->where('transaction_category','utility_bills')
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
        // ->addColumn('phone_number',function($data){
        //     return $data->phone_number;
        // }) 
        ->addColumn('metre_number',function($data){
            return $data->metre_number;
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
                $status_display = '<span class="badge bg-red-300 text-white">Unsuccessful</span>';
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
     * Show the form for creating a new resource.
     */
    public function buy_electricity_subscription()
    {
        $dataa = $this->get_user_dashboard_data();
        $data = [...$dataa];

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

        $siteTemplate = SiteTemplate::first();
        if(! $siteTemplate || $siteTemplate->template_name == 'template_1'){
            return view('user.electricity.buy_electricity')->with($data);
        }

        // dd($data);
        return view('template2.user.electricity.buy_electricity')->with($data);
    }

    public function validate_metre_number(Request $request){
        //call the automation involved
        $plan_details = ProductPlan::with('product_plan_category','automation')
        ->where('visibility',1)
        ->where('id',$request->plan_id)->first();
      
        if(! $plan_details){
            return [
                'status' => -1,
                'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
                'admin_message' => 'Wrong plan Id',
            ];
        }

        $user_id = auth()->id();
        $automation_slug = $plan_details->automation->slug;
        if($automation_slug == 'megasubplug'){
            $validate_metre_number = (new MegaSubElectricity(metre_number: $request->smart_card_number, plan_id: $request->plan_id, user_id: $user_id))->validateMetreNumber();
            return $validate_metre_number;
      
        }
    }


       /**
     * Show the form for creating a new resource.
     */
    public function buy_electricity_subscription_by_plan_category($id)
    {

        $plan_category = ProductPlanCategory::with('product','network','automation')->where('id',$id)->first();
        
        $product_plans = ProductPlan::where('automation_id',$plan_category->automation->id)
        ->where('visibility',1)
        ->where('product_plan_category_id',$id)->get();

        $product = Product::where('slug','utility_bills')->first(); //TODO: have enums that gets the id later
        $product_plan_categories = ProductPlanCategory::where('product_id',$product->id)->get(); //TODO: have enums that gets the id later
        $data['product_plan_categories'] = $product_plan_categories;
        
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
            'validation_address' => 'nullable',
            'electricity_product_plan_category_id' => 'required',
            'electricity_product_plan_id' => 'required',
            'wallet_category' => 'required',
            'no_of_slots' => 'required',
            'amount' => 'required',
            'pin' => ['required','string','regex:/^\d{4,5}$/'],
        ]);
        
        // return response()->json(['status'=>'-1', 'message'=>$validator->errors()->first(),'data' => $request->electricity_product_plan_category_id ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json(['status'=>'-1', 'message'=>$validator->errors()->first(),'data' => $request->all() ]);
        }

        $data1['days_count'] = [1,7,30];
        $data1['user_id'] = auth()->id();
        $check_purchase_limit =  ProductsService::check_purchase_limit($data1);
        if($check_purchase_limit['status'] == -1){
            return response()->json(['status'=>'-1', 'message'=>$check_purchase_limit['message'] ]);
        }

        
        if($request->amount < 0){
            return response()->json(['status'=>'-1', 'message'=>'amount cannot be less than 0','data' => ''  ]);
        }

        $success = 0;
        $failure = 0;
        $status = 0;
        $message = 'Pending';
        $display_results = [];
        $no_of_slots = $request->no_of_slots; //to be adjusted later

        $plan_details = ProductPlan::where('id',$request->electricity_product_plan_id)
        ->where('visibility',1)
        ->first();
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
        $user_details = auth()->user();
        $user_id = $user_details->id;
        $metre_number = $request->metre_number;

        if(! $user_details){
            //end session and redirect to login
            redirect(url('/login'));
            return response()->json(['status'=>'-1', 'message'=>'please logout and login again' ]);
        }


        if($user_details->pin != $request->pin){
            //end session and redirect to login
            return response()->json(['status'=>'-1', 'message'=>'Incorrect PIN' ]);
        }

        //////////////////////    
        $automation_id = $plan_details->automation_id;
        $product_plan_category = $plan_details->product_plan_category;
        $actual_amount = abs($request->amount);

        $user_level_selling = "user_level_".$plan_level."_selling_price";
        $purchase_discount =  $plan_details->$user_level_selling;
        $actual_discount_value = ceil(($purchase_discount/100) * $actual_amount);  
        //below forms the new amount to sell to the user: discounted
        // $amount = ceil($actual_amount - abs($actual_discount_value));
        $amount = $actual_discount_value < 0 || $actual_discount_value > $actual_amount ? $actual_amount : ($actual_amount - $actual_discount_value);

     

        DB::beginTransaction();
        try{

              ////validate wallet
                        if($request->wallet_category == 'main_wallet'){
                            $wallet_before = $user_details->main_wallet;
                            $total_amount =  $no_of_slots * $amount;
                            if($total_amount > $wallet_before || $wallet_before < 0){
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
                                


                                //WE MOVED THIS
                                // if($automation_details->slug == 'megasubplug'){
                                //     $duplication_check = 1;
                                //     $buy_electricity_subscription = (new MegaSubElectricity($metre_number,$request->electricity_product_plan_id,$total_amount,$request->validation_extra_info,1,$plan_category_details->product_plan_category_name,$user_details->phone_number))->buyElectricity();
                                //     // return response()->json(['status'=>'-1', 'message'=>$buy_electricity_subscription ]);
                               
                                // }else{
                                //     //this will be like this until other automations are processed
                                //     $buy_electricity_subscription['status'] = -1;
                                //     $buy_electricity_subscription['user_message'] = 'Electricity subscription failed...';
                                //     $buy_electricity_subscription['admin_message'] = 'Electricity subscription failed...';
                                // }
                                // // logger(json_encode($buy_electricity_subscription_megasub));
                                // // dd($buy_electricity_subscription_megasub);

                                $data['automation_details'] = $automation_details;
                                $data['metre_number'] = $metre_number;
                                $data['plan_id'] = $request->electricity_product_plan_id;
                                $data['slots'] = 1;
                                $data['total_amount'] = $total_amount;
                                $data['validation_extra_info'] = $request->validation_extra_info;
                                $data['product_plan_category_name'] = $plan_category_details->product_plan_category_name;
                                $data['phone_number'] = $user_details->phone_number;
                                $data['user_id'] = $user_id;
                                $buy_electricity_subscription = AutomationLogic::initiateElectricityPurchase($data);
                                logger('ELECTTT: '.json_encode($buy_electricity_subscription));

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
                                $creationData['metre_number'] = $request->metre_number;
                                $creationData['validation_address'] = $request->validation_address;
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
        $product_plans_categories = ProductPlanCategory::whereIn('product_id',$product_id)
        ->where('visibility',1)
        ->where('network_id',$network)->get();
        
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
