<?php

namespace App\Services\Automation\OgdamsAutomation;

use App\Models\Automation;
use App\Models\ProductPlan;

class OgdamsVendData{

    private $mobile_number;

    private $network_api_id;

    private $airtime_api_id;


    private $automation_slug = 'ogdams';

    private $duplication_check = 1;

    private $validatephonenetwork = 1;

    private $plan_id = '';


    private $api_key = '';
    private $api_password = '';

    public function __construct($mobile_number,$plan_id){
        $this->mobile_number = $mobile_number;
        $this->plan_id = $plan_id;
        $this->api_key = Automation::where('slug','ogdams')->first()->api_public_key;
    }
    

    protected function getNetworkApiID($network_name){
        $network_name = strtolower($network_name);
        if($network_name == 'mtn'){
            return 1;
        }

        if($network_name == 'glo'){
            return 3;
        }

        if($network_name == 'airtel'){
            return 2;
        }

        if($network_name == '9mobile'){
            return 4;
        }

        return -1;
    }
    public function buyData(){
        
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


        $network_name = $plan_details->product_plan_category->network->network_name;
        $automation_plan_id = $plan_details->automation_product_plan_id;   
        $network_id = $this->getNetworkApiID($network_name);

        if($network_id == -1){
            //not found
            return [
                'status' => -1,
                'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
                'admin_message' => 'Api Id supplied is not found - 1:mtn 3:glo 2:airtel 4:9mobile',
            ];

        }

        $curl = curl_init();

        // $postfields = [
        //     "networkId"=>$network_id, 
        //     "planId"=>$automation_plan_id, 
        //     "phoneNumber"=>$this->mobile_number, 
        // ];
        // '{
        //     "networkId" : '.$network_id.',
        //     "planId" : '.$automation_plan_id.',
        //     "phoneNumber" : "'.$this->mobile_number.'"
        
        // }'


        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://simhosting.ogdams.ng/api/v1/vend/data',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>   '{
            "networkId" : '.$network_id.',
            "planId" : '.$automation_plan_id.',
            "phoneNumber" : "'.$this->mobile_number.'"
        
          }',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$this->api_key,  //sk_live_b73e769b-1ec0-47ea-9593-58c0d8b2cf9e
            'Content-Type: application/json',
            'Accept: application/json'
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
               
        $response_decode = json_decode($response,true);

        if(isset($response_decode['code']) && $response_decode['code'] == '200'){
            //successful transaction
            return [
                'status' => 1,
                'user_message' => isset($response_decode['data']['msg']) ? $response_decode['data']['msg'] :  'Transaction was successful',
                'admin_message' => isset($response_decode['data']['msg']) ? $response_decode['data']['msg'] :  'Transaction was successful',
            ];
        }else if( isset($response_decode['code']) && $response_decode['code'] == '201' ){
            return [
                'status' => 1,
                'user_message' => isset($response_decode['data']['msg']) ? $response_decode['data']['msg'] :  'Transaction is being processed',
                'admin_message' => isset($response_decode['data']['msg']) ? $response_decode['data']['msg'] :  'Transaction is being processed',
            ];
        }else if( isset($response_decode['code']) && $response_decode['code'] == '202' ){
            return [
                'status' => 0,
                'user_message' => isset($response_decode['data']['msg']) ? $response_decode['data']['msg'] :  'Transaction has been received and being processed',
                'admin_message' => isset($response_decode['data']['msg']) ? $response_decode['data']['msg'] :  'Transaction has been received and being processed',
            ];
        }else if( isset($response_decode['code']) && $response_decode['code'] == '424' ){
            return [
                'status' => -1,
                'user_message' => isset($response_decode['data']['msg']) ? $response_decode['data']['msg'] :  'Transaction failed',
                'admin_message' => isset($response_decode['data']['msg']) ? $response_decode['data']['msg'] :  'Transaction failed',
            ];
        }else{
            return [
                'status' => -1,
                'user_message' => isset($response_decode['data']['msg']) ? $response_decode['data']['msg'] :  'Transaction failed...',
                'admin_message' => isset($response_decode['data']['msg']) ? $response_decode['data']['msg'] :  'Transaction failed...',
            ];
        }

        
    }
}
