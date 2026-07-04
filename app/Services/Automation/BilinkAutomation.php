<?php

namespace App\Services\Automation;

use App\Models\Network;
use App\Models\ProductPlan;


class BilinkAutomation{

    private $network_id;

    private $automation_id;

    private $api_id;

    private $plan_api_id;

    private $mobile_number;

    private $token;

    private $url;

    private $amount;
    
    private $user_id;

    private $smart_card_number;
    private $meter_number;



    // private $ported_number;


    public function __construct($data){
        $this->automation_id = $data['automation_id'] ?? '';
        $this->network_id = $data['network_id'] ?? '';
        $this->plan_id = $data['plan_id'] ?? '';
        $this->mobile_number = $data['phone_number'] ?? '';
        $this->token = $data['token'] ?? '';
        $this->url = $data['url'] ?? '';
        $this->amount = $data['amount'] ?? 0;
        $this->user_id = $data['user_id'] ?? '';
        $this->smart_card_number = $data['smart_card_number'] ?? '';
        $this->meter_number = $data['meter_number'] ?? '';
    }


    public function buyAirtime(){
        
        $plan_details = ProductPlan::with('product_plan_category.network')
        ->where('visibility',1)
        ->where('id',$this->plan_id)->first();
        if(! $plan_details){
            return [
                'status' => -1,
                'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
                'admin_message' => 'Wrong plan Id',
            ];
        }

        $network_details = Network::where('visibility',1)->where('id',$this->network_id)->first();
        if(! $network_details){
            return [
                'status' => -1,
                'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
                'admin_message' => 'Network ID is likely not available or set to hidden',
            ];
        }


        $network_name = $network_details->network_name;
     


        $ref = uniqid('simserver_');
        $automation_plan_id = $plan_details->automation_product_plan_id; 
        $array = [
            // "network"=>$api_network_id,
            // "amount"=>$this->amount,
            // "mobile_number"=>$this->mobile_number,
            // "airtime_type"=> $automation_plan_id,
            // "Ported_number"=>false
            "amount"=>$this->amount,
            "product_code"=>$automation_plan_id,
            "phone_number"=>$this->mobile_number,
            "action"=>"vend",
            "user_reference"=>$ref,
            "async"=>true,
            "callback"=>"https://bilink.ng/autobiz_vending_index.php"
        ];

        $encoded_array = json_encode($array);
        logger('sim Encoded arr: '.$encoded_array);
        $header_array = array(
            'Authorization: Bearer '.$this->token,
            'Content-Type: application/json'
        );
        $header_json = json_encode($header_array);
        logger('sim header: '.$header_json);

        $curl = curl_init();
        curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => "$this->url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $encoded_array,
            CURLOPT_HTTPHEADER => $header_array
        )
        );
        $response = curl_exec($curl);
        $response_dec = json_decode($response,true);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        // logger($response);

        if(isset($response_dec['status']) && $response_dec['status'] === true){
            //success
            
            return [
                'status' => 1,
                'user_message' => $response_dec['data']['true_response'] ?? 'Transaction was succesfully processed',
                'admin_message' => $response,
            ];

        }else{

            $usermsg = isset($response_dec['server_message']) ? $response_dec['server_message'] : "Sorry, transaction failed. Please try again";


            return [
                'status' => -1,
                'user_message' =>$usermsg,
                'admin_message' => $response,
            ];
        }        
    }

    private function sendRequest(array $payload)
    {
        $encoded = json_encode($payload);

        logger('SIMserver Payload: '.$encoded);

        $headers = [
            'Authorization: Bearer '.$this->token,
            'Content-Type: application/json'
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $encoded,
            CURLOPT_HTTPHEADER => $headers
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        return [
            'raw' => $response,
            'decoded' => json_decode($response, true)
        ];
    }

    public function validateCable()
    {

        $plan = ProductPlan::find($this->plan_id);

        if (! $plan) {
            return [
                'status' => -1,
                'user_message' => 'Invalid cable package selected',
                'admin_message' => 'Plan not found'
            ];
        }

        $payload = [
            'product_code'      => $plan->automation_product_plan_id,
            'smartcard_number'  => $this->smart_card_number,
            'action'            => 'verify'
        ];

        $response = $this->sendRequest($payload);

        if (($response['decoded']['status'] ?? false) === true) {
            return [
                'status' => 1,
                'user_message' =>  $response['decoded']['data']['text_status'] ?? 'Validation Successful',
                'admin_message' => $response['raw'],
                'name' => $response['decoded']['data']['name'] ?? 'Name Validation Success',
                'address' => $response['decoded']['data']['name'] ?? 'Address Validation',
            ];
        }

        return [
            'status' => -1,
            'user_message' =>  $response['decoded']['data']['text_status'] ?? 'Validation failed',
            'admin_message' => $response['raw'],
            'name' => $response['decoded']['data']['text_status'] ?? 'Validation failed',
          
        ];
    }


    public function buyCable()
    {
        $plan = ProductPlan::find($this->plan_id);

        if (! $plan) {
            return [
                'status' => -1,
                'user_message' => 'Invalid cable package selected',
                'admin_message' => 'Plan not found'
            ];
        }

        $ref = uniqid('simserver_');

        $payload = [
            'product_code'      => $plan->automation_product_plan_id,
            'phone_number'      => $this->mobile_number,
            'smartcard_number'  => $this->smart_card_number,
            'amount'            => $this->amount,
            'user_reference'    => $ref,
            'action'            => 'vend',
            'callback'          => 'https://bilink.ng/autobiz_vending_index.php'
        ];

        $response = $this->sendRequest($payload);

        if (($response['decoded']['status'] ?? false) === true) {
            return [
                'status' => 1,
                'user_message' => $response['decoded']['data']['text_status'] ?? 'Cable subscription successful',
                'admin_message' => $response['raw'],
            ];
        }

        return [
            'status' => -1,
            'user_message' => $response['decoded']['data']['text_status'] ?? 'Cable subscription failed',
            'admin_message' => $response['raw'],
        ];
    }

    public function validateElectricity()
    {
    $plan = ProductPlan::find($this->plan_id);

    if (! $plan) {
        return [
            'status' => -1,
            'user_message' => 'Invalid electricity provider',
            'admin_message' => 'Plan not found'
        ];
    }

    $payload = [
        'product_code'   => $plan->automation_product_plan_id,
        'meter_number'   => $this->meter_number,
        'amount'         => $this->amount,
        'user_reference' => uniqid('simserver_'),
        'action'         => 'verify'
    ];

    $response = $this->sendRequest($payload);

    if (($response['decoded']['status'] ?? false) === true) {
        return [
            'status' => 1,
            'user_message' => 'Meter validated successfully',
            'admin_message' => $response['raw'],
        ];
    }

    return [
        'status' => -1,
        'user_message' => $response['decoded']['server_message'] ?? 'Meter validation failed',
        'admin_message' => $response['raw'],
    ];
    }

    public function buyElectricity()
    {
        $plan = ProductPlan::find($this->plan_id);

        if (! $plan) {
            return [
                'status' => -1,
                'user_message' => 'Invalid electricity provider',
                'admin_message' => 'Plan not found'
            ];
        }

        $ref = uniqid('simserver_');

        $payload = [
            'product_code'   => $plan->automation_product_plan_id,
            'meter_number'   => $this->meter_number,
            'amount'         => $this->amount,
            'user_reference' => $ref,
            'action'         => 'vend',
            'callback'       => 'https://bilink.ng/autobiz_vending_index.php'
        ];

        $response = $this->sendRequest($payload);

        if (($response['decoded']['status'] ?? false) === true) {
            return [
                'status' => 1,
                'user_message' => $response['decoded']['data']['true_response'] ?? 'Electricity purchase successful',
                'admin_message' => $response['raw'],
            ];
        }

        return [
            'status' => -1,
            'user_message' => $response['decoded']['server_message'] ?? 'Electricity purchase failed',
            'admin_message' => $response['raw'],
        ];
    }

   

}
