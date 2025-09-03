<?php

namespace App\Http\Controllers;

use App\Models\ProductPlanCustomPricing;
use App\Models\UniqueProductPlan;
use Exception;
use App\Models\User;
use App\Models\Network;
use App\Models\Product;
use App\Models\UserPlan;
use App\Models\Automation;
use App\Models\CouponCode;
use App\Models\ProductPlan;
use App\Models\Transaction;
use App\Models\SiteTemplate;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use App\Models\UsedUserCouponCode;
use App\Models\UserBulkDataWallet;
use App\Models\UserVirtualAccount;
use Illuminate\Support\Facades\DB;
use App\Models\ProductPlanCategory;
use App\Services\Utils\UtilService;
use App\Models\BulkDataProductPlans;
use App\Models\UserBulkDataPurchase;
use App\Traits\WalletTransactionLogs;
use Illuminate\Support\Facades\Route;
use App\Http\Services\CouponCodeService;
use Illuminate\Support\Facades\Validator;
use App\Services\Automation\AutomationLogic;
use App\Traits\Dashboard\UserDashboardDataTrait;
use App\Services\Automation\MegaSubPlugAutomation\VendData;
use App\Services\Automation\OgdamsAutomation\OgdamsVendData;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubVendData;
use App\Http\Services\Api\v1\VendorUsersApi\Products\ProductsService;
use App\Services\Automation\MsOrgGroupAutomation\MsOrgGroupAutomation;

class DataController extends Controller
{
    use UserDashboardDataTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    public function fetch_data_transactions(Request $request){
   
        // date('Y-m-d', strtotime('-10 days'))
        $date_from = $request->date_from ?? '';

        // date('Y-m-d');
        $date_to= $request->date_to ?? '';

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
        ->where('transaction_category','data')
        ->where('wallet_category','main_wallet')
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

    public function fetch_data_wallet_transactions(Request $request){
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
        ->where('transaction_category','data')
        ->where('wallet_category','data_wallet')
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
    public function buy_data_v2()
    {
        $dataa = $this->get_user_dashboard_data();
        $data = [...$dataa];
        // dd($data);
        $networks = Network::all();
        $product = Product::where('slug','data')->first(); //TODO: have enums that gets the id later
        $data['networks'] = $networks;
        $data['product'] = $product;
        // dd($data);
        $product_plan_categories = ProductPlanCategory::where('product_id',$product->id)->get(); //TODO: have enums that gets the id later
        $data['product_plan_categories'] = $product_plan_categories;

        $siteTemplate = SiteTemplate::first();
        if(! $siteTemplate || $siteTemplate->template_name == 'template_1'){
            return view('user.data.buy_data_v2')->with($data);
        }

        // dd($data);
        return view('template2.user.data.buy_data')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function buy_data()
    {
       
        $dataa = $this->get_user_dashboard_data();
        $data = [...$dataa];
        $networks = Network::all();
        $product = Product::where('slug','data')->first(); //TODO: have enums that gets the id later
        $data['networks'] = $networks;
        $data['product'] = $product;
        // dd($data);
        $product_plan_categories = ProductPlanCategory::where('product_id',$product->id)->get(); //TODO: have enums that gets the id later
        $data['product_plan_categories'] = $product_plan_categories;

        $siteTemplate = SiteTemplate::first();
        if(! $siteTemplate || $siteTemplate->template_name == 'template_1'){
            return view('user.data.buy_data')->with($data);
        }

        // dd($data);
        return view('template2.user.data.buy_data')->with($data);
    }

    public function buy_data_by_plan_category($id){
        
        $plan_category = ProductPlanCategory::with('product','network','automation')->where('id',$id)->first();
        
        $product_plans = ProductPlan::where('automation_id',$plan_category->automation->id)
        ->where('visibility',1)
        ->where('product_plan_category_id',$id)->get();
        
        $product = Product::where('slug','data')->first(); //TODO: have enums that gets the id later
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

        // dd($user_id);

        //data txns list
        $data_transactions = Transaction::with('user')->where('transaction_category','data')
        ->where('user_id',$user_id)
        ->latest()
        ->get();
        $data['data_transactions'] = $data_transactions;
        $data['user_details'] = $user_details;
        $data['plan_category'] = $plan_category;
        $data['product_plans'] = $product_planss;
        
        // dd($data);
        return view('user.data.buy_data_by_plan_category')->with($data);
    }


     /**
     * Show the form for creating a new resource.
     */
    public function buy_bulk_data()
    {
        $bulk_data_wallets = UserBulkDataWallet::with('product_plan_category')->where('user_id',auth()->id())->get();
        $user_bulk_data_purchases = UserBulkDataPurchase::with('product_plan_category')->where('user_id',auth()->id())->latest()->paginate(50);
        
        $product_plan_categories = ProductPlanCategory::get(); //TODO: have enums that gets the id later
        $data['product_plan_categories'] = $product_plan_categories;

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
            'product_plan_category_id' => 'nullable', #watchh, changed 4th aug. 25
            'product_plan_id' => 'required',
            'pin' => ['required','regex:/^\d{4,5}$/'],
            'wallet_category'=>['required',Rule::in(['main_wallet','data_wallet'])],
            'validatephonenetwork'=>['required',Rule::in([0,1])],
        ]);
        
        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json(['status'=>'-1', 'message'=>$validator->errors()->first(),'data' => $request->all() ]);
        }


        $data1['days_count'] = [1,7,30];
        $data1['user_id'] = auth()->id();
        $check_purchase_limit =  ProductsService::check_purchase_limit($data1);
        if($check_purchase_limit['status'] == -1){
            return response()->json(['status'=>'-1', 'message'=>$check_purchase_limit['message'] ]);
        }



        $pending = 0;
        $success = 0;
        $failure = 0;
        $status = 0;
        $message = 'Pending';
        $display_results = [];
        $coupon_count = 0;

        $plan_details = ProductPlan::where('id',$request->product_plan_id)->where('visibility',1)->first();
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
            return response()->json(['status'=>'-1', 'message'=>__('messages.Incorrect PIN') ]);
        }

        $user_id = $user_details->id;
        $phone_numbers = $request->phone_number;
        //ensure there are no mapping issue:
        $phone_numbers = trim($phone_numbers);
        $phone_numbers_array = explode(',',$phone_numbers);
        $phone_numbers_count = count($phone_numbers_array);

        if($phone_numbers_count == 1){
            $phone_number = $phone_numbers;
            $validate_phone = (new UtilService())->phoneNumberValidation($phone_number);
            $validated_phone_number = $validate_phone['validated_phone_number'];
            if($validate_phone['status'] != 1){
                return response()->json(['status'=>'-1', 'message'=>$validate_phone['message'].' Number: '.$validated_phone_number  ]);
            }
        }

        //HERE SELLING PRICE CHANGES IF THEHRE IS A CUSTOM SETTING: put in a service later
        $check_custom_setting = ProductPlanCustomPricing::where('product_plan_id','=', $request->product_plan_id)->where('user_id',$user_id)->first();
        $amount = $check_custom_setting == NULL ? $amount : $check_custom_setting->price;  

        DB::beginTransaction();
        try{

              ////validate wallet
                        if($request->wallet_category == 'main_wallet'){
                            $wallet_before = $user_details->main_wallet;
                            $total_amount = $phone_numbers_count * $amount;
                            if($total_amount > $wallet_before || $wallet_before < 0){
                                return response()->json(['status'=>'-1', 'message'=>'Insufficient wallet balance' ]);
                            }
                    
                            //calling the actual vending via the automation:
                            $automation_details = Automation::where('id',$automation_id)->first();
                            //TODO: candidate for separation:
                            for($i = 0; $i < count($phone_numbers_array); $i++ ){


                                $datacoupon['product_plan_id'] = $request->product_plan_id;
                                $datacoupon['amount'] = $amount; //original amount
                                $datacoupon['user'] = $user_details; 
                                $get_deducted_amount = (new CouponCodeService())->get_coupon_information($datacoupon);
                                $amount_after_coupon = $get_deducted_amount['amount'];
                                $amount = $amount_after_coupon; //this is the new amount
                                $coupon = $get_deducted_amount['coupon'];
                                $remaining_slots = $get_deducted_amount['remaining_slots'];
                                $dataa['coupon'] = $coupon;


                                $phone_number = $phone_numbers_array[$i];
                                $dataa['phone_number'] = $phone_number;
                                $dataa['automation_details'] = $automation_details;
                                $dataa['network_id'] = $request->network_id;
                                $dataa['plan_id'] = $request->product_plan_id;
                                $dataa['validatephonenetwork'] = $request->validatephonenetwork;
                                $sell_data = AutomationLogic::initiateDataPurchase($dataa);
                                $set_for_manual = $sell_data['set_for_manual'] ?? 0;
                                // logger('DATAAA: '.json_encode($sell_data));


                                if($sell_data['status'] == 1){

                                    $coupon_count  = 1;
                                    $success++;
                                    $status = 1;
                                    $wallet_before = User::where('id',$user_id)->first()->main_wallet;
                                    $wallet_after = $wallet_before - $amount;

                                }else if($sell_data['status'] == 0){
                                    //SET TO PENDING , but user was debited:::not useful for now
                                    $coupon_count  = 0;

                                    $pending++;
                                    $status = 0;
                                    $wallet_before = User::where('id',$user_id)->first()->main_wallet;
                                    $wallet_after = $wallet_before - $amount;
                                   
                                }
                                else{
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
                                    $user_message = 'Failed due to insufficient balance';
                                    $admin_message = 'Failed due to insufficient balance';
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
                                $creationData['set_for_manual'] = $set_for_manual ?? 0;
                                $creationData['user_id'] = $user_id;
                                $creationData['wallet_category'] = $request->wallet_category;
                                $creationData['product_plan_id'] = $request->product_plan_id;
                                $creationData['phone_number'] = $validated_phone_number;
                                $creationData['amount'] = $amount;
                                $creationData['coupon_code_id'] = $coupon;
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
                                $walletLog['action_by'] = auth()->user()->id;           
                                $walletLog['description'] = 'Data Purchase from main wallet';
                                $this->log_wallet_transactions($walletLog);
                    
                                User::where('id',$user_id)->update([
                                    'main_wallet' => $wallet_after
                                ]);
                    
                            }

                            DB::commit();
                    
                            if($failure > 1){
                              return response()->json(['status'=>2, 'message'=>" $failure issue(s) found. Check transaction history", 'data' => $display_results  ]);   
                            }
                            else if(count($phone_numbers_array) == 1 && $failure == 1){
                                return response()->json(['status'=>2, 'message'=>"Ops, Transaction was not successful, please try again", 'data' => $display_results  ]);   
                            }
                            else{
                                if($pending > 0){
                                    return response()->json(['status'=>1, 'message'=>'Transaction is being processed.', 'data' => $display_results  ]);
                                }else{
                                    return response()->json(['status'=>1, 'message'=>'Transaction was successfully processed', 'data' => $display_results  ]);
                                }
                            }
                    
                        } 

                        if($request->wallet_category == 'data_wallet'){
                            $get_bulk_data_wallet_details = UserBulkDataWallet::where('user_id',$user_id)->where('product_plan_category_id',$request->product_plan_category_id)->first();
                            
                            if(! $get_bulk_data_wallet_details ){
                                $bulk_wallet_balance_before = 0;
                            }
                            $bulk_wallet_balance_before = $get_bulk_data_wallet_details->bulk_wallet_balance_mb ?? 0;

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
                                if($automation_details->slug == 'megasubplug'){
                                    $sell_data = (new MegaSubVendData($phone_numbers_array[$i],$request->product_plan_id,$request->validatephonenetwork))->buyData();
                                }
                                else if($automation_details->slug == 'ogdams' || $automation_details->slug == 'ogdamsv2'){
                                    $sell_data = (new OgdamsVendData($phone_numbers_array[$i],$request->product_plan_id))->buyData();
                                }
                                else{
                                    //this will be like this until other automations are processed
                                    $sell_data['status'] = -1;
                                    $sell_data['user_message'] = 'Bulk data processing failed.';
                                    $sell_data['admin_message'] = 'Bulk data processing failed.';
                                }

                                if($sell_data['status'] == 1){
                                    $success++;
                                    $status = 1;
                                    $get_bulk_data_wallet_details = UserBulkDataWallet::where('user_id',$user_id)->where('product_plan_category_id',$request->product_plan_category_id)->first();
                                    $bulk_wallet_balance_before = $get_bulk_data_wallet_details->bulk_wallet_balance_mb;
                                    $bulk_wallet_balance_after = $bulk_wallet_balance_before - $data_value_mb; 
                                }else{
                                    //it might be processing or it failed
                                    $status = -1;
                                    $failure++;
                                    $get_bulk_data_wallet_details = UserBulkDataWallet::where('user_id',$user_id)->where('product_plan_category_id',$request->product_plan_category_id)->first();
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
                                ->where('product_plan_category_id',$request->product_plan_category_id)
                                ->update([
                                    'bulk_wallet_balance_mb' => $bulk_wallet_balance_after
                                ]);
                        
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
                                $creationData['user_screen_message'] = $user_message;
                                $creationData['admin_screen_message'] = $admin_message;
                                $transaction = Transaction::create($creationData);

                                $walletLog['user_id'] = $user_id;
                                $walletLog['transaction_category'] = 'DATA_FROM_DATA_WALLET';
                                $walletLog['balance_before'] = $bulk_wallet_balance_before;
                                $walletLog['balance_after'] = $bulk_wallet_balance_after;
                                $walletLog['transaction_id'] = $transaction->id;
                                $walletLog['action_by'] = auth()->user()->id;
                                $walletLog['description'] = 'Data Purchase from data wallet';
                                $this->log_wallet_transactions($walletLog);             
                            }
    
                            DB::commit();
                            if($failure > 0){
                                return response()->json(['status'=>2, 'message'=>" $failure issue(s) found. Check transaction history", 'data' => $display_results  ]);   
                            }
                            return response()->json(['status'=>1, 'message'=>'Bulk data transaction was successfully processed', 'data' => $display_results  ]);
                     
                    


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
            'pin' => ['required','string','regex:/^\d{4,5}$/'],
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

            $description = 'Bulk Data Purchase';
            $creationData['transaction_category'] = 'bulk_data';
            $creationData['user_id'] = auth()->id();
            $creationData['wallet_category'] = 'main_wallet';
            $creationData['product_plan_id'] = $user_bulk_data_wallet->id; //not sure what to do here
            $creationData['amount'] = $price;
            $creationData['discounted_amount'] = $price;
            $creationData['status'] = 0;
            $creationData['balance_before'] = $wallet_before;
            $creationData['balance_after'] = $wallet_after;
            $creationData['description'] = $description;
            $creationData['admin_screen_message'] = 'Bulk data purchase was successful: '. $bulk_data_plan->bulk_data_plan_name;
            $creationData['user_screen_message'] = 'Purchase of Bulk data plan: '. $bulk_data_plan->bulk_data_plan_name.' was successful';
            $transaction = Transaction::create($creationData);

            $walletLog['user_id'] = $user_details->id;
            $walletLog['transaction_category'] = 'BULK_DATA_PURCHASE';
            $walletLog['balance_before'] = $wallet_before;
            $walletLog['balance_after'] = $wallet_after;
            $walletLog['transaction_id'] = $create->id;
            $walletLog['action_by'] = auth()->user()->id;
            $walletLog['description'] = 'BULK DATA Purchase from main wallet with transaction_id';
            $this->log_wallet_transactions($walletLog);

            DB::commit();
            return response()->json(['status'=>1, 'message'=>'Bulk data was successfully processed', 'data' => $dataaa  ]);
        
        }catch(Exception $exception){
            logger($exception->getMessage().' on line '.$exception->getLine());
            DB::rollBack();
            return response()->json(['status'=>-1, 'message'=>$exception->getMessage() .' on line '.$exception->getLine(), 'data' => $dataaa  ]);

        }

     
    }


    public function get_single_bulk_data_wallet($plan_id){
        $plan_details = ProductPlan::where('id',$plan_id)->where('visibility',1)->first();
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
        $product_plans_categories = ProductPlanCategory::where('network_id',$network)
        ->where('visibility',1)
        ->where('product_id',$product_id)->get();
        
        return response()->json(['status'=>'1', 'message'=>'Product plans categories fetched','data' => $product_plans_categories ]);

    }


       /**
     * Get all the products plans. TODO: THIS must be moved to a service, airtime is using this function, other products are doing too
     */
    public function fetch_product_plans(Request $request)
    {

        // return response()->json(['status'=>'1','user_level'=>3 ,'message'=>'Product plans fetched','counter' =>5,'data' => $request->all() ]);



        $network_id = $request->network_id ?? '';
        $amount = $request->amount ?? '';
        $plan_category_id = $request->plan_category_id ?? '';
        $product_slug = $request->product_slug ?? ''; //this is required
        // logger($product_slug.'wpp');
        $slug_array = ['data','airtime','cable_subscription','utility_bills'];
        if(! in_array($product_slug,$slug_array)){
            //get it by route:
            $current_route = Route::currentRouteName();

            if($current_route == 'user.data.buy_data' || $current_route == 'user.data.buy_data2'){
                $product_slug = 'data';
            }

            else if($current_route == 'user.airtime.buy_airtime' || $current_route == 'user.airtime.buy_airtime2'){
                $product_slug = 'airtime';
            }

            else if($current_route == 'user.cable_subscription.buy_cable_subscription'){
                $product_slug = 'cable_subscription';
            }

            else if($current_route == 'user.electricity.buy_electricity_subscription'){
                $product_slug = 'utility_bills';
            }

            else{
                // $product_slug = '';
            }
            
        }


        logger('TESTING SLUG: '.$product_slug);

        $product_id = Product::where('slug',$product_slug)->first()->id;

        $product_planss = [];

        $counter =0;

       //TODO: 
        $user_details = auth()->user();
        $user_plan_id = $user_details->user_plan_id;
        $user_id = $user_details->id;
        $user_level = UserPlan::select('plan_level')->where('id',$user_plan_id)->first();
        $plan_level = $user_level->plan_level;
        $sellingp = 'user_level_'.$plan_level.'_selling_price';


        ///NEW VERSION 1
        if(auth()->user()->email == 'oreofe@gmail.com' && $product_slug == 'data'){
            $uniqueplans = UniqueProductPlan::where('network_id',$request->network_id)->first();
            foreach($uniqueplans as $product_plan){

                //get thhe normal pricing
                $price_level = 'price_'.$plan_level;
                $amount = $product_plan->$price_level;
                $selling_price = $amount;


                //HERE SELLING PRICE CHANGES IF THEHRE IS A CUSTOM SETTING: put in a service later
                $check_custom_setting = ProductPlanCustomPricing::where('product_plan_id','=', $product_plan->id)->where('user_id',auth()->id())->first();
                $amount = $check_custom_setting == NULL ? $amount : $check_custom_setting->price;  
                $user_level_selling = "user_level_".$plan_level."_selling_price";
                $user_level_commission = "user_level_".$plan_level."_commission";
                $selling_price = $product_plan->$user_level_selling;
                $upline_commission = $product_plan->$user_level_commission;
                $selling_price = $check_custom_setting == NULL ? $selling_price : $check_custom_setting->price;  
            
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
                   $product_planss[$counter]['product_plan_id'] = $product_plan->id;
                   $product_planss[$counter]['amount'] = $amount;
                   $product_planss[$counter]['selling_price'] = $discounted_selling_price.'oooo';
                   $product_planss[$counter]['upline_commission'] = $upline_commission;
                   $product_planss[$counter]['product_plan_name'] = $product_plan->product_plan_name;
                   $product_planss[$counter]['data_size_in_mb'] = $product_plan->data_size_in_mb;
                   $product_planss[$counter]['validity_in_days'] = $product_plan->validity_in_days;    
                   $product_planss[$counter]['automation_id'] = $product_plan->product_plan->automation->id;    
               }
           }

                // Extract unique sizes from $product_planss
                $data_sizes = collect($product_planss)
                ->pluck('data_size_in_mb')
                ->unique()
                ->sort()
                ->values()
                ->toArray();
           
                return response()->json(['status'=>'1','user_level'=>$plan_level ,'message'=>'Product plans fetched','counter' =>count($product_planss),'data' => $product_planss, 'sizes' => $data_sizes ]);

        }
        
         
        if($plan_category_id == ''){
            $product_plan_categories = ProductPlanCategory::select('id','automation_id')
            ->where('product_id',$product_id)
            ->where('network_id',$network_id)
            ->get();

            $product_plan_categories_id_arr = ProductPlanCategory::where('product_id',$product_id)
            ->where('network_id',$network_id)
            ->pluck('id')
            ->toArray();
            
        }else{
            $product_plan_categories = ProductPlanCategory::when(!empty($network_id), function($query) use ($network_id) {
                $query->where('network_id',$network_id);
            })
            ->select('id','automation_id')
            ->where('product_id',$product_id)
            ->where('id',$plan_category_id)
            ->get();
        }
     
        //plan category has a value
        if($plan_category_id != ''){
            foreach($product_plan_categories as $key=>$product_plan_category){
                //get the automation id
                //get the product_category_id 
    
                if($product_slug == 'airtime'){
                    $product_plans = ProductPlan::where('product_plan_category_id',$product_plan_category->id)
                    // ->where('automation_id',$product_plan_category->automation_id)
                    ->where('visibility',1)
                    ->limit(1)
                    ->get();
                }else{
                    // $product_plans = ProductPlan::where('product_plan_category_id',$product_plan_category->id)
                    // ->where('visibility',1)
                    // // ->where('automation_id',$product_plan_category->automation_id)
                    // ->orderByRaw('CAST(data_size_in_mb AS UNSIGNED)')
                    // ->orderByRaw('CAST('.$sellingp.' AS UNSIGNED)')
                    // ->orderByRaw('CAST(validity_in_days AS UNSIGNED) DESC')
                    // ->get();

                    $product_plans = ProductPlan::where('product_plan_category_id', $product_plan_category->id)
                    ->where('visibility', 1)
                    ->orderByRaw('CASE WHEN CAST(data_size_in_mb AS UNSIGNED) < 500 THEN 1 ELSE 0 END') // Push <500MB to bottom
                    ->orderByRaw('CAST(data_size_in_mb AS UNSIGNED)') // Then order by size
                    ->orderByRaw('CAST(' . $sellingp . ' AS UNSIGNED)') // Then by price
                    ->orderByRaw('CAST(validity_in_days AS UNSIGNED) DESC') // Then by validity
                    ->get();
                }
    
                if(count($product_plans) > 0){
                    foreach($product_plans as $product_plan){


                         //HERE SELLING PRICE CHANGES IF THEHRE IS A CUSTOM SETTING: put in a service later
                         $check_custom_setting = ProductPlanCustomPricing::where('product_plan_id','=', $product_plan->id)->where('user_id',auth()->id())->first();
                         $amount = $check_custom_setting == NULL ? $amount : $check_custom_setting->price;  
    
                        $user_level_selling = "user_level_".$plan_level."_selling_price";
                        $user_level_commission = "user_level_".$plan_level."_commission";

                        $selling_price = $product_plan->$user_level_selling;
                        $upline_commission = $product_plan->$user_level_commission;
 
                        $selling_price = $check_custom_setting == NULL ? $selling_price : $check_custom_setting->price;  


                        
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
                            $product_planss[$counter]['product_plan_id'] = $product_plan->id;
                            $product_planss[$counter]['amount'] = $amount;
                            $product_planss[$counter]['selling_price'] = $discounted_selling_price;
                            $product_planss[$counter]['upline_commission'] = $upline_commission;
                            $product_planss[$counter]['product_plan_name'] = $product_plan->product_plan_name;
                            $product_planss[$counter]['data_size_in_mb'] = $product_plan->data_size_in_mb;
                            $product_planss[$counter]['validity_in_days'] = $product_plan->validity_in_days;    
                            $product_planss[$counter]['automation_id'] = $product_plan->automation_id;    
                        }
                    }
                }    
            }
        }else{

            // $product_plans = ProductPlan::whereIn('product_plan_category_id',$product_plan_categories_id_arr)
            // ->where('visibility',1)
            // // ->where('automation_id',$product_plan_category->automation_id)
            // ->orderByRaw('CAST(data_size_in_mb AS UNSIGNED)')
            // ->orderByRaw('CAST('.$sellingp.' AS UNSIGNED)')
            // ->orderByRaw('CAST(validity_in_days AS UNSIGNED) DESC')
            // ->get();


            $product_plans = ProductPlan::whereIn('product_plan_category_id', $product_plan_categories_id_arr)
            ->where('visibility', 1)
            ->orderByRaw('CASE WHEN CAST(data_size_in_mb AS UNSIGNED) < 500 THEN 1 ELSE 0 END') // Push <500MB to bottom
            ->orderByRaw('CAST(data_size_in_mb AS UNSIGNED)') // Then order by size
            ->orderByRaw('CAST(' . $sellingp . ' AS UNSIGNED)') // Then by price
            ->orderByRaw('CAST(validity_in_days AS UNSIGNED) DESC') // Then by validity
            ->get();

            //here plan category is empty:
            if(count($product_plans) > 0){
                foreach($product_plans as $product_plan){


                   

                    $user_level_selling = "user_level_".$plan_level."_selling_price";
                    $user_level_commission = "user_level_".$plan_level."_commission";
                    // $user_level_selling = "{user_level_$user_level_selling_price}";
                    $selling_price = $product_plan->$user_level_selling;
                    $upline_commission = $product_plan->$user_level_commission;

                     //HERE SELLING PRICE CHANGES IF THEHRE IS A CUSTOM SETTING: put in a service later
                     $check_custom_setting = ProductPlanCustomPricing::where('product_plan_id','=', $product_plan->id)->where('user_id',auth()->id())->first();
                     $selling_price = $check_custom_setting == NULL ? $selling_price : $check_custom_setting->price;  
                    
                    if( ( $product_slug == 'airtime' || $product_slug == 'utility_bills' ) && $amount != ''){
                          $purchase_discount = $product_plan->$user_level_selling;
                        //   logger('purchase discount:'.$purchase_discount);
                        //   logger('purchase amount:'.$amount);
                          $actual_discount_value = ceil(($purchase_discount/100) * $amount);  
                          $discounted_selling_price = $amount - abs($actual_discount_value);
                          $selling_price = 0; //this is from the system, not applicable for airtime
                    }else{
                        $discounted_selling_price = $selling_price;
                    }

               
                    if($product_plan){
                        $counter++;
                        $product_planss[$counter]['product_plan_id'] = $product_plan->id;
                        $product_planss[$counter]['amount'] = $amount;
                        $product_planss[$counter]['selling_price'] = $discounted_selling_price;
                        $product_planss[$counter]['upline_commission'] = $upline_commission;
                        $product_planss[$counter]['product_plan_name'] = $product_plan->product_plan_name;
                        $product_planss[$counter]['data_size_in_mb'] = $product_plan->data_size_in_mb;
                        $product_planss[$counter]['validity_in_days'] = $product_plan->validity_in_days;    
                        $product_planss[$counter]['automation_id'] = $product_plan->automation_id;    
                    }
                }
            }   


        }
       

        // Extract unique sizes from $product_planss
        $data_sizes = collect($product_planss)
        ->pluck('data_size_in_mb')
        ->unique()
        ->sort()
        ->values()
        ->toArray();

        
           
        return response()->json(['status'=>'1','user_level'=>$plan_level ,'message'=>'Product plans fetched','counter' =>count($product_planss),'data' => $product_planss, 'sizes' => $data_sizes ]);
        // return response()->json(['status'=>'1','user_level'=>$user_plan_id ,'message'=>'Product plans fetched','counter' =>count($product_planss),'data' => $product_planss ]);

    }


     /**
     * Get all the products plans. TODO: THIS must be moved to a service, airtime is using this function, other products are doing too
     */
    public function fetch_product_plansOLD1(Request $request)
    {

        // return response()->json(['status'=>'1','user_level'=>3 ,'message'=>'Product plans fetched','counter' =>5,'data' => $request->all() ]);

        $network_id = $request->network_id ?? '';
        $amount = $request->amount ?? '';
        $plan_category_id = $request->plan_category_id ?? '';
        $product_slug = $request->product_slug ?? ''; //this is required
        // logger('ProductSlug display in fetch product plans fxn: '.$product_slug.' <== display here.');
        
        
        $product_id = Product::where('slug',$product_slug)->first()->id;
        // logger($plan_category_id);
         
        if($plan_category_id == ''){
            $product_plan_categories = ProductPlanCategory::select('id','automation_id')
            ->where('product_id',$product_id)
            ->where('network_id',$network_id)
            ->get();
        }else{
            $product_plan_categories = ProductPlanCategory::when(!empty($network_id), function($query) use ($network_id) {
                $query->where('network_id',$network_id);
            })->select('id','automation_id')
            ->where('product_id',$product_id)
            ->where('id',$plan_category_id)
            ->get();
        }

        // return response()->json(['status'=>'1','user_level'=>3 ,'message'=>'Product plans fetchedddd','counter' =>5,'data' => $network_id ]);
       
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

            if($product_slug == 'airtime'){
                $product_plans = ProductPlan::where('product_plan_category_id',$product_plan_category->id)
                // ->where('automation_id',$product_plan_category->automation_id)
                ->where('visibility',1)
                ->limit(1)
                ->get();
            }else{
                $product_plans = ProductPlan::where('product_plan_category_id',$product_plan_category->id)
                ->where('visibility',1)
                // ->where('automation_id',$product_plan_category->automation_id)
                // ->orderBy('data_size_in_mb')
                ->orderByRaw('CAST(data_size_in_mb AS UNSIGNED)')
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
                        $product_planss[$counter]['product_plan_id'] = $product_plan->id;
                        $product_planss[$counter]['amount'] = $amount;
                        $product_planss[$counter]['selling_price'] = $discounted_selling_price;
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
     * Get the network, and product plans based on a phone number
     */
    public function fetch_data_plans_by_phone_number(Request $request){
        $validate_phone = (new UtilService())->phoneNumberNetworkValidation($request->phone_number);
        $validated_phone_number = $validate_phone['validated_phone_number'];
        $selected_network = $validate_phone['selected_network'] ?? 'NIL';
        // $selected_network_caps = strtoupper($selected_network);
        $get_network_id = Network::where('network_name',$selected_network)->first();
        if($get_network_id){
            $network_id = $get_network_id->id;
            //use the network to fetch productplans for product list

        } else{
            //network not determined
            $network_id = '';
            $selected_network = 'Select';
        }
        sleep(1);
        return response()->json(['status'=>'1','network_id' => $network_id, 'network_name' => $selected_network ]);
        // return response()->json(['status'=>'1','user_level'=>$plan_level ,'message'=>'Bulk data plans fetched','data' => $bulk_data_product_plan ]);
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
