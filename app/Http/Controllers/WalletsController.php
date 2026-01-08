<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use App\Models\User;
use App\Models\Setting;
use App\Models\SiteTemplate;
use Illuminate\Http\Request;
use App\Models\FundingOption;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use App\Models\AdminWebhookString;
use App\Models\UserVirtualAccount;
use Illuminate\Support\Facades\DB;
use App\Models\LandingPagesSetting;
use App\Models\FundingWebhookPayload;
use App\Models\FundingOptionBankCodes;
use App\Models\UserWalletFundingPromo;
use Illuminate\Support\Facades\Session;
use App\Models\UserMonnifyVirtualAccount;
use Illuminate\Support\Facades\Validator;
use App\Http\Services\WalletFundingPromoService;
use App\Traits\Dashboard\UserDashboardDataTrait;
use App\Models\MaxCrystalPaymentsPendingApproval;

class WalletsController extends Controller
{

    use UserDashboardDataTrait;
    //TODO .... let it from from DB
    // private $monnify_api_key = 'TUtfVEVTVF9QQlFQWUw0VFNDOlpIOFFLRkpYTEo3WFlaVDNFOTNGVDY3Qk04UUVSR1pC'; //sandbox
    // private $contract_code = '8553974224';  //sandbox
    // private $base_url = 'https://sandbox.monnify.com/api/'; 

    //test
    
    // private $monnify_api_key_test = 'MK_TEST_1F28BSYQ1M';

    // private $monnify_secret_key = 'EB3LNDJHVZDSUX8U4RL8L58RBV7RT472';

    // private $monnify_webhook = 'https://sandbox.monnify.com';

    // private $monnify_platform_wallet = '2942822539';

    // private $monnify_contract_code = '0034137369';

    //MK_PROD_MQMBKENYJD,  7VY7ZWS45SHTMZA98MP1K1QS20CRVSZV, https://api.monnify.com, 8020165710, 339854561147

    private $monnify_token_authorization = 'TUtfUFJPRF9NUU1CS0VOWUpEOjdWWTdaV1M0NVNIVE1aQTk4TVAxSzFRUzIwQ1JWU1pW'; //live
    private $contract_code = '339854561147'; //live
    private $base_url = 'https://api.monnify.com/api/'; 

    public function admin_total_balances(){
        $total_balances = User::sum('main_wallet');

        return response()->json([
          'balance'=> $total_balances
        ]);

        // return $total_balances;
    }

    public function securewavehook(Request $request)
    {
        try {
    
            // Raw body (best for bank/fintech webhook payloads)
            $raw = $request->getContent();
            logger($raw);
    
            // Log the webhook
            DB::table('webhook_calls')->insert([
                'name'      => 'securewave',      // name of webhook source
                'url'       => 'https://securewavengtest.com',
                'payload'   => $raw,
            ]);
            logger('securewavehook got here');
    
            return response()->json(['status' => 'ok'], 200);
    
        } catch (Throwable $e) {
    
              // Log the exception also
              // Log the webhook
              DB::table('webhook_calls')->insert([
                'name'      => 'securewave',      // name of webhook source
                'url'       => 'https://securewavengtest.com',
                'payload'   => $raw,
              ]);
    
            return response()->json(['status' => 'error'], 500);
        }
    }

    

    // public function securewavehook(Request $request){
    // try {
    //     // --- Raw payload ---
    //     $raw = $request->getContent();

    //     // --- Get headers ---
    //     $signature = $request->header('X-SecureWave-Signature');
    //     $timestamp = $request->header('X-SecureWave-Timestamp');
    //     $event     = $request->header('X-SecureWave-Event');

    //     // --- Merchant secret (stored in env or DB) ---
    //     $webhookSecret = config('services.securewave.webhook_secret1'); // or fetch per merchant

    //     // --- Verify timestamp (optional, prevents replay attacks) ---
    //     if (abs(now()->timestamp - (int) $timestamp) > 300) { // 5 minutes
    //         logger('Webhook expired: '.$timestamp);
    //         return response()->json(['status' => 'error', 'message' => 'Webhook expired'], 400);
    //     }

    //     // --- Compute expected signature ---
    //     $expected = hash_hmac('sha256', $timestamp.'.'.$raw, $webhookSecret);

    //     // --- Compare signatures ---
    //     if (!hash_equals($expected, $signature)) {
    //         logger('Invalid webhook signature', [
    //             'expected' => $expected,
    //             'received' => $signature,
    //         ]);
    //         return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 401);
    //     }

    //     // --- Log the valid webhook ---
    //     DB::table('webhook_calls')->insert([
    //         'name'    => 'securewave',
    //         'url'     => 'https://securewavengtest.com',
    //         'payload' => $raw,
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ]);

    //     logger('securewavehook verified successfully', ['event' => $event]);

    //     // --- Handle payload logic here ---
    //     $data = json_decode($raw, true);
    //     // e.g., mark transaction as paid, update balance, etc.

    //     return response()->json(['status' => 'ok'], 200);

    // } catch (Throwable $e) {
    //     logger('securewavehook exception', [
    //         'message' => $e->getMessage(),
    //         'payload' => $raw ?? null,
    //     ]);

    //     return response()->json(['status' => 'error'], 500);
    // }
    // }

    // public function securewavehook2(Request $request){
    //   try {
    //       // --- Raw payload ---
    //       $raw = $request->getContent();
  
    //       // --- Get headers ---
    //       $signature = $request->header('X-SecureWave-Signature');
    //       $timestamp = $request->header('X-SecureWave-Timestamp');
    //       $event     = $request->header('X-SecureWave-Event');
  
    //       // --- Merchant secret (stored in env or DB) ---
    //       $webhookSecret = config('services.securewave.webhook_secret2'); // or fetch per merchant
  
    //       // --- Verify timestamp (optional, prevents replay attacks) ---
    //       if (abs(now()->timestamp - (int) $timestamp) > 300) { // 5 minutes
    //           logger('Webhook expired: '.$timestamp);
    //           return response()->json(['status' => 'error', 'message' => 'Webhook expired'], 400);
    //       }
  
    //       // --- Compute expected signature ---
    //       $expected = hash_hmac('sha256', $timestamp.'.'.$raw, $webhookSecret);
  
    //       // --- Compare signatures ---
    //       if (!hash_equals($expected, $signature)) {
    //           logger('Invalid webhook signature2', [
    //               'expected' => $expected,
    //               'received' => $signature,
    //           ]);
    //           return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 401);
    //       }
  
    //       // --- Log the valid webhook ---
    //       DB::table('webhook_calls')->insert([
    //           'name'    => 'securewave',
    //           'url'     => 'https://securewavengtest.com',
    //           'payload' => $raw,
    //           'created_at' => now(),
    //           'updated_at' => now(),
    //       ]);
  
    //       logger('securewavehook verified successfully', ['event' => $event]);
  
    //       // --- Handle payload logic here ---
    //       $data = json_decode($raw, true);
    //       // e.g., mark transaction as paid, update balance, etc.
  
    //       return response()->json(['status' => 'ok'], 200);
  
    //   } catch (Throwable $e) {
    //       logger('securewavehook exception', [
    //           'message' => $e->getMessage(),
    //           'payload' => $raw ?? null,
    //       ]);
  
    //       return response()->json(['status' => 'error'], 500);
    //   }
    //   }

    


    public function xixapayhook($id,Request $request){
        header('Content-Type: application/json');
        $can_fund = '';
        $funding_option_details = FundingOption::with('webhook_string')->where('slug','xixapay')->first();
        $response = file_get_contents('php://input');
        $signatureHeader = $_SERVER['HTTP_XIXAPAY']; // xixapay signature header
        // Your Xixapay secret security key
        $secretKey = $funding_option_details->api_secret_key; // Replace with your actual secret key
        // Calculate the expected signature using HMAC-SHA256
        $calculatedSignature = hash_hmac('sha256', $response, $secretKey);
        // Compare the calculated signature with the one from the xixapay header
        if (hash_equals($calculatedSignature, $signatureHeader)) {
            // Signature is valid, proceed with handling the webhook
            logger("Webhook signature is valid!");
            // Process the payload (decode JSON, handle business logic)
            $response_decode = json_decode($response, true);
            // Example: Process the data (e.g., update database)
        } else {
             logger('ran err');
            // Signature is invalid, reject the request
            header('HTTP/1.1 400 Bad Request');
            echo "Invalid signature!";exit;
        }

        $promo_id = NULL;
        $custom_user_funding_promo_id = NULL;


        DB::beginTransaction();
        try{

        $check_exists = FundingWebhookPayload::where('transaction_reference',$response_decode['transaction_id'])
        ->first();


        // notificaton json format
        // {
        //   "notification_status": "payment_successful",
        //   "transaction_id": "xxx",
        //   "amount_paid": 100,
        //   "settlement_amount": 99.5,
        //   "settlement_fee": 0.5,
        //   "transaction_status": "success",
        //   "sender": {
        //     "name": "A D E LIMITED",
        //     "account_number": "****4290",
        //     "bank": "HYDROGEN"
        //   },
        //   "receiver": {
        //     "name": "adex-Abd(xixapay)",
        //     "account_number": "667985",
        //     "bank": "PalmPay"
        //   },
        //   "customer": {
        //     "name": "adex dev",
        //     "email": "adexplug@gmail.com,
        //     "phone": null,
        //     "customer_id": "xxx"
        //   },
        //   "description": "Your payment has been successfully processed.",
        //   "timestamp": "2024-11-22T13:00:04.256092Z"
        // }

      

        if( ($response_decode['notification_status'] == 'payment_successful') && (!$check_exists) ){    
            
            $email = $response_decode['customer']['email'];
            $user_details = User::select('id','main_wallet')->where('email',$email)->first();
            
            if($user_details){
              $created_data['funding_status'] = 'success';

              //carry out funding here::: this will change later
              $old_amount = $user_details->main_wallet;
              $new_amount = $user_details->main_wallet;
              $amount_funded = $response_decode['settlement_amount'];
          
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
              // logger('Cannot fund because user details not found');exit;
            }

            $paid_amount = $response_decode['amount_paid'];
            $bank_name = $response_decode['receiver']['bank'];
            $get_charges = FundingOptionBankCodes::where('bank_name','like','%'.$bank_name.'%')
                            ->where('funding_option_id',$funding_option_details->id)
                            ->first();
            if($get_charges){
              $rate_type = $get_charges->rate_category;
              if($rate_type == 'Flat'){
                $charges = $get_charges->bank_charges;
                $amount_to_fund_user = $paid_amount - $charges;
              }else{
                $charges1 = $get_charges->bank_charges;
                $capped_value = $get_charges->capped_at;
                $charges = ceil(($charges1/100) * $paid_amount);
                if($charges > $capped_value){
                  $charges = $capped_value;
                }
                $amount_to_fund_user = $paid_amount - $charges;
              }
            }else{
                //use crystalpay default settings
                $charges = $response_decode['event_data']['data']['charged'];
                $amount_to_fund_user = $response_decode['event_data']['data']['settled'];
            }


            //inacase there is a custom funding promo: think of DRY
            //incase there is a promo
            $user_wallet_funding_promo  = UserWalletFundingPromo::where('user_id',$user_details->id)
            ->where('funding_option_id',$funding_option_details->id)
            ->where('status',1)
            ->first();

            if($user_wallet_funding_promo){
              //custom funding exists
              $daaat['promo_discount_category'] = $user_wallet_funding_promo->rate_category;
              $daaat['promo_discount_percentage_cap'] = $user_wallet_funding_promo->capped_at;
              $daaat['funding_amount'] = $paid_amount;
              $daaat['promo_value'] = $user_wallet_funding_promo->value;
              $daaat['funding_option_id'] = $funding_option_details->id;
              $amount_to_fund_user = (new WalletFundingPromoService())->get_amount_to_fund_user($daaat);
              // logger('custom promo.: '.$amount_to_fund_user);
              $custom_user_funding_promo_id = $user_wallet_funding_promo->id;
              //custom funding promo 
            }

          


            //incase there is a general promo: think of DRY
            $daat['user'] = $user_details;
            $daat['funding_amount'] = $paid_amount;
            $daat['funding_option_id'] = $funding_option_details->id;
            $check_promo = (new WalletFundingPromoService())->apply_funding_promo($daat);
            if($check_promo['status'] == 1){
              // logger('general promo: '.$check_promo['actual_amount_to_fund_user']);
              $amount_to_fund_user = $check_promo['actual_amount_to_fund_user'];
              $promo_id = $check_promo['promo_id'];
            }
            //general promo supercedes custom


            $created_data['funding_slug'] = 'xixapay';
            $created_data['user_id'] = $user_details->id;
            $created_data['wallet_funding_promo_id'] = $promo_id;
            $created_data['custom_wallet_funding_promo_id'] = $custom_user_funding_promo_id;
            $created_data['user_email'] = $response_decode['customer']['email'];
            $created_data['status'] = $response_decode['transaction_status'];
            $created_data['message'] = $response_decode['description'];
            $created_data['package_id'] = $get_charges['bank_code'];
            $created_data['bank_name'] = $bank_name;
            $created_data['account_name'] = $response_decode['receiver']['name'];
            $created_data['account_number'] = $response_decode['receiver']['account_number'];
            $created_data['account_reference'] = $response_decode['transaction_id'];
            $created_data['amount_paid'] = $response_decode['amount_paid'];
            $created_data['amount_charged'] = $charges;
            $created_data['amount_settled'] = $amount_to_fund_user;
            $created_data['currency'] = 'NGN';
            $created_data['collection_reference'] = $response_decode['transaction_id'];
            $created_data['transaction_reference'] = $response_decode['transaction_id'];
            $created_data['payload_content'] = $response;
            $created = FundingWebhookPayload::create($created_data);
            $new_amount = $old_amount + $amount_to_fund_user;

            if($can_fund == 'yes'){
              $updated = $user_details->update([
                'main_wallet' => $new_amount
              ]);
            }else{
              $updated = true;
            }

              $walletLog['user_id'] = $user_details->id;
              $walletLog['transaction_category'] = 'XIXPAY_WALLET_FUNDING';
              $walletLog['balance_before'] = $old_amount;
              $walletLog['balance_after'] = $new_amount;
              $walletLog['transaction_id'] = $response_decode['transaction_id'];
              $walletLog['action_by'] = 'webhook';           
              $walletLog['description'] = "Wallet of the user with the email: $email has been credited with $amount_to_fund_user via crystal pay";
              $this->log_wallet_transactions($walletLog);
              
              if( $created && $updated ){
                DB::commit();
                // logger('Great... All good.');

              }else{
                logger('Crediting failed for some reasons...');
                DB::rollBack();
              }
          
        }else{
          logger('This webhook did not update wallet because its likely that the payment has been processed before');
        }
      }catch(Exception $ex){
        logger(
          $ex->getMessage() . 
          ' on line ' . $ex->getLine() . 
          ' in ' . $ex->getFile() . 
          ' [Thrown in class: ' . get_class($ex) . ']'
      );
        DB::rollBack();
      }

      // logger('testing webhook end');
    }
   
    public function webhook($id,Request $request){
       
      header('Content-Type: application/json');
      $response = file_get_contents('php://input');
      $response_decode = json_decode($response,true);
      logger("Crystalpay webhook headers: ". json_encode($request->headers->all()) );

      
    
      $can_fund = '';

      $funding_option_details = FundingOption::with('webhook_string')->where('slug','crystal_pay')->first();

      $promo_id = NULL;
      $custom_user_funding_promo_id = NULL;


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
            $new_amount = $user_details->main_wallet;
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
            logger('Cannot fund because user details not found');exit;
          }

          $paid_amount = $response_decode['event_data']['data']['paid'];
          $package_id = $response_decode['package_id'];
          $get_charges = FundingOptionBankCodes::where('bank_code',$package_id)->first();
          if($get_charges){
            $rate_type = $get_charges->rate_category;
            if($rate_type == 'Flat'){
              $charges = $get_charges->bank_charges;
              $amount_to_fund_user = $paid_amount - $charges;
            }else{
              $charges1 = $get_charges->bank_charges;
              $capped_value = $get_charges->capped_at;
              $charges = ceil(($charges1/100) * $paid_amount);
              if($charges > $capped_value){
                $charges = $capped_value;
              }
              $amount_to_fund_user = $paid_amount - $charges;
            }
          }else{
              //use crystalpay default settings
              $charges = $response_decode['event_data']['data']['charged'];
              $amount_to_fund_user = $response_decode['event_data']['data']['settled'];
          }


          //inacase there is a custom funding promo: think of DRY
          //incase there is a promo
          $user_wallet_funding_promo  = UserWalletFundingPromo::where('user_id',$user_details->id)
          ->where('funding_option_id',$funding_option_details->id)
          ->where('status',1)
          ->first();

          if($user_wallet_funding_promo){
            //custom funding exists
            $daaat['promo_discount_category'] = $user_wallet_funding_promo->rate_category;
            $daaat['promo_discount_percentage_cap'] = $user_wallet_funding_promo->capped_at;
            $daaat['funding_amount'] = $paid_amount;
            $daaat['promo_value'] = $user_wallet_funding_promo->value;
            $daaat['funding_option_id'] = $funding_option_details->id;
            $amount_to_fund_user = (new WalletFundingPromoService())->get_amount_to_fund_user($daaat);
            logger('custom promo.: '.$amount_to_fund_user);
            $custom_user_funding_promo_id = $user_wallet_funding_promo->id;
            //custom funding promo 
          }

         


          //incase there is a general promo: think of DRY
          $daat['user'] = $user_details;
          $daat['funding_amount'] = $paid_amount;
          $daat['funding_option_id'] = $funding_option_details->id;
          $check_promo = (new WalletFundingPromoService())->apply_funding_promo($daat);
          if($check_promo['status'] == 1){
            // logger('general promo: '.$check_promo['actual_amount_to_fund_user']);
            $amount_to_fund_user = $check_promo['actual_amount_to_fund_user'];
            $promo_id = $check_promo['promo_id'];
          }
          //general promo supercedes custom


          $created_data['funding_slug'] = 'crystal_pay';
          $created_data['user_id'] = $user_details->id;
          $created_data['wallet_funding_promo_id'] = $promo_id;
          $created_data['custom_wallet_funding_promo_id'] = $custom_user_funding_promo_id;
          $created_data['user_email'] = $email;
          $created_data['status'] = $response_decode['event_data']['status'];
          $created_data['message'] = $response_decode['event_data']['message'];
          $created_data['package_id'] = $response_decode['package_id'];
          $created_data['bank_name'] = $response_decode['destination']['bank_name'];
          $created_data['account_name'] = $response_decode['destination']['account_name'];
          $created_data['account_number'] = $response_decode['destination']['account_number'];
          $created_data['account_reference'] = $response_decode['destination']['account_reference'];
          $created_data['amount_paid'] = $response_decode['event_data']['data']['paid'];
          $created_data['amount_charged'] = $charges;
          $created_data['amount_settled'] = $amount_to_fund_user;
          $created_data['currency'] = $response_decode['event_data']['data']['currency'];
          $created_data['collection_reference'] = $response_decode['collection_reference'];
          $created_data['transaction_reference'] = $response_decode['transaction_reference'];
          $created_data['payload_content'] = $response;
          $created = FundingWebhookPayload::create($created_data);
          $new_amount = $old_amount + $amount_to_fund_user;

          if($can_fund == 'yes'){
            $updated = $user_details->update([
              'main_wallet' => $new_amount
            ]);
          }else{
            $updated = true;
          }

            $walletLog['user_id'] = $user_details->id;
            $walletLog['transaction_category'] = 'CRYSTALPAY_WALLET_FUNDING';
            $walletLog['balance_before'] = $old_amount;
            $walletLog['balance_after'] = $new_amount;
            $walletLog['transaction_id'] = $response_decode['transaction_reference'];
            $walletLog['action_by'] = 'webhook';           
            $walletLog['description'] = "Wallet of the user with the email: $email has been credited with $amount_to_fund_user via crystal pay";
            $this->log_wallet_transactions($walletLog);
            
            if( $created && $updated ){
              DB::commit();
              logger('Great... All good.');

            }else{
              // logger('Crediting failed for some reasons...');
              DB::rollBack();
            }
         
      }else{
        // logger('This webhook did not update wallet because its likely that the payment has been processed before');
      }
      }catch(Exception $ex){
        logger(
          $ex->getMessage() . 
          ' on line ' . $ex->getLine() . 
          ' in ' . $ex->getFile() . 
          ' [Thrown in class: ' . get_class($ex) . ']'
      );
        DB::rollBack();
      }

      logger('testing webhook end');
    }

    public function webhook22($id,Request $request){
       
      header('Content-Type: application/json');
      $response = file_get_contents('php://input');
      $response_decode = json_decode($response,true);
      
    
      $can_fund = '';

      $funding_option_details = FundingOption::with('webhook_string')->where('slug','crystal_pay')->first();

      $promo_id = NULL;
      $custom_user_funding_promo_id = NULL;


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
            $new_amount = $user_details->main_wallet;
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
            logger('Cannot fund because user details not found');exit;
          }

          $paid_amount = $response_decode['event_data']['data']['paid'];
          $package_id = $response_decode['package_id'];
          $get_charges = FundingOptionBankCodes::where('bank_code',$package_id)->first();
          if($get_charges){
            $rate_type = $get_charges->rate_category;
            if($rate_type == 'Flat'){
              $charges = $get_charges->bank_charges;
              $amount_to_fund_user = $paid_amount - $charges;
            }else{
              $charges1 = $get_charges->bank_charges;
              $capped_value = $get_charges->capped_at;
              $charges = ceil(($charges1/100) * $paid_amount);
              if($charges > $capped_value){
                $charges = $capped_value;
              }
              $amount_to_fund_user = $paid_amount - $charges;
            }
          }else{
              //use crystalpay default settings
              $charges = $response_decode['event_data']['data']['charged'];
              $amount_to_fund_user = $response_decode['event_data']['data']['settled'];
          }


          //inacase there is a custom funding promo: think of DRY
          //incase there is a promo
          $user_wallet_funding_promo  = UserWalletFundingPromo::where('user_id',$user_details->id)
          ->where('funding_option_id',$funding_option_details->id)
          ->where('status',1)
          ->first();

          if($user_wallet_funding_promo){
            //custom funding exists
            $daaat['promo_discount_category'] = $user_wallet_funding_promo->rate_category;
            $daaat['promo_discount_percentage_cap'] = $user_wallet_funding_promo->capped_at;
            $daaat['funding_amount'] = $paid_amount;
            $daaat['promo_value'] = $user_wallet_funding_promo->value;
            $daaat['funding_option_id'] = $funding_option_details->id;
            $amount_to_fund_user = (new WalletFundingPromoService())->get_amount_to_fund_user($daaat);
            logger('custom promo.: '.$amount_to_fund_user);
            $custom_user_funding_promo_id = $user_wallet_funding_promo->id;
            //custom funding promo 
          }

         


          //incase there is a general promo: think of DRY
          $daat['user'] = $user_details;
          $daat['funding_amount'] = $paid_amount;
          $daat['funding_option_id'] = $funding_option_details->id;
          $check_promo = (new WalletFundingPromoService())->apply_funding_promo($daat);
          if($check_promo['status'] == 1){
            logger('general promo: '.$check_promo['actual_amount_to_fund_user']);
            $amount_to_fund_user = $check_promo['actual_amount_to_fund_user'];
            $promo_id = $check_promo['promo_id'];
          }
          //general promo supercedes custom


          $created_data['funding_slug'] = 'crystal_pay';
          $created_data['user_id'] = $user_details->id;
          $created_data['wallet_funding_promo_id'] = $promo_id;
          $created_data['custom_wallet_funding_promo_id'] = $custom_user_funding_promo_id;
          $created_data['user_email'] = $email;
          $created_data['status'] = $response_decode['event_data']['status'];
          $created_data['message'] = $response_decode['event_data']['message'];
          $created_data['package_id'] = $response_decode['package_id'];
          $created_data['bank_name'] = $response_decode['destination']['bank_name'];
          $created_data['account_name'] = $response_decode['destination']['account_name'];
          $created_data['account_number'] = $response_decode['destination']['account_number'];
          $created_data['account_reference'] = $response_decode['destination']['account_reference'];
          $created_data['amount_paid'] = $response_decode['event_data']['data']['paid'];
          $created_data['amount_charged'] = $charges;
          $created_data['amount_settled'] = $amount_to_fund_user;
          $created_data['currency'] = $response_decode['event_data']['data']['currency'];
          $created_data['collection_reference'] = $response_decode['collection_reference'];
          $created_data['transaction_reference'] = $response_decode['transaction_reference'];
          $created_data['payload_content'] = $response;
          $created = FundingWebhookPayload::create($created_data);
          $new_amount = $old_amount + $amount_to_fund_user;

          if($can_fund == 'yes'){
            $updated = $user_details->update([
              'main_wallet' => $new_amount
            ]);
          }else{
            $updated = true;
          }

            $walletLog['user_id'] = $user_details->id;
            $walletLog['transaction_category'] = 'CRYSTALPAY_WALLET_FUNDING';
            $walletLog['balance_before'] = $old_amount;
            $walletLog['balance_after'] = $new_amount;
            $walletLog['transaction_id'] = $response_decode['transaction_reference'];
            $walletLog['action_by'] = 'webhook';           
            $walletLog['description'] = "Wallet of the user with the email: $email has been credited with $amount_to_fund_user via crystal pay";
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
        logger(
          $ex->getMessage() . 
          ' on line ' . $ex->getLine() . 
          ' in ' . $ex->getFile() . 
          ' [Thrown in class: ' . get_class($ex) . ']'
      );
        DB::rollBack();
      }

      logger('testing webhook end');
    }

 



    public function wallet_creditings(Request $request){
      // dd('sss');
      return view('admin.wallets_creditings.index');
    }

    public function authenticate_with_monnify(){     
      $curl = curl_init();
      curl_setopt_array($curl, array(
      CURLOPT_URL => $this->base_url.'v1/auth/login',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_HTTPHEADER => array(
      'Accept: application/json',
      'Content-Type: application/json',
      'Authorization: Basic '.$this->monnify_token_authorization
      ),
      ));
      $response = curl_exec($curl);
      curl_close($curl);
      $response_dec = json_decode($response,true);
      logger('Monnify Auth: '.$response);

      if( isset($response_dec['requestSuccessful'])
      && $response_dec['requestSuccessful'] 
      && isset($response_dec['responseMessage'])
      && $response_dec['responseMessage'] == 'success'
      ){
          return [
              'status' => 1,
              'message' => $response_dec['responseBody']['accessToken']
          ];
      }

      return [
          'status' => -1,
          'message' => $response_dec['responseMessage']
      ];
      // dd($response);
    }


    public function verify_monnify_account_via_nin(Request $request){
     
      $validator = Validator::make($request->all(), [
        'nin' => 'required',
        'nin_fullname' => 'required',
        'nin_phone_number' => 'required',
        'pin' => ['required','string','regex:/^\d{4,5}$/'],
      ]);
    
      if ($validator->stopOnFirstFailure()->fails()) {
          return response()->json(['status'=>'-1', 'message'=>$validator->errors()->first(),'data' => $request->all() ]);
      }

   

      $nin = $request->nin;
      $nin_fullname = $request->nin_fullname;
      $nin_phone_number = $request->nin_phone_number;
     

      $user_id = auth()->id();
      $registered_phone = auth()->user()->phone;

      $authenticate_with_monnify = $this->authenticate_with_monnify();
      if($authenticate_with_monnify['status'] !=  1){
          Session::flash('failure','Authentication failed');
          return redirect()->back();   
      }

      DB::beginTransaction();

      try{
              $authenticate = $this->authenticate_with_monnify();

            
              $nin_phone_number_arr = [];

              if( $authenticate['status'] != 1){
                Session::flash('failed', 'Authentication failed');
                return redirect()->back(); 
              }

              $token = $authenticate['message']; 

            
              //before calling verification endpoint, check if its been called before:
              $user_details = User::where('id',$user_id)->first();
              if(! $user_details){
                  logger('Nin verification: User not found: userid: '. $user_id);
                  Session::flash('failed', 'User record not found');
                  return redirect()->back(); 
              }

              if($user_details->nin_json != NULL){
                  //means it has been called before
                  // logger(message: 'THIS ONE RAN');
                  $nin_response_json = $user_details->nin_json;
                  $response_decodde = json_decode($nin_response_json,true);
                  $responseMessage = $response_decodde['responseMessage'] ;   


                  $verifiedNin = $response_decodde['responseBody']['nin'] ?? NULL;
                  $verifiedNinLastName = $response_decodde['responseBody']['lastName'] ?? NULL;
                  $verifiedNinFirstName = $response_decodde['responseBody']['firstName'] ?? NULL;
                  $verifiedNinMobileNumber= $response_decodde['responseBody']['mobileNumber'] ?? NULL;

                  if( isset($responseMessage) && $responseMessage == 'success' ){

                      if( strlen($nin_phone_number) == 11){
                          $extracted_number = substr($nin_phone_number,1);
                          $phone_range1 = $nin_phone_number;
                          $phone_range2 = "234$extracted_number";
                          $phone_range3 = "+234$extracted_number";
                          $nin_phone_number_arr = [ $phone_range1, $phone_range2, $phone_range3 ];
                       }
                       if( strlen($nin_phone_number) == 13){
                           $extracted_number = substr($nin_phone_number,3);
                           $phone_range1 = "0$extracted_number";
                           $phone_range2 = "234$extracted_number";
                           $phone_range3 = "+234$extracted_number";
                           $nin_phone_number_arr = [ $phone_range1, $phone_range2, $phone_range3 ];
                       }
                       if( strlen($nin_phone_number) == 14){
                           $extracted_number = substr($nin_phone_number,4);
                           $phone_range1 = "0$extracted_number";
                           $phone_range2 = "234$extracted_number";
                           $phone_range3 = "+234$extracted_number";
                           $nin_phone_number_arr = [ $phone_range1, $phone_range2, $phone_range3 ];
                       }
  
                      //  logger('userid: '.$user_id);
                      //  logger('inputtednin: '.$nin_phone_number);
                      //  logger('verifiednin: '.$verifiedNinMobileNumber);
                       logger(json_encode($nin_phone_number_arr));
  
                       //ask if to stop users if their phone number on nin does not match their registered phone number
                      if(  $verifiedNinMobileNumber == 'Not Available' ||  in_array($verifiedNinMobileNumber,$nin_phone_number_arr) ){
                          
                        $updatee = User::where('id',$user_id)->update([
                          'nin'  => $nin,
                          'is_nin_verified'  => 1
                        ]);

                        DB::commit();
                        Session::flash('success', 'NIN successfully updated');
                        return redirect()->back(); 
                      
                      }else{
                          //  logger('THIS ONE RAAAAAN - neg');
                           //bad status - DONT VERIFY
                           DB::commit();
                           Session::flash('failure', 'Phone number does not match NIN registered phone number: '. $verifiedNinMobileNumber);
                           return redirect()->back(); 
                        
                       }
  
                  }
                  
              }else{
                  //call the endpoints
                  $curl = curl_init();
                  curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://api.monnify.com/api/v1/vas/nin-details',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS =>'{
                      "nin":"'.$nin.'"
                  }',
                  CURLOPT_HTTPHEADER => array(
                      'Content-Type: application/json',
                      'Authorization: Bearer '.$token,
                  ),
                  ));
  
                  $response = curl_exec($curl);
  
                  curl_close($curl);
                    
                  $response_decode = json_decode($response,true);
      
                  $responseMessage = $response_decode['responseMessage'] ;   
                  
                  
      
                  if( isset($responseMessage) && $responseMessage == 'success' ){
      
                          $verifiedNin = $response_decode['responseBody']['nin'] ?? NULL;
                          $verifiedNinLastName = $response_decode['responseBody']['lastName'] ?? NULL;
                          $verifiedNinFirstName = $response_decode['responseBody']['firstName'] ?? NULL;
                          $verifiedNinMobileNumber= $response_decode['responseBody']['mobileNumber'] ?? NULL;
  
                          if( strlen($nin_phone_number) == 11){
                             $extracted_number = substr($nin_phone_number,1);
                             $phone_range1 = $nin_phone_number;
                             $phone_range2 = "234$extracted_number";
                             $phone_range3 = "+234$extracted_number";
                             $nin_phone_number_arr = [ $phone_range1, $phone_range2, $phone_range3 ];
                          }
                          if( strlen($nin_phone_number) == 13){
                              $extracted_number = substr($nin_phone_number,3);
                              $phone_range1 = "0$extracted_number";
                              $phone_range2 = "234$extracted_number";
                              $phone_range3 = "+234$extracted_number";
                              $nin_phone_number_arr = [ $phone_range1, $phone_range2, $phone_range3 ];
                          }
                          if( strlen($nin_phone_number) == 14){
                              $extracted_number = substr($nin_phone_number,4);
                              $phone_range1 = "0$extracted_number";
                              $phone_range2 = "234$extracted_number";
                              $phone_range3 = "+234$extracted_number";
                              $nin_phone_number_arr = [ $phone_range1, $phone_range2, $phone_range3 ];
                          }
  
                          // logger('NIN Phone: '.$nin_phone_number);
                          // logger(json_encode($nin_phone_number_arr));
  
                          //ask if to stop users if their phone number on nin does not match their supplied phone number
                         if(  $verifiedNinMobileNumber == 'Not Available' || in_array($verifiedNinMobileNumber,$nin_phone_number_arr) ){
                              //good status
                              //deduct the fee if available         
                              User::where('id',$user_id)
                              ->update([
                                  'nin_json' => $response,
                                  'nin'  => $nin,
                                  'is_nin_verified'  => 1
                              ]);
      
                              DB::commit();
                              Session::flash('success', 'NIN verification was successful');
                              return redirect()->back(); 
                         }else{
                              //
                              //bad status - DONT VERIFY
                              //user table should now have the status logged
                              User::where('id',$user_id)
                              ->update([
                                  'nin_json' => $response,
                                  'nin'  => $nin,
                              ]);
  
                              DB::commit();

                              Session::flash('failure', 'Phone number does not match NIN registered phone number: '. $verifiedNinMobileNumber);
                              return redirect()->back(); 
                          }
      
                    }else{
                      Session::flash('failure',$responseMessage);
                      return redirect()->back(); 
                    } 
              }
   
      }catch(Exception $e){
          DB::rollBack();
          logger($e->getMessage());
          Session::flash('failure','Ops...Verification failed');
          return redirect()->back(); 
      }

    }

   
    public function verify_monnify_account_via_bvn(Request $request){ 

      $validator = Validator::make($request->all(), [
        'bank_code' => 'required',
        'bvn' => 'required',
        'account_number' => 'required',
        'pin' => ['required','string','regex:/^\d{4,5}$/'],
      ]);
    
      if ($validator->stopOnFirstFailure()->fails()) {
          return response()->json(['status'=>'-1', 'message'=>$validator->errors()->first(),'data' => $request->all() ]);
      }

      $bvn = $request->bvn;
      $bvn_account_number = $request->account_number;
      $bank_code = $request->bank_code;
      // dd($bvn.'------'.$bvn_account_number.'-----------'.$bank_code);

      $user_id = auth()->id();
      $authenticate_with_monnify = $this->authenticate_with_monnify();
      if($authenticate_with_monnify['status'] !=  1){
          Session::flash('failure','Authentication failed');
          return redirect()->back();   
      }

      DB::beginTransaction();
        try{
          

                $token = $authenticate_with_monnify['message']; 
                $curl = curl_init();
    
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.monnify.com/api/v1/vas/bvn-account-match',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>'{
                      "bankCode":"'.$bank_code.'",
                      "accountNumber":"'.$bvn_account_number.'",
                      "bvn":"'.$bvn.'"
                  }',
                    CURLOPT_HTTPHEADER => array(
                      'Content-Type: application/json',
                      'Authorization: Bearer '.$token,
                    ),
                  ));
                  
                  $response = curl_exec($curl);
                  // logger('Monnify BVN: ');
                  // logger($response);
                  // logger('Monnify BVN end:');
                  
                  curl_close($curl);
                  
                  $response_decode = json_decode($response,true);
    
                  $responseMessage = $response_decode['responseMessage'];          
    
                  if( isset($responseMessage) && $responseMessage == 'success' ){
                        $verifiedBvn = $response_decode['responseBody']['bvn'];
                        $verifiedAccountNumber = $response_decode['responseBody']['accountNumber'];
                        $verifiedAccountName = $response_decode['responseBody']['accountName'];
                        $verifiedMatchStatus = $response_decode['responseBody']['matchStatus'];
                        $verifiedMatchPercentage = $response_decode['responseBody']['matchPercentage'];
                        $verifiedMatchPercentage = $response_decode['responseBody']['matchPercentage'];
                
                        
                      if(  $verifiedBvn == $bvn && ($verifiedMatchStatus == 'FULL_MATCH'  || $verifiedMatchStatus == 'PARTIAL_MATCH')  ){
                            //good status
                            User::where('id',$user_id)
                            ->update([
                                'bvn_json'  => $response,
                                'is_bvn_verified'  => 1,
                                'bvn'  => $bvn
                            ]);
    
                            DB::commit();
                            Session::flash('success', 'BVN succesfully verified');
                            return redirect()->back();

                      }else{
                            // logger('Monnify bvn verification failed: '.$responseMessage);
                            DB::commit();
                            Session::flash('failure', $responseMessage);
                            return redirect()->back();   
                      }
    
                  }else{
                    Session::flash('failure', $responseMessage);
                    return redirect()->back();   
                  }   
            
    
        }catch(Exception $e){
            DB::rollBack();
            logger($e->getMessage());
            Session::flash('failure', 'Ops... Something went wrong');
            return redirect()->back();            
        }
    }

    public function generate_monnify_virtual_accounts(Request $request){
      // $validator = Validator::make($request->all(), [
      //   'pin' => 'required|'string','regex:/^\d{4,5}$/|exists:users,pin'
      // ]);
      
      // if ($validator->stopOnFirstFailure()->fails()) {
      //   return redirect()->back()->withErrors($validator)->withInput();
      // }

        // $validator = Validator::make($request->all(), [
        //     'user_id' => 'required|exists:users,id'
        // ]);

        // if ($validator->stopOnFirstFailure()->fails()) { 
        //     return redirect()->back()->withErrors($validator)->withInput();   
        // }

        $user_id = auth()->id();

        $authenticate_with_monnify = $this->authenticate_with_monnify();
        if($authenticate_with_monnify['status'] !=  1){
            Session::flash('failure','Authentication failed');
            return redirect()->back();   
        }

        $check_existing_virtual_accounts = UserMonnifyVirtualAccount::where('user_id',$user_id)->get();
        if(count($check_existing_virtual_accounts)  >= 1 ){
                Session::flash('failure','You seem to have virtual accounts already generated');
                return redirect()->back();     
        }

        $token = $authenticate_with_monnify['message'];

        $get_user_details = User::where('id',auth()->id())->first();

        if($get_user_details->is_bvn_verified == 0 && $get_user_details->is_nin_verified == 0 ){
            Session::flash('failure','BVN or NIN must be verified');
            return redirect()->back();       
        }

        if($get_user_details->user_monnify_reference == NULL ){ 
            $user_monnify_reference = $user_id;
            // Session::flash('failure','Monnify reference cannot be null');
            // return redirect()->back();    
        }


        $user_mon_ref = $get_user_details->user_monnify_reference ?? $user_monnify_reference;

        DB::beginTransaction();
        try{

        if($get_user_details->is_bvn_verified == 1 && $get_user_details->is_nin_verified == 0 ){
            $array = [
                "accountReference"=>$user_mon_ref,
                "accountName"=>$get_user_details->first_name.' '.$get_user_details->last_name,
                "currencyCode"=>"NGN",
                "contractCode"=>$this->contract_code,
                "customerEmail"=>$get_user_details->email,
                "customerName"=>$get_user_details->first_name.' '.$get_user_details->last_name,
                "bvn"=>$get_user_details->bvn,
                "getAllAvailableBanks"=>true
            ];
        }

        if($get_user_details->is_bvn_verified == 0 && $get_user_details->is_nin_verified == 1 ){
            $array = [
                "accountReference"=>$user_mon_ref,
                "accountName"=>$get_user_details->first_name.' '.$get_user_details->last_name,
                "currencyCode"=>"NGN",
                "contractCode"=>$this->contract_code,
                "customerEmail"=>$get_user_details->email,
                "customerName"=>$get_user_details->first_name.' '.$get_user_details->last_name,
                "nin"=>$get_user_details->nin,
                "getAllAvailableBanks"=>true
            ];
        }

        if($get_user_details->is_bvn_verified == 1 && $get_user_details->is_nin_verified == 1 ){
            $array = [
                "accountReference"=>$user_mon_ref,
                "accountName"=>$get_user_details->first_name.' '.$get_user_details->last_name,
                "currencyCode"=>"NGN",
                "contractCode"=>$this->contract_code,
                "customerEmail"=>$get_user_details->email,
                "customerName"=>$get_user_details->first_name.' '.$get_user_details->last_name,
                "bvn"=>$get_user_details->bvn,
                "nin"=>$get_user_details->nin,
                "getAllAvailableBanks"=>true
            ];
        }
      
        $json_encoded_array = json_encode($array);
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->base_url.'v2/bank-transfer/reserved-accounts',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$json_encoded_array,
        CURLOPT_HTTPHEADER => array(
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Bearer '.$token
        ),
        ));

        $response = curl_exec($curl);
        // logger('Monnify Virtual Accounts Generated: '. $response);

        $response_dec = json_decode($response,true);

        if( isset($response_dec['requestSuccessful'])
        && $response_dec['requestSuccessful'] 
        && isset($response_dec['responseMessage'])
        && $response_dec['responseMessage'] == 'success' ){
            //here, generate and add the virtual accounts
            $accounts = $response_dec['responseBody']['accounts'];
            foreach($accounts as $account){
                $bank_code = $account['bankCode'];
                $bank_name = $account['bankName'];
                $account_name = $account['accountName'];
                $account_number = $account['accountNumber'];
                UserMonnifyVirtualAccount::updateOrCreate([
                    'bank_code' => $bank_code,
                    'user_id' => $user_id,
                ],[
                    'bank_name' => $bank_name,
                    'account_name' => $account_name,
                    'account_number' => $account_number,
                    'user_monnify_reference' => $user_mon_ref
                ]);
            }
        }else{
          Session::flash('failure','Error generating virtual accounts');
          return redirect()->back(); 
        }
        curl_close($curl);   
        DB::commit();
        Session::flash('success','Successfully generated virtual accounts');
        return redirect()->back();  
    }catch(Exception $ex){
        DB::rollback();
        logger('Error occurred. :'. $ex->getMessage().' on line: '.$ex->getLine());
        Session::flash('failure','An Error occurred. Please try again');
        return redirect()->back();     
    }      


    }

    public function pending_funding_transactions(Request $request){
      // dd('sss');
      $data['setting'] = Setting::where('field_name','max_automatic_crediting_allowed')->first()  ?? 'SET MAX AMOUNT';

      return view('admin.wallets_creditings.pending_creditings')->with($data);
    }

    public function fetch_crystal_pay_funding_transactions(Request $request){

          // date('Y-m-d', strtotime('-2 days'))
          $date_from = $request->date_from ?? '';
          
          // date('Y-m-d')
          $date_to= $request->date_to ?? '';

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
            $res = $data->transaction_reference;
            $promo_id = $data->wallet_funding_promo_id;
            $promo_bonus = $data->amount_settled - $data->amount_paid;

            // if($promo_id != NULL && $promo_bonus > 0){
            //   $res .= "<br>Promo bonus of ₦" . number_format($promo_bonus, 2) . " enjoyed 🎉";
            // }

            if ($promo_id != NULL && $promo_bonus > 0 || ($promo_bonus == 0)) {
              if($promo_bonus == 0){
                $formatted_bonus = number_format($promo_bonus, 2);
                $res .= "<br><div style='
                    background: #d1fae5;
                    border: 1px solid #10b981;
                    color: #065f46;
                    padding: 8px 14px;
                    margin-top: 10px;
                    border-radius: 8px;
                    font-size: 14px;
                    font-weight: 500;
                    display: inline-block;
                '>
                    🎉 ".__('messages.You enjoyed 100% funding. No charges!')."</span>
                </div>";
              }else{
                $formatted_bonus = number_format($promo_bonus, 2);
                $res .= "<br><div style='
                    background: #d1fae5;
                    border: 1px solid #10b981;
                    color: #065f46;
                    padding: 8px 14px;
                    margin-top: 10px;
                    border-radius: 8px;
                    font-size: 14px;
                    font-weight: 500;
                    display: inline-block;
                '>
                    🎉 ".__('messages.You enjoyed promo bonus').": <span style='color: #047857;'>₦{$formatted_bonus}</span>
                </div>";
              }
            }
          

            return $res;

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

      // date('Y-m-d', strtotime('-10000 days'))
      $date_from = $request->date_from ?? '';

      // date('Y-m-d')
      $date_to= $request->date_to ?? '';

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
      ->latest()
      ->limit($limit)
      ->get();
  
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
        $res = $data->payment_reference;
        return $res;
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
          'pin' => ['required','string','regex:/^\d{4,5}$/','exists:users,pin'],
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
        $dataa = $this->get_user_dashboard_data();
        $data = [...$dataa];
        
        $user_id = auth()->id();
        // $funding_option = FundingOption::with('bank_codes.virtual_user_account_with_bank_code')->where('activation_status',1)->first();
        $funding_option = FundingOption::with(['bank_codes' => function($query){
          $query->where('visibility_status',1);
        }])->where('activation_status',1)->first();
        $data['funding_option'] = $funding_option;
        $banks = config('banks');
        foreach($banks as $key=>$bank){
          $dataa[$key] = $bank;
        }
        $data['banks'] = $dataa;
        
        // dd($data);
        // return $funding_option;
        if($funding_option->slug == 'monnify'){
          if( auth()->user()->is_bvn_verified == 0 && auth()->user()->is_nin_verified == 0 ){
            return redirect()->route('user.wallet.monnify.verifications')->with($data);
          }

          $monnify_virtual_accounts = UserMonnifyVirtualAccount::where('user_id',$user_id)->get();
          $data['monnify_virtual_accounts'] = $monnify_virtual_accounts;
          // dd($data);
          return view('user.wallet.monnify.index')->with($data);
        }

        $generated_user_virtual_accts_funding_option_id = UserVirtualAccount::where('user_id',auth()->id())->pluck('funding_option_id')->first();
        $generated_user_virtual_accts_bank_code = UserVirtualAccount::where('user_id',auth()->id())->pluck('bank_code')->toArray();
        $user_virtual_accounts = UserVirtualAccount::where('user_id',auth()->id())->get();
        $data['generated_user_virtual_accts_funding_option_id'] = $generated_user_virtual_accts_funding_option_id;
        $data['generated_user_virtual_accts_bank_code'] = $generated_user_virtual_accts_bank_code;
        $data['user_virtual_accounts'] = $user_virtual_accounts;

        $siteTemplate = SiteTemplate::first();
        if(! $siteTemplate || $siteTemplate->template_name == 'template_1'){
            return view('user.wallet.crystal_pay.index')->with($data);
        }
        
        return view('template2.user.wallet.crystal_pay.index')->with($data);
    }

    public function monnify_verifications(Request $request){
      $banks = config('banks');
      $bvn_status = auth()->user()->is_bvn_verified;
      $nin_status = auth()->user()->is_nin_verified;
      $data['nin_status'] = $nin_status;
      $data['bvn_status'] = $bvn_status;
      
      foreach($banks as $key=>$bank){
        $dataa[$key] = $bank;
      }
      $data['banks'] = $dataa;
      return view('user.wallet.monnify.verifications')->with($data);
    }
   
    public function fund_wallet(Request $request){
        // dd('good');
        $user_id = auth()->id();
        $virtual_account = UserVirtualAccount::where('user_id',$user_id)->first();
        $data['virtual_account'] = $virtual_account;
        return view('user.wallet.fund_wallet')->with($data);
    }

    //CRYSTALPAY accounts generation
    public function generate_virtual_account(Request $request){
        $validator = Validator::make($request->all(), [
            'pin' => ['required','string','regex:/^\d{4,5}$/'],
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
          $bvn = $request->phone_number;
          $bank_code = $request->bank_code;
          $funding_option_id = $request->funding_option;  
          
           //success
           if($email == 'mike.e.emmanuel@gmail.com' || $email == 'emmanuel@gmail.com'){
                $bvn = '22225551122';
           }

          
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
                $api_key = $wallet_funding->api_secret_key;
                $biz_bvn = $wallet_funding->biz_bvn ?? $phone_number;
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
                    "bvn"=>$biz_bvn
                ];


                // return $arrr;
                $arrjson = json_encode($arrr);

                // logger("CP data passed: $arrjson");
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

                // logger("Account generation crystalpay:  $response");

                $response_dec = json_decode($response,true);    
                
                if(  isset($response_dec['success']) 
                     && $response_dec['success'] == true
                     &&  isset($response_dec['status']) 
                     &&  $response_dec['status'] == 'Success' 
                     && isset($response_dec['data']['account_number'])
                     &&  $response_dec['data']['account_number'] != ''
                       ){
                   
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

                    Session::flash('success','Virtual account was successfully generated');
                    return redirect()->back();    

                }

                
                Session::flash('failure','Sorry, Virtual account for this bank cannot be generated at this point. Try again later. ');
                return redirect()->back();  
                
        }

    

  }
