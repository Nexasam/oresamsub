<?php

namespace App\Services\Automation;

use App\Models\User;
use App\Models\Automation;
use App\Models\ProductPlan;
use Illuminate\Support\Facades\Http;

class VtpassAutomation{

    private $smart_card_number;
    private $metre_number;
    private $electricity_plan_api_id;
    private $amount;
    private $total_amount;
    private $phone_number;
    private $automation_slug = 'vtpass';
    private $validatephonenetwork = 1;
    private $plan_id = '';
    private $extra_info = '';
    private $no_of_slots = '';

    private $product_plan_category_name = '';
    private $duplication_check = 0;

    private $api_skey = '';
    private $api_pkey = '';
    private $api_key = '';
    private $user_id = '';


    public function __construct($data){
        
        $this->smart_card_number = $data['smart_card_number'] ?? ''; //metreno
        $this->user_id = $data['user_id'] ?? '';
        $this->extra_info = $data['extra_info'] ?? '';
        $this->plan_id = $data['plan_id'] ?? '';
        $this->phone_number = $phone_number ?? '';
        $this->no_of_slots = $no_of_slots ?? '';
        $this->product_plan_category_name = $data['product_plan_category_name'] ?? '';
        $this->amount = $data['total_amount'] ?? '';
        $this->total_amount = $data['total_amount'] ?? '';
        $this->duplication_check = '0';
        $this->api_pkey = Automation::where('slug',$this->automation_slug)->first()->api_public_key;
        $this->api_skey = Automation::where('slug',$this->automation_slug)->first()->api_secret_key;
        $this->api_key = Automation::where('slug',$this->automation_slug)->first()->api_password;
      
    }

    public function validateSmartCard(){
        
            $user_details = User::where('id',$this->user_id)->first();
            if(! $user_details){
                return [
                    'status' => -1,
                    'message' => 'User record not found'
                ];
            }

            $plan_details = ProductPlan::with('product_plan_category')
            ->where('visibility',1)
            ->where('id',$this->plan_id)->first();

        
            
            if(! $plan_details){
                return [
                    'status' => -1,
                    'message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
                    'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
                    'admin_message' => 'Wrong plan Id',
                ];
            }

            $automation_product_plan_id = $plan_details->automation_product_plan_id;
            $smart_card_number = $this->smart_card_number;
            // logger('API ID: '.$automation_product_plan_id);
            $product_namee = strtolower($plan_details->product_plan_category->product_plan_category_name);
            logger('API ID: '.$product_namee);


            //test
            // $smart_card_number = 1212121212;
            $arr = [
                'billersCode' => $smart_card_number,
                'serviceID' => $product_namee,
            ];
            $arrjson = json_encode($arr);

            $url = 'https://vtpass.com/api/merchant-verify';
            // $url = 'https://sandbox.vtpass.com/api/merchant-verify';

            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$arrjson,
            CURLOPT_HTTPHEADER => array(
                'api-key: '.$this->api_key,
                'secret-key: '.$this->api_skey,
                'Content-Type: application/json'
            ),
            ));        
            $response = curl_exec($curl);
            logger('CABLEVTPASS: '.$response);
            curl_close($curl);
            $response_decode = json_decode($response,true);

            if(isset($response_decode['code']) && $response_decode['code'] == '000' ){
                //successful transaction
                return [
                        'status' => 1,
                        'address' => isset($response_decode['content']['Customer_Name']) ? $response_decode['content']['Customer_Name']  :  'Address not found',
                        'name' => isset($response_decode['content']['Customer_Name']) ? $response_decode['content']['Customer_Name']  :  'Address not found',
                        'message' => isset($response_decode['content']['Customer_Name']) ? $response_decode['content']['Customer_Name']  :  'Address not found',
                    ];

            }else{           
                return [
                        'status' => 1,
                        'address' =>  'Address not found',
                        'name' => 'Defaulted to: '.$user_details->first_name.' '.$user_details->last_name,
                        'message' => isset($response_decode['content']['Customer_Name']) ? $response_decode['content']['Customer_Name'] :  'Defaulted to: '.$user_details->first_name.' '.$user_details->last_name,
                ];
            }

    }

    public function buyCable(){
        $user_details = User::where('id',$this->user_id)->first();
        if(! $user_details){
            return [
                'status' => -1,
                'message' => 'User record not found'
            ];
        }

        $plan_details = ProductPlan::with('product_plan_category')
        ->where('visibility',1)
        ->where('id',$this->plan_id)->first();

        
        if(! $plan_details){
            return [
                'status' => -1,
                'message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
                'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
                'admin_message' => 'Wrong plan Id',
            ];
        }

        $automation_product_plan_id = $plan_details->automation_product_plan_id;
        $smart_card_number = $this->smart_card_number;
        logger('API ID: '.$automation_product_plan_id);


        $product_namee = strtolower($plan_details->product_plan_category->product_plan_category_name);
        // $smart_card_number = '1212121212';
        // $request_id = date('YmdH').str(5).time();
        $request_id = date('YmdH') . rand(10000, 99999) . time();
        $arr = [
            "billersCode"=>$smart_card_number,
            "serviceID"=>$product_namee,
            "request_id"=>$request_id,
            "variation_code"=>$automation_product_plan_id,
            "amount"=>$this->amount,
            "phone"=>$user_details->phone_number,
            "subscription_type"=>"renew",
            "quantity"=>"1"
        ];
        $arrjson = json_encode($arr);
        logger($arrjson);

        $url = 'https://vtpass.com/api/pay';
        // $url = 'https://sandbox.vtpass.com/api/pay';

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>$arrjson,
          CURLOPT_HTTPHEADER => array(
            'api-key: '.$this->api_key,
            'secret-key: '.$this->api_skey,
            'Accept: application/json',
            'Content-Type: application/json'
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);

        logger($response);

        $response_decode = json_decode($response,true);

        if(isset($response_decode['code']) && $response_decode['code'] == '000' ){
            //successful transaction
            return [
                'status' => 1,
                'user_message' => isset($response_decode['content']['response_description']) ? $response_decode['content']['response_description']  :  'Transaction was successful',
                'admin_message' => $response,
             ];

        }else{
                
            return [
                'status' => -1,
                'user_message' => isset($response_decode['content']['response_description']) ? $response_decode['content']['response_description']  :  'Transaction failed',
                'admin_message' => $response,
             ];
        }
    }
}
