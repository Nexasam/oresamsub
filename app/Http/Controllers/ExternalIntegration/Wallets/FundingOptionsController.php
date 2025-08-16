<?php

namespace App\Http\Controllers\ExternalIntegration\Wallets;

use Exception;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\FundingOption;
use App\Models\UserVirtualAccount;
use App\Http\Controllers\Controller;
use App\Models\FundingWebhookPayload;
use App\Models\FundingOptionBankCodes;
use App\Models\NairaWalletTransaction;
use App\Models\DollarWalletTransaction;
use App\Http\Traits\JsonResponseWrapper;
use App\Traits\JsonResponseWrapperMobile;
use Illuminate\Support\Facades\Validator;


class FundingOptionsController extends Controller
{
 
    use JsonResponseWrapperMobile;

     //  TODO: modify these later to take other currencies
    public function fetch_naira_funding_options(Request $request){
        $funding_options = FundingOption::with(['bank_codes' => function($query){
            $query->where('visibility_status',1);
        }])
        ->where('activation_status',1)
        ->get();
        return $this->success('Wallet funding options fetched',data: $funding_options);   
    }

    public function fetch_user_naira_funding_transactions(Request $request){
  
          $user_details = User::select('id','pin')->where('id',$request->user_id)->first();
          // if($user_details->pin != $request->pin){
          //     return $this->error('Incorrect PIN', data: $request->all(), code: 403 );   
          // }

          $funding_txns = FundingWebhookPayload::where('user_id',$request->user_id)->latest()->get();
          
          return $this->success('Naira wallet fundings fetched',data: $funding_txns);   
    }

    public function fetch_naira_virtual_accounts(Request $request){
        $user_details = User::select('id','pin')->where('id',$request->user_id)->first();
    
        if($user_details->pin != $request->pin){
            return $this->error('Incorrect PIN', data: $request->all(), code: 403 );   
        }

        $funding_options = UserVirtualAccount::with('funding_option:id,funding_option_name')
        ->where('user_id',$request->user_id)
        ->get();

        return $this->success('Naira virtual accounts fetched',data: $funding_options);   
    
      }
    

    public function generate_naira_virtual_accounts(Request $request){

        // return ['ss'];
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            // 'pin' => ['required','string','regex:/^\d{4,5}$/'],
            'bank_code' => 'required|max:255|exists:funding_option_bank_codes,bank_code',
            'funding_option_id' => 'required|exists:funding_options,id',
          ]);
          
          try{

            if ($validator->stopOnFirstFailure()->fails()) {
              return $this->error($validator->errors()->first(), data: $request->all(), code: 403 );    
            }
  
            $user_details = User::select('id','pin')->where('id',$request->user_id)->first();
      
            //   if($user_details->pin != $request->pin){
            //       return $this->error('Incorrect PIN', data: $request->all(), code: 403 );   
            //   }
  
  
           $fetch_user_acct = UserVirtualAccount::where('user_id',$request->user_id)
           ->where('funding_option_id',$request->funding_option_id)
           ->where('bank_code',$request->bank_code)
           ->first();
         
           if($fetch_user_acct){
             return $this->error('Sorry you already have an account generated: Account number is '.$fetch_user_acct->account_number, data: $request->all(), code: 403 );    
           }
  
  
           $check_bank_code_of_funding = FundingOptionBankCodes::where('funding_option_id',$request->funding_option_id)
           ->where('bank_code',$request->bank_code)
           ->first();
         
           if(! $check_bank_code_of_funding){
             return $this->error('This bank code does not exist', data: $request->all(), code: 403 );    
           }
  
            $user_details = User::where('id',$request->user_id)->first();
            $pin = $request->pin;
  
            // if($user_details->pin != $pin){
            //     return $this->error('Incorrect PIN', data: $request->all(), code: 403 );          
            // }
  
  
            $first_name = $user_details->first_name;
            $last_name = $user_details->last_name;
            $email = $user_details->email;
            $phone_number = $user_details->phone_number;
            $bvn = $user_details->bvn ?? NULL;
            // $bvn = $request->bvn;
            $bank_code = $request->bank_code;
            $funding_option_id = $request->funding_option_id;      
  
  
            //call crystalpay generate endpoint: revamp later
            $wallet_funding = FundingOption::where('id',$funding_option_id)->first();
            $api_key = $wallet_funding->api_secret_key;
            // $api_key = '1417307778664652904fd25';
            $biz_bvn = $wallet_funding->biz_bvn ?? $phone_number;

        
  
            if($wallet_funding->slug != 'crystal_pay'){
                return $this->error('Only Crystal pay is currently being activated', data: $request->all(), code: 403 );    
            }
  
            //  if($bvn == NULL){
            //   return $this->error('BVN record not found', data: $request->all(), code: 403 );    
            //  }
  
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
            CURLOPT_POSTFIELDS =>'{
            "firstname": "'.$first_name.'",
            "lastname": "'.$last_name.'",
            "email": "'.$email.'",
            "virtual_account_package": "'.$bank_code.'",  
            "bvn": "'.$phone_number.'"
            }',
            CURLOPT_HTTPHEADER => array(
                'secret_key: '.$api_key,
                'Content-Type: application/json',
                'Accept: application/json'
            ),
            ));
  
            $response = curl_exec($curl);
            // logger('Wallet generation');
            // logger($response);
            // logger('Wallet generation');
  
            $response_dec = json_decode($response,true);
  
            //   return $response;
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
                    'bvn' => $biz_bvn
                ]);
  
                return $this->success('Wallet virtual accounts generated',data: $response_dec); 
            }

          }catch(Exception $exception){
             logger('Exception:' . $exception->getMessage().' on line '.$exception->getLine());  
             return $this->error('Issues generating virtual accounts');   
          }


          //    return $response_dec;
          return $this->success('Wallet virtual accounts generated.',data: $response_dec); 

    }
}
