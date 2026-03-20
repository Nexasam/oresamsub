<?php
namespace App\Services\Automation;

use App\Models\AutomationWalletFunding;
use App\Models\FundingOption;
use Illuminate\Support\Facades\Log;

class WalletAutoFundingService
{
    public function run()
    {
        AutomationWalletFunding::with('automation')
        ->where('automatic_funding', true)
        ->where('active', 'yes')
        ->chunk(5, function ($automations) {

            foreach ($automations as $automation) {
                $this->process($automation);
            }
        });
    }

    public function getSecurewaveMerchantBalance(){

        // 'Authorization: Bearer '.$api_secret_key,
        // 'x-api-key: '.$api_public_key

        $funding_option = FundingOption::where('slug','securewaveng')->first();


        $api_public_key = $funding_option->api_public_key;
        $api_secret_key = $funding_option->api_secret_key;

        $curl = curl_init();


        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://securewaveng.com/api/banks',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer '.$api_secret_key,
            'x-api-key: '.$api_public_key
        ),
        ));

        $response = curl_exec($curl);

        $response_dec = json_decode($response,true);

        curl_close($curl);

        if($response_dec['status']){
            $balance = $response_dec['data']['balance'] ?? 0;
            return [
                'status' => 1,
                'messsage' => "success",
                'balance' => $balance
            ];
        }

        return [
            'status' => -1,
            'messsage' => $response_dec['message'],
            'balance' => 0
        ];
        
        
    }

    public function process($automation)
    {
        // $user = $automation->automation->user;
        // $wallet = $user->wallet;

        // if (!$wallet) return;

        // Check threshold
        if ($automation->last_balance > $automation->threshold) {
            logger('Threshold not reached yet.');
            return;
        }

        $merchantbalance = $this->getSecurewaveMerchantBalance();
        
        if(($automation->amount_to_fund < $merchantbalance['balance']) || $merchantbalance['balance'] == 0){
            //no funds to fund from or amount is great that merchant amount
            logger('no funds to fund from or amount is greater than merchant amount which is:'.$merchantbalance['balance']);
            return;
        }

        try {
            // 🔥 FUNDING LOGIC HERE
            $this->fundWallet($automation);

            // Optional logging
            Log::info('Auto funding success', [
                'amount' => $automation->amount_to_fund
            ]);

        } catch (\Throwable $e) {

            Log::error('Auto funding failed', [
                // 'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            // if ($automation->send_failed_notification) {
            //     $this->notifyFailure($user, $e->getMessage());
            // }
        }
    }

    protected function fundWallet($automation)
    {
        $amount_to_fund = $automation->amount_to_fund;
        // OPTION 1: Internal wallet top-up
        // $user->wallet->increment('balance', $amount);

        // OPTION 2 (later): call payment gateway / virtual account funding
        // e.g Monnify / Paystack API

        // You can also create a transaction record
        // $user->transactions()->create([
        //     'type' => 'credit',
        //     'amount' => $amount,
        //     'description' => 'Auto wallet funding',
        //     'status' => 'success',
        // ]);

        $funding_option = FundingOption::where('slug','securewaveng')->first();


        $api_public_key = $funding_option->api_public_key;
        $api_secret_key = $funding_option->api_secret_key;


           $arr = [
                "customer_email"=>$automation->linked_customer_email,
                "amount"=>$amount_to_fund,
                "narration"=>"Customer withdrawal"
           ];
           $json_req = json_encode($arr);

            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://securewaveng.com/api/customer_withdrawals/withdraw',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$json_req,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer '.$api_secret_key,
                'x-api-key: '.$api_public_key
            ),
            ));

            $response = curl_exec($curl);

            $response_dec = json_decode($response,true);
    
            curl_close($curl);
    
            if($response_dec['status']){
                // $balance = $response_dec['data']['balance'] ?? 0;
                return [
                    'status' => 1,
                    'messsage' => "successful withdrawal"
                ];
            }
    
            return [
                'status' => -1,
                'messsage' => $response_dec['message']
            ];
    }

    protected function notifyFailure($user, $message)
    {
        // Simple version
        Log::warning("Notify user {$user->id}: {$message}");

        // Later: Mail / SMS / WhatsApp
    }
}