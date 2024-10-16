<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\FundingOption;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use App\Models\AdminWebhookString;
use App\Models\UserVirtualAccount;
use Illuminate\Support\Facades\DB;
use App\Models\LandingPagesSetting;
use App\Models\FundingWebhookPayload;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\MaxCrystalPaymentsPendingApproval;

class WalletsController extends Controller
{

    public function webhook($id,Request $request){
       
        //{
        //   $resp = '{"event":"VIRTUAL_ACCOUNT_INFLOW",
        //   "source":{
        //       "bank_name":"WEMA BANK",
        //       "account_name":"OLUSOLA  ADEBUNMI",
        //       "account_number":"0239582872"
        //   },
        //   "reference":"2144185",
        //   "event_data":{
        //       "data":{
        //         "paid":500,
        //         "charged":25,
        //         "settled":475,
        //         "currency":"NGN"
        //       },
        //       "status":"SUCCESSFUL",
        //       "message":"Virtual Account Payment received",
        //       "success":true
        //   },
        //   "package_id":1,
        //   "amount_info":{
        //       "paid":500,
        //       "charged":25,
        //       "settled":475,
        //       "currency":"NGN"
        //   },
        //   "destination":{
        //       "bank_code":"",
        //       "bank_name":"wema",
        //       "account_name":"CrystalPay-konnectdataOreofeAdebu",
        //       "account_email":"oreofe@gmail.com",
        //       "account_number":"7172937429",
        //       "account_reference":"wema_7172937429"
        //   },
        //   "collection_reference":"COLLECTED_20240820144515_000000214418558",
        //   "transaction_reference":"COLLECTED_20240820144515_000000214418558"
        // }';

        header('Content-Type: application/json');
        $response = file_get_contents('php://input');
        $response_decode = json_decode($response,true);
        logger('testing webhook start');
        logger($response);
      
        $can_fund = '';

        $funding_option_details = FundingOption::with('webhook_string')->where('slug','crystal_pay')->first();
 

        DB::beginTransaction();
        try{

        $check_exists = FundingWebhookPayload::where('transaction_reference',$response_decode['transaction_reference'])
        ->first();

       

        if( ($response_decode['event_data']['status'] == 'SUCCESSFUL') && (!$check_exists) ){    
            
            $email = $response_decode['destination']['account_email'];

            $user_details = User::select('id','main_wallet')->where('email',$email)->first();
            
            if($user_details){
              $created_data['funding_status'] = 'success';

              //carry out funding here::: this will change later
              $old_amount = $user_details->main_wallet;
              $amount_funded = $response_decode['event_data']['data']['settled'];
           
             //check if the amount is greater than the max set for automatic crediting
              $setting = Setting::where('field_name','max_automatic_crediting_allowed')->first();
              if($setting && $amount_funded > intval($setting->field_value) ){
                //log automatic crediting
                $can_fund = 'no';
                MaxCrystalPaymentsPendingApproval::create([
                  'user_id' => $user_details->id,
                  'amount' => $amount_funded,
                  'payment_reference' => $response_decode['transaction_reference']
                ]);
              }else{
                $new_amount = $old_amount + $amount_funded;
                //carry out funding here
                $can_fund = 'yes';
              }
                           
            }else{
              $created_data['funding_status'] = 'failed';
              $can_fund = 'no';
              logger('Cannot fund because user details not found');
            }


            $created_data['funding_slug'] = 'crystal_pay';
            $created_data['user_id'] = $user_details->id;
            $created_data['user_email'] = $email;
            $created_data['status'] = $response_decode['event_data']['status'];
            $created_data['message'] = $response_decode['event_data']['message'];
            $created_data['package_id'] = $response_decode['package_id'];
            $created_data['bank_name'] = $response_decode['destination']['bank_name'];
            $created_data['account_name'] = $response_decode['destination']['account_name'];
            $created_data['account_number'] = $response_decode['destination']['account_number'];
            $created_data['account_reference'] = $response_decode['destination']['account_reference'];
            $created_data['amount_paid'] = $response_decode['event_data']['data']['paid'];
            $created_data['amount_charged'] = $response_decode['event_data']['data']['charged'];
            $created_data['amount_settled'] = $response_decode['event_data']['data']['settled'];
            $created_data['currency'] = $response_decode['event_data']['data']['currency'];
            $created_data['collection_reference'] = $response_decode['collection_reference'];
            $created_data['transaction_reference'] = $response_decode['transaction_reference'];
            $created_data['payload_content'] = $response;

          
              $created = FundingWebhookPayload::create($created_data);

              if($can_fund == 'yes'){
                $updated = $user_details->update([
                  'main_wallet' => $new_amount
                ]);
              }else{
                $updated = true;
              }
  
              $settled_amount = $response_decode['event_data']['data']['settled'];
              $walletLog['user_id'] = $user_details->id;
              $walletLog['transaction_category'] = 'CRYSTALPAY_WALLET_FUNDING';
              $walletLog['balance_before'] = $user_details->main_wallet;
              $walletLog['balance_after'] = $user_details->main_wallet + $response_decode['event_data']['data']['settled'];
              $walletLog['transaction_id'] = $response_decode['transaction_reference'];
              $walletLog['action_by'] = 'webhook';           
              $walletLog['description'] = "Wallet of the user with the email: $email has been credited with $settled_amount via crystal pay";
              $this->log_wallet_transactions($walletLog);
              

              if( $created && $updated ){
                DB::commit();
                logger('Great... All good.');

              }else{
                logger('Crediting failed for some reasons...');
                DB::rollBack();
              }
           
        }else{
          logger('This webhook did not update wallet because its likely that the payment has been processed before');
        }
      }catch(Exception $ex){
        logger($ex->getMessage().' on line '.$ex->getLine());
        DB::rollBack();
      }

      logger('testing webhook end');
    }

    public function wallet_creditings(Request $request){
      // dd('sss');
      return view('admin.wallets_creditings.index');
    }

    public function pending_funding_transactions(Request $request){
      // dd('sss');
      $data['setting'] = Setting::where('field_name','max_automatic_crediting_allowed')->first()  ?? 'SET MAX AMOUNT';

      return view('admin.wallets_creditings.pending_creditings')->with($data);
    }

    public function fetch_crystal_pay_funding_transactions(Request $request){

          $date_from = $request->date_from ?? date('Y-m-d', strtotime('-10000 days'));
          $date_to= $request->date_to ?? date('Y-m-d');

          $reference = $request->reference ?? '';
        
          $limit = $request->limit ?? 2000;
          
          $data = FundingWebhookPayload::when(!empty($date_from) && !empty($date_to) , function ($query) use ($date_from,$date_to){
              $date_to = date('Y-m-d', strtotime('+1 day', strtotime($date_to)));
              $query->where('created_at','>=',$date_from)->where('created_at','<=',$date_to);
          })->when(!empty($reference) , function ($query) use ($reference){
            $query->where('transaction_reference',$reference);
          })->when(auth()->user()->role->role_name == 'User', function($query){
            $query->where('user_id',auth()->id());
          })
          ->latest()->limit($limit)->get();
      
          return DataTables::of($data)
          ->addIndexColumn()
          ->addColumn('DT_RowIndex',function($data){
            return $data->id;
          })
          ->addColumn('user_email',function($data){
            $first_name = $data->user->first_name  ?? 'nil';
            $last_name = $data->user->last_name  ?? 'nil';
            $phone_number = $data->user->phone_number  ?? 'nil';
            $email = $data->user->email  ?? 'nil';
            $user_details =  $first_name.'<br>'.$last_name.'<br>'.$phone_number.'<br>'.$email.'<br>';     
            return $user_details;
          })
          ->addColumn('transaction_reference',function($data){
            return $data->transaction_reference;
          })
          ->addColumn('status',function($data){
            return $data->status;
          })
          ->addColumn('funding_status',function($data){
            return $data->funding_status;
          })
          ->addColumn('message',function($data){
            return $data->message;
          })
          // ->addColumn('package_id',function($data){
          //   return $data->package_id;
          // })
          ->addColumn('bank_name',function($data){
            return $data->bank_name;
          })
          ->addColumn('account_name',function($data){
            return $data->account_name;
          })
          ->addColumn('account_number',function($data){
            return $data->account_number;
          })
          ->addColumn('account_reference',function($data){
            return $data->account_reference;
          })
          ->addColumn('amount_paid',function($data){
            return $data->amount_paid;
          })
          ->addColumn('amount_charged',function($data){
            return $data->amount_charged;
          })
          ->addColumn('amount_settled',function($data){
            return $data->amount_settled;
          })
          // ->addColumn('user_email',function($data){
          //   return $data->user_email;
          // })
          ->addColumn('created_at',function($data){
              return $data->created_at;
          }) 
          ->addColumn('action',function($data){
              $route = '#';
              // $route = route('transaction_details',$data->id);
              $actionBtn = '<a href="'.$route.'" type="button" class="hs-dropdown-toggle ti-btn ti-btn-primary" data-hs-overlay="#hs-vertically-centered-scrollable-modal'.$data->email.'">
              Details
              </a>';
              return '-';
          })
          
          ->escapeColumns([])
          ->make(true);


        
    }

    public function fetch_crystal_pay_pending_transactions(Request $request){

      $date_from = $request->date_from ?? date('Y-m-d', strtotime('-10000 days'));
      $date_to= $request->date_to ?? date('Y-m-d');

      $reference = $request->reference ?? '';
    
      $limit = $request->limit ?? 2000;
      
      $data = MaxCrystalPaymentsPendingApproval::when(!empty($date_from) && !empty($date_to) , function ($query) use ($date_from,$date_to){
          $date_to = date('Y-m-d', strtotime('+1 day', strtotime($date_to)));
          $query->where('created_at','>=',$date_from)->where('created_at','<=',$date_to);
      })->when(!empty($reference) , function ($query) use ($reference){
        $query->where('payment_reference',$reference);
      })->when(auth()->user()->role->role_name == 'User', function($query){
        $query->where('user_id',auth()->id());
      })
      ->latest()->limit($limit)->get();
  
      return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('DT_RowIndex',function($data){
        return $data->id;
      })
      ->addColumn('user',function($data){
        $first_name = $data->user->first_name  ?? 'nil';
        $last_name = $data->user->last_name  ?? 'nil';
        $phone_number = $data->user->phone_number  ?? 'nil';
        $email = $data->user->email  ?? 'nil';
        $user_details =  $first_name.'<br>'.$last_name.'<br>'.$phone_number.'<br>'.$email.'<br>';     
        return $user_details;
      })
      ->addColumn('payment_reference',function($data){
        return $data->payment_reference;
      })
      ->addColumn('amount',function($data){
        return $data->amount;
      })
      ->addColumn('status',function($data){
        return $data->status == 0 ? '<span class="badge bg-primary text-white">Pending</span>' : '<span class="badge bg-success text-white">Success</span>';
      })
      ->addColumn('date',function($data){
        return $data->created_at;
      })
      ->addColumn('action',function($data){
          $route = route('admin.wallet.crediting_details',$data->id);
          
          if($data->status == 0){
            $actionBtn = '<a href="'.$route.'" type="button" class="hs-dropdown-toggle ti-btn ti-btn-primary" data-hs-overlay="#hs-vertically-centered-scrollable-modal'.$data->email.'">
            Details
            </a>';
          }else{
            $actionBtn = '-';
          }
          
          return $actionBtn;
      }) 
      ->escapeColumns([])
      ->make(true);


    
    }

    public function complete_pending_wallet_crediting(Request $request){
        $validator = Validator::make($request->all(), [
          'pin' => 'required|digits:4|exists:users,pin',
          'user_id' => 'required|exists:users,id',
          'transaction_id' => 'required|max:255|exists:max_crystal_payments_pending_approvals,id',
          'action' => ['required',Rule::in([-1,1])],
        ]);
        

        if ($validator->stopOnFirstFailure()->fails()) {
          return redirect()->back()->withErrors($validator)->withInput();
        }

        $user_details = auth()->user();
        $pin = $request->pin;


        
        if(! $user_details){
          Session::flash('failure','Record not found');
          return redirect()->back();
        }

        if($user_details->pin != $pin){
          Session::flash('failure','Wrong PIN entered');
          return redirect()->back();
        }

        $details = MaxCrystalPaymentsPendingApproval::where('id',$request->transaction_id)->first();
      
        if($details->status == 1){
          Session::flash('failure','This crediting has already been marked as success');
          return redirect()->back();
        }

        if($details->status == -1){
          Session::flash('failure','This crediting has already been marked as failed');
          return redirect()->back();
        }

        $user_to_fund_details = User::where('id',$details->user_id)->first();

        //actual action
        if($request->action == 1){
          $new_amount = $details->amount +  $user_to_fund_details->main_wallet;
          // carry out a crediting of users wallet
          $user_to_fund_details->update([
            'main_wallet' => $new_amount
          ]);

          $details->update([
            'status' => 1
          ]);

          Session::flash('success','Successfully marked as success');
          return redirect()->back();  
          // update status to 1
        }
        else if($request->action == -1){
          // update status to -1
          $details->update([
            'status' => -1
          ]);

          Session::flash('success','Successfully marked as failed');
          return redirect()->back();

        }else{
          //this should not happen 
          Session::flash('failure','Something went wrong... Please inform Developer');
          return redirect()->back();
        }  
    }

    public function wallet_crediting_details($id){
      $data['data'] = MaxCrystalPaymentsPendingApproval::with(['user'])->where('id',$id)->first();
      // dd($data);
      return view('admin.wallets_creditings.wallet_crediting_details')->with($data);
    }

    public function index(Request $request){
        // dd('good');
        $user_id = auth()->id();
        $funding_option = FundingOption::with('bank_codes.virtual_user_account_with_bank_code')->where('activation_status',1)->first();
        // $funding_option = FundingOption::with('bank_codes')->where('activation_status',1)->first();
        $data['funding_option'] = $funding_option;

        $generated_user_virtual_accts_funding_option_id = UserVirtualAccount::where('user_id',auth()->id())->pluck('funding_option_id')->first();
        $generated_user_virtual_accts_bank_code = UserVirtualAccount::where('user_id',auth()->id())->pluck('bank_code')->first();
        $user_virtual_accounts = UserVirtualAccount::where('user_id',auth()->id())->get();
        $data['generated_user_virtual_accts_funding_option_id'] = $generated_user_virtual_accts_funding_option_id;
        $data['generated_user_virtual_accts_bank_code'] = $generated_user_virtual_accts_bank_code;
        $data['user_virtual_accounts'] = $user_virtual_accounts;
        
        // $whatsapp_support = LandingPagesSetting::where('field_name','support_whatsapp_number')->first();
        // if($whatsapp_support){
        //     $whatsapp_support_number = $whatsapp_support->field_details;
        // }else{
        //     $whatsapp_support_number = '08168509044'; //change later
        // }
        // $data['whatsapp_support'] = $whatsapp_support;
       
        // if(env('APP_NAME') == 'OresamSub'){
        //   return $data;
        // }
        
        return view('user.wallet.crystal_pay.index')->with($data);
    }
   
    public function fund_wallet(Request $request){
        // dd('good');
        $user_id = auth()->id();
        $virtual_account = UserVirtualAccount::where('user_id',$user_id)->first();
        $data['virtual_account'] = $virtual_account;
        return view('user.wallet.fund_wallet')->with($data);
    }

    public function generate_virtual_account(Request $request){
        $validator = Validator::make($request->all(), [
            'pin' => 'required|digits:4|exists:users,pin',
            // 'bvn' => 'required|max:255',
            // 'first_name' => 'required|max:255',
            // 'last_name' => 'required|max:255',
            // 'email_address' => 'required|max:255',
            'bank_code' => 'required|max:255',
            'funding_option' => 'required|exists:funding_options,id|max:255',
          ]);
          
    
          if ($validator->stopOnFirstFailure()->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
          }
    
          $user_details = auth()->user();
          $pin = $request->pin;


          
          if(! $user_details){
            Session::flash('failure','Record not found');
            return redirect()->back();
          }

          if($user_details->pin != $pin){
            Session::flash('failure','Wrong PIN entered');
            return redirect()->back();
          }


          $first_name = $user_details->first_name;
          $last_name = $user_details->last_name;
          $email = $user_details->email;
          $phone_number = $user_details->phone_number;
          // $bvn = $request->bvn;
          $bank_code = $request->bank_code;
          $funding_option_id = $request->funding_option;      

          
          //   $fetch_user_accts = UserVirtualAccount::where('user_id',$user_details->id)->where('bank_name','WEMA BANK')->first();
          $fetch_user_acct = UserVirtualAccount::where('user_id',$user_details->id)
          ->where('funding_option_id',$funding_option_id)
          ->where('bank_code',$bank_code)
          ->first();
        
          if($fetch_user_acct){
            Session::flash('failure','Sorry you already have an account generated: Account number is '.$fetch_user_acct->account_number);
            return redirect()->back();
          }

          
          //call crystalpay generate endpoint: revamp later
                $wallet_funding = FundingOption::where('id',$funding_option_id)->first();
                $api_key = $wallet_funding->api_secret_key;
                // $api_key = '1417307778664652904fd25';
            

                if($wallet_funding->slug != 'crystal_pay'){
                    Session::flash('failure','Only Crystal pay is currently being activated');
                    return redirect()->back();
                }

                $arrr = [
                    "firstname"=>$first_name,
                    "lastname"=>$last_name,
                    "email"=>$email,
                    "virtual_account_package"=>$bank_code,  
                    "bvn"=>$phone_number
                ];

                // return $arrr;
                $arrjson = json_encode($arrr);

                $curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.crystalpay.finance/business/v1/virtual-account',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                // CURLOPT_POSTFIELDS =>'{
                // "firstname": "'.$first_name.'",
                // "lastname": "'.$last_name.'",
                // "email": "'.$email.'",
                // "virtual_account_package": "'.$bank_code.'",  
                // "bvn": "'.$phone_number.'"
                // }',
                CURLOPT_POSTFIELDS =>$arrjson,
                CURLOPT_HTTPHEADER => array(
                    'secret_key: '.$api_key,
                    'Content-Type: application/json',
                    'Accept: application/json'
                ),
                ));

                $response = curl_exec($curl);

                // return $response;

                $response_dec = json_decode($response,true);

               
                
                if(  isset($response_dec['success']) 
                     && $response_dec['success'] == true
                     &&  isset($response_dec['status']) 
                     &&  $response_dec['status'] == 'Success' 
                     && isset($response_dec['data']['account_number'])
                     &&  $response_dec['data']['account_number'] != ''
                       ){
                    //success
                   
                    UserVirtualAccount::create([
                        'user_id' => $user_details->id,
                        'funding_option_id' => $funding_option_id,
                        'funding_slug' => $wallet_funding->slug,
                        'response_status' =>$response_dec['status'],
                        'bank_name' =>$response_dec['data']['bank_name'],
                        'bank_code' =>$bank_code,
                        'account_name' =>$response_dec['data']['account_name'],
                        'account_email' =>$response_dec['data']['account_email'],
                        'account_number' =>$response_dec['data']['account_number'],
                        'account_reference' => $response_dec['data']['account_reference'],
                        'bvn' => $phone_number
                    ]);

                    Session::flash('success','Virtual account was successfully generated');
                    return redirect()->back();    

                }
                
        }

}
