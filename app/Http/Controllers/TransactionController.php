<?php

namespace App\Http\Controllers;

use App\Models\ProductPlan;
use App\Models\Transaction;
use App\Models\SiteTemplate;
use Illuminate\Http\Request;
use App\Models\UserBulkDataWallet;
use App\Models\ProductPlanCategory;
use Illuminate\Support\Facades\Mail;
use App\Mail\WalletFundingNotification;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Traits\Dashboard\UserDashboardDataTrait;

class TransactionController extends Controller
{
    use UserDashboardDataTrait;

  public function user_all_transactions(){
    $dataa = $this->get_user_dashboard_data();
    $data = [...$dataa];

    $data['product_plan_categories'] = ProductPlanCategory::select('id','product_plan_category_name')->get();

    $siteTemplate = SiteTemplate::first();
        if(! $siteTemplate || $siteTemplate->template_name == 'template_1'){
            return view('user.transactions.index')->with($data);
        }

        $data['hideNav'] = true;
        return view('template2.user.transactions.index')->with($data);
  } 
  
  public function admin_all_transactions(){
    $data['product_plan_categories'] = ProductPlanCategory::select('id','product_plan_category_name')->get();

    return view('admin.transactions.index')->with($data);
  } 

  public function transaction_details($id){
    $dataa = $this->get_user_dashboard_data();
    $data = [...$dataa];
    // dd($data['user']->role->role_name);
    $data['data'] = Transaction::with(['user','product_plan.product_plan_category'])->where('id',$id)->first();

    $siteTemplate = SiteTemplate::first();
    if(! $siteTemplate || $siteTemplate->template_name == 'template_1' || $data['user']->role->role_name == 'Admin' ){
        return view('transaction_details')->with($data);
    }

    return view('template2.user.transactions.detail')->with($data);
  }

  public function transaction_refund(Request $request){
    $validator = Validator::make($request->all(), [
        'pin' => ['required','string','regex:/^\d{4,5}$/'],
        'transaction_id' => 'required|exists:transactions,id',
      ]);

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

      if(auth()->user()->pin != $request->pin){
        //end session and redirect to login
        Session::flash('failure','Incorrect PIN entered'); 
        return redirect()->back();
      }

      //steps to refund::: put in a service class later: put in a separation fxn temporaritly
      //get the amount of txn, get the balance of the user, then add the funds back, next log what has happened
      $transaction_details = Transaction::with('user')->where('id',$request->transaction_id)->first();
    //   return $transaction_details;
      if(! $transaction_details){
        Session::flash('failure','Transaction not found'); 
        return redirect()->back();
      }

      $amount = $transaction_details->amount;
      $amount_deducted = $transaction_details->discounted_amount ?? $transaction_details->amount;
      $wallet_category = $transaction_details->wallet_category;
      $transaction_category = $transaction_details->transaction_category;
      $status = $transaction_details->status;
      $user_id = $transaction_details->user_id;
      if($transaction_details->status == 2){
        Session::flash('failure','This is a refunded transaction'); 
        return redirect()->back();
      }

      if($wallet_category == 'main_wallet'){
        $former_wallet_balance =  $transaction_details->user->main_wallet;
        $new_wallet_balance = $transaction_details->user->main_wallet + $amount_deducted;

        //update user wallet
         $transaction_details->user->update([
            'main_wallet' => $new_wallet_balance
         ]); 


         $transaction_details->update([
            'status' => 2 //i.e refunded
         ]); 

         $walletLog['user_id'] = $user_id;
         $walletLog['transaction_category'] = 'REFUND_TRANSACTION';
         $walletLog['balance_before'] = $former_wallet_balance;
         $walletLog['balance_after'] = $new_wallet_balance;
         $walletLog['transaction_id'] = $transaction_details->id;
         $walletLog['action_by'] = auth()->user()->id;
         $walletLog['description'] = 'Transaction was refunded for the ID: '. $transaction_details->id;
         $this->log_wallet_transactions($walletLog);
        //log: refund

        Session::flash('success','Refund was successful'); 
        return redirect()->back();

      }else{

            // return [
            //     'status' => 'refund of transaction from data wallet in progress'
            // ];

            $product_plan_details = ProductPlan::select('product_plan_category_id')->where('id',$transaction_details->product_plan_id)->first();

            $get_bulk_data_wallet_details = UserBulkDataWallet::where('user_id',$user_id)->where('product_plan_category_id',$product_plan_details->product_plan_category_id)->first();
                            
            if(! $get_bulk_data_wallet_details ){
                 Session::flash('failure','No bulk data wallet found'); 
                 return redirect()->back();
            }
            $current_bulk_wallet_balance = $get_bulk_data_wallet_details->bulk_wallet_balance_mb;
            $data_size_bought = $transaction_details->balance_before - $transaction_details->balance_after;
            $new_bulk_wallet_balance = $current_bulk_wallet_balance + $data_size_bought;
        
            //update user wallet
            UserBulkDataWallet::where('user_id',$user_id)->where('product_plan_category_id',$product_plan_details->product_plan_category_id)
            ->update([
                'bulk_wallet_balance_mb' => $new_bulk_wallet_balance
            ]); 

            $transaction_details->update([
              'status' => 2 //i.e refunded
            ]); 
        
             $walletLog['user_id'] = $user_id;
             $walletLog['transaction_category'] = 'REFUND_DATA_WALLET_TRANSACTION';
             $walletLog['balance_before'] = $current_bulk_wallet_balance;
             $walletLog['balance_after'] = $new_bulk_wallet_balance;
             $walletLog['transaction_id'] = $transaction_details->id;
             $walletLog['action_by'] = auth()->user()->id;
             $walletLog['description'] = 'Data wallet transaction was refunded for the ID: '. $transaction_details->id;
             $this->log_wallet_transactions($walletLog);

             Session::flash('success','Refund was successful.'); 
             return redirect()->back();
      }

      
      //if refs, work on their reversals too but this should never happen because rewards happen only when txn is confirmed
      //if data purchase, treat separately

  }

  public function user_fetch_transactions(Request $request){

        // $date_from = $request->date_from ?? '';
        // $date_to= $request->date_to ?? '';

        // $date_from = $request->date_from ?? date('Y-m-d');
        // date('Y-m-d', strtotime('-10 days'))
        // ?? date('Y-m-d')
        $date_from = $request->date_from ?? '';
        $date_to= $request->date_to ;
        $product_plan_category_filter = $request->product_plan_category_filter ?? '';
        
        $phone = $request->phone_recharged ?? '';
        

        $limit = $request->limit ?? 2000;

        // ->when( !empty($email) , function ($query) use ($email){
        //     $query->where('email',$email);
        //   })

        // $product_plan_ids = ProductPlan::where('product_plan_category_id',$product_plan_category_filter)->pluck('id');
        // return $product_plan_ids;
        
        $data = Transaction::when(!empty($date_from) && !empty($date_to) , function ($query) use ($date_from,$date_to){
            $date_to = date('Y-m-d', strtotime('+1 day', strtotime($date_to)));
            $query->where('created_at','>=',$date_from)->where('created_at','<=',$date_to);
        })->when(!empty($product_plan_category_filter) , function ($query) use ($product_plan_category_filter){
            $product_plan_ids = ProductPlan::where('product_plan_category_id',$product_plan_category_filter)->pluck('id');
            $query->whereIn('product_plan_id',$product_plan_ids);
        })->when(!empty($phone) , function ($query) use ($phone){
          $query->where('phone_number',$phone);
        })
        ->with(['user','product_plan'])
        ->where('wallet_category','!=','data_wallet')
        ->where('user_id',auth()->id())
        ->latest()->limit($limit)->get();

        //  return $data;
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
                    $response_decode = json_decode($data->admin_screen_message,true);
                    $token_details = isset($response_decode['Detail']['info']['realresponse']) ? $response_decode['Detail']['info']['realresponse'] :  '-';
                    $prefix = $token_details == '-' ? 'Token details: ' : '';
                    $dataa .=  'Metre No: '.$data->metre_number.'<br>';
                    $dataa .=  '<b>'.$prefix.':  '.$token_details.'</b><br>';
                }
                if($data->transaction_category == 'data'){
                    $dataa .= number_format($data->product_plan->data_size_in_mb ?? '0') .' MB';
                }

            }else{
                $dataa = 'NIL';
            }
            return '<span style="white-space: normal;word-wrap: break-word;word-break: normal;width:auto">'.$dataa.'</span>';
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
            // $route = 'transactions.transaction_details';
            $route = route('transactions.transaction_details',$data->id);
            $actionBtn = '<a href="'.$route.'" type="button" class="hs-dropdown-toggle ti-btn ti-btn-primary" data-hs-overlay="#hs-vertically-centered-scrollable-modal'.$data->email.'">
            Details
            </a>';
            return $actionBtn;
        })
        
        ->escapeColumns([])
        ->make(true);


       
  }


  public function admin_fetch_transactions(Request $request){

        // $date_from = $request->date_from ?? date('Y-m-d');
        // date('Y-m-d', strtotime('-10 days'))
        $date_from = $request->date_from ?? '';

        // ?? date('Y-m-d')
        $date_to= $request->date_to ?? '';

        $product_plan_category_filter = $request->product_plan_category_filter ?? '';
        
        $phone = $request->phone_recharged ?? '';
    
        $limit = $request->limit ?? 700;

        
        $data = Transaction::when(!empty($date_from) && !empty($date_to) , function ($query) use ($date_from,$date_to){
            $date_to = date('Y-m-d', strtotime('+1 day', strtotime($date_to)));
            $query->where('created_at','>=',$date_from)->where('created_at','<=',$date_to);
        })->when(!empty($product_plan_category_filter) , function ($query) use ($product_plan_category_filter){
            $product_plan_ids = ProductPlan::where('product_plan_category_id',$product_plan_category_filter)->pluck('id');
            $query->whereIn('product_plan_id',$product_plan_ids);
        })->when(!empty($phone) , function ($query) use ($phone){
          $query->where('phone_number',$phone);
        })
        ->where('wallet_category','!=','data_wallet')
        ->with(['user','product_plan'])->latest()->limit($limit)
        ->get();


        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('DT_RowIndex',function($data){
        return $data->id;
        })
        ->addColumn('user_id',function($data){
            $user_plan_name = $data->user->user_plan->updated_user_plan_name ??  $data->user->user_plan->user_plan_name;
            $first_name = $data->user->first_name  ?? 'nil';
            $last_name = $data->user->last_name  ?? 'nil';
            $phone_number = $data->user->phone_number  ?? 'nil';
            $user_details =  $first_name.'<br>'.$last_name.'<br>'.$phone_number.'<br>'; 
            $user_details .= '<b><i>'.$user_plan_name.'</i></b>';    
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
                    $response_decode = json_decode($data->admin_screen_message,true);
                    $token_details = isset($response_decode['Detail']['info']['realresponse']) ? $response_decode['Detail']['info']['realresponse'] :  '-';
                    $prefix = $token_details == '-' ? 'Token details: ' : '';
                    $dataa .=  'Metre No: '.$data->metre_number.'<br>';
                    $dataa .=  '<b>'.$prefix.'  '.$token_details.'</b>  <br>';
                }
                if($data->transaction_category == 'data'){
                    $dataa .= number_format($data->product_plan->data_size_in_mb ?? '0') .' MB';
                }

            }else{
                $dataa = 'NIL';
            }
            return '<span style="white-space: normal;word-wrap: break-word;word-break: normal;width:auto">'.$dataa.'</span>';
        })

        ->addColumn('transaction_category',function($data){
            $transaction_category = $data->transaction_category;
            return $transaction_category;
        })
        // ->addColumn('response',function($data){
        //     return  '<span style="white-space: normal;word-wrap: break-word;word-break: normal;width:auto">'.$data->user_screen_message.'</span>';
        //     // return $user_screen_message;
        // })
        // ->addColumn('admin_response',function($data){
        //     return  '<span style="white-space: normal;word-wrap: break-word;word-break: normal;width:auto">'.$data->admin_screen_message.'</span>';
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

          
            if($data->set_for_manual == 1){
                $status_display .= '<span class="font-bold">URGENT: TREAT MANUALLY</span>';
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


  public function manually_mark_transaction_as_successful(Request $request){
       $validator = Validator::make($request->all(), [
        'success_message' => 'required',
        'pin' => 'required','string','regex:/^\d{4,5}$/',
        'transaction_id' => 'required|exists:transactions,id',
        ]);


        if ($validator->stopOnFirstFailure()->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        //   if(auth()->user()->pin != $request->pin){
        //     //end session and redirect to login
        //     Session::flash('failure','Incorrect PIN entered'); 
        //     return redirect()->back();
        //   }


        $transaction_details = Transaction::with('user')->where('id',$request->transaction_id)->first();
        if(! $transaction_details){
            Session::flash('failure','Transaction not found'); 
            return redirect()->back();
        }

        $txnid = $transaction_details->id;
        $txnid = $transaction_details->id;
        $status = $transaction_details->status;
        if($transaction_details->status == 1){
            Session::flash('failure','This transaction is already successful'); 
            return redirect()->back();
        }

       $userinfooo = auth()->user()->username.' '.auth()->user()->email;

        //update user wallet
        $transaction_details->update([
            'status' => 1,
            'user_screen_message' => $request->success_message,
            'admin_screen_message' => 'MANUALLY RESOLVED: '.$request->success_message,
            'set_for_manual' => 0,
            'manually_processed_by' => $userinfooo,
        ]); 

        //  $walletLog['user_id'] = $user_id;
        //  $walletLog['transaction_category'] = 'MARK_TRANSACTION_AS_SUCCESSFUL';
        //  $walletLog['balance_before'] = $former_wallet_balance;
        //  $walletLog['balance_after'] = $new_wallet_balance;
        //  $walletLog['transaction_id'] = $transaction_details->id;
        //  $walletLog['action_by'] = auth()->user()->id;
        //  $walletLog['description'] = 'Transaction was refunded for the ID: '. $transaction_details->id;
        //  $this->log_wallet_transactions($walletLog);
        //log: refund


        Session::flash('success','Transaction was successfully mark as Successful'); 
        return redirect()->back();
 }




}
