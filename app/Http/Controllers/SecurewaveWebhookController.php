<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\FundingOption;
use Illuminate\Support\Facades\DB;
use App\Models\FundingWebhookPayload;
use App\Models\FundingOptionBankCodes;
// use App\Models\UserWalletFundingPromo;
use App\Http\Services\WalletFundingPromoService;
use App\Traits\Dashboard\UserDashboardDataTrait;
use App\Models\MaxCrystalPaymentsPendingApproval;

class SecurewaveWebhookController extends Controller
{

    use UserDashboardDataTrait;
  


    //NOT USING THIS OH
    public function securewavehook(Request $request)
    {
       
            $raw = $request->getContent();
            $response  = $raw;
            $response_decode = json_decode($response,true);

            // $rawPayload = $request->getContent();
            $signature = $request->header('X-Signature'); // The HMAC sent from Securewaveng

            if (is_null($signature)) {
                logger('Webhook signature missing');
                return response()->json(['status' => 'unauthorized', 'message' => 'Missing signature'], 401);
            }
        
            // Retrieve merchant secret (configured on their system)
            // $secretKey = config('securewave.webhook_secret'); // or fetch from DB
            $getSecret = FundingOption::where('slug','securewaveng')->first();
            if(! $getSecret || !$getSecret->api_secret_key){
                logger('Webhook signature verification failed - wrong key.');
                return response()->json(['status' => 'unauthorized...wrong key'], 401);
            }
            
            $secretKey = $getSecret->api_secret_key;
            
            // Compute HMAC of the payload
            $computedHmac = hash_hmac('sha256', $raw, $secretKey);

            logger("Merchant hash: $computedHmac; Platform hash: $signature");
        
            // Compare signatures securely
            if (!hash_equals($computedHmac, $signature)) {
                logger('Webhook signature verification failed.');
                return response()->json(['status' => 'unauthorized'], 401);
            }

            
            
            $can_fund = '';

            $funding_option_details = FundingOption::with('webhook_string')->where('slug','securewaveng')->first();

            $promo_id = NULL;
            $custom_user_funding_promo_id = NULL;


            DB::beginTransaction();
            try{

            $provider_ref = $response_decode['provider_reference'];

            $check_exists = FundingWebhookPayload::where('transaction_reference',$response_decode['provider_reference'])
            ->first();

     

            if( ($response_decode['transaction_status'] == 'success') && (!$check_exists) ){    
                
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
                        'payment_reference' => $response_decode['provider_reference']
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
                    return response()->json(['status' => 'could not fund user wallet because user information was not found'], 200);

                }

                $paid_amount = $response_decode['amount'];
                if( strtolower($response_decode['receiver']['bank']) == 'kolomoni'){
                    $package_id = 1;
                }else if( strtolower($response_decode['receiver']['bank']) == 'wema'){
                    $package_id = 2;
                }else{
                    $package_id = 3;
                    // $package_id = $response_decode['provider_reference']; //work on changing later most likely
                }
                $get_charges = FundingOptionBankCodes::where('bank_code',$package_id)
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
                    //use securewaveng default settings
                    $charges = $response_decode['fees'];
                    $amount_to_fund_user = $response_decode['settlement_amount'];
                }



                //////CUSTOM WALLET FUNDING PROMO SERVICE 
                $walletPromoService = new WalletFundingPromoService();
                $userWalletPromo = $walletPromoService->getUserPromo(
                $user_details->id,
                $funding_option_details->id
                );      
                if ($userWalletPromo) {
                    $dataForCalc = [
                        'promo_discount_category' => $userWalletPromo->rate_category,
                        'promo_discount_percentage_cap' => $userWalletPromo->capped_at,
                        'funding_amount' => $paid_amount,
                        'promo_value' => $userWalletPromo->value,
                        'funding_option_id' => $funding_option_details->id,
                    ];
                    $amount_to_fund_user = $walletPromoService->get_amount_to_fund_user($dataForCalc)['actual_amount_to_fund_user'];
                    $promoBonus = $walletPromoService->get_amount_to_fund_user($dataForCalc)['promo_value'];
                    $custom_user_funding_promo_id = $userWalletPromo->id;

                    
                    $log = $walletPromoService->processFundingAndLog(
                    $user_details,
                    $funding_option_details,
                    $paid_amount,
                    $promoBonus,
                    $custom_user_funding_promo_id,
                    $response_decode['event_data']['data']['charged'] ?? $charges,
                    $response_decode['event_data']['data']['settled'] ?? ($paid_amount - $charges)
                    );

                }

        
                $created_data['funding_slug'] = 'securewaveng';
                $created_data['user_id'] = $user_details->id;
                $created_data['wallet_funding_promo_id'] = $promo_id;
                $created_data['custom_wallet_funding_promo_id'] = $custom_user_funding_promo_id;
                $created_data['user_email'] = $email;
                $created_data['status'] = $response_decode['transaction_status'] ?? 'nil';
                $created_data['message'] = $response_decode['description'] ?? 'nil';
                $created_data['package_id'] = $response_decode['provider_reference'];
                $created_data['bank_name'] = $response_decode['receiver']['bank'] ?? 'nil';
                $created_data['account_name'] = $response_decode['receiver']['name'] ?? 'nil';
                $created_data['account_number'] = $response_decode['receiver']['account_number'] ?? 'nil';
                $created_data['account_reference'] = $response_decode['provider_reference'] ?? 'nil';
                $created_data['amount_paid'] = $paid_amount;
                $created_data['amount_charged'] = $charges;
                $created_data['amount_settled'] = $amount_to_fund_user;
                $created_data['currency'] ='NGN';
                $created_data['collection_reference'] = $response_decode['provider_reference'];
                $created_data['transaction_reference'] = $response_decode['provider_reference'];
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
                    $walletLog['transaction_category'] = 'SECUREWAVENG_WALLET_FUNDING';
                    $walletLog['balance_before'] = $old_amount;
                    $walletLog['balance_after'] = $new_amount;
                    $walletLog['transaction_id'] = $response_decode['provider_reference'];
                    $walletLog['action_by'] = 'webhook';           
                    $walletLog['description'] = "Wallet of the user with the email: $email has been credited with $amount_to_fund_user via securewaveng";
                    $this->log_wallet_transactions($walletLog);
                    
                    if( $created && $updated ){
                    DB::commit();
                    logger('Great... All good.');
                    return response()->json(['status' => 'successfully processed'], 200);


                    }else{
                    // logger('Crediting failed for some reasons...');
                    DB::rollBack();
                    return response()->json(['status' => 'wallet could not be credited for some reasons'], 200);

                    }
                
            }else{
                    // logger('This webhook did not update wallet because its likely that the payment has been processed before');
                    return response()->json(['status' => 'already  likely received'], 200);
            }
            }catch(Exception $ex){
                logger(
                $ex->getMessage() . 
                ' on line ' . $ex->getLine() . 
                ' in ' . $ex->getFile() . 
                ' [Thrown in class: ' . get_class($ex) . ']'
            );
                DB::rollBack();
                return response()->json(['status' => 'error occurred: '.$ex->getMessage()], 200);

            }
    }
    

  }
