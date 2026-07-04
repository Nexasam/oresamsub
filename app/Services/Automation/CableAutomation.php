<?php

namespace App\Services\Automation;

use App\Models\Network;
use App\Models\ProductPlan;


class CableAutomation{

    private $network_id;
    private $plan_id;

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

    public function validatePayeelordCable()
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
            "plan_id"=>$plan->automation_product_plan_id,
            "smart_card_number"=> $this->smart_card_number,
            "phone"=> $this->mobile_number,
        ];
        $encoded = json_encode($payload);

        $headers = [
            'Authorization: Bearer '.$this->token,
            'Content-Type: application/json'
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://www.payeelord.com/api/verify/smartcard',
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
        logger('Payeelord Validation Response: '.$response);

        $response_dec = json_decode($response, true);

        curl_close($curl);


        if (  isset($response_dec['status']) && $response_dec['status'] === 'successful') {
            return [
                'status' => 1,
                'user_message' =>  $response_dec['api_response'] ?? 'Validation Successful',
                'admin_message' => $response,
                'name' => $response_dec['api_response'] ?? 'Name Validation Success',
                'address' => $response_dec['api_response'] ?? 'Address Validation',
            ];
        }

        return [
            'status' => -1,
            'user_message' =>  $response_dec['message'] ?? 'Validation failed',
            'admin_message' => $response,
            'name' => $response_dec['message'] ?? 'Validation failed',
          
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
