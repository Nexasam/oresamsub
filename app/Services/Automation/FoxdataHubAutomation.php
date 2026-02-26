<?php

namespace App\Services\Automation;

use App\Models\Network;
use App\Models\ProductPlan;

class FoxdataHubAutomation{

    private $network_id;

    private $automation_id;


    private $automation_details;

    private $api_id;
    private $plan_id;

    private $plan_api_id;

    private $mobile_number;

    private $token;

    private $url;

    private $amount;

    private $smart_card_number;

    private $metre_number;

    private $validation_extra_info;


    // private $ported_number;


    public function __construct($data){
        $this->automation_id = $data['automation_id'] ?? "";
        $this->automation_details = $data['automation_details'] ?? "";
        $this->network_id = $data['network_id'] ?? "";
        $this->plan_id = $data['plan_id'] ?? "";
        $this->mobile_number = $data['phone_number'] ?? "";
        $this->smart_card_number = $data['smart_card_number'] ?? "";
        $this->metre_number = $data['metre_number'] ?? "";
        $this->validation_extra_info = $data['validation_extra_info'] ?? "";
        $this->token = $data['token'] ?? "";
        $this->url = $data['url'] ?? "";
        $this->amount = $data['amount'] ?? 0;
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

        $network_details = Network::where('visibility',1)->where('id',$this->network_id)->first();
        if(! $network_details){
            return [
                'status' => -1,
                'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
                'admin_message' => 'Network ID is likely not available or set to hidden',
            ];
        }


        $automation_plan_id = $plan_details->automation_product_plan_id; 

        $custom_ref = substr(uniqid(rand(), true), 0, 15);

        $array = [
            "mobile_number"=>$this->mobile_number,
            "plan"=> $automation_plan_id,
            "reference"=>$custom_ref,
        ];
        $encoded_array = json_encode($array);
        $header_array = array(
            'Authorization: Token '.$this->token,
            'Content-Type: application/json',
            'Accept: application/json',
        );
        $header_json = json_encode($header_array);

     
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://foxdatahub.com/api/v1/user/buy_data',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$encoded_array,
        CURLOPT_HTTPHEADER => $header_array,
        ));
        $response = curl_exec($curl);
        $response_dec = json_decode($response,true);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);



        // if(isset($response_dec['status']) && $response_dec['status'] == 'success'){
        if(isset($response_dec['status']) && $response_dec['status'] == true ){
            //success
            return [
                'status' => 1,
                'user_message' => $response_dec['message'] ?? 'Congratulations, your transaction was successfully processed',
                'admin_message' => $response,
            ];
        }else{

            // $usermsg = isset($response_dec['message']) ? $response_dec['message'].'sss' : "Sorry, transaction failed. Please try again";
            $realresponse = $response_dec['message'] ??  "Sorry, transaction failed with code: $httpcode";

            return [
                'status' => -1,
                'user_message' =>$realresponse,
                'admin_message' => $response,
            ];

        }        
    }

    public function buyElectricity(){
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

        $automation_plan_id = $plan_details->automation_product_plan_id; 

        $custom_ref = substr(uniqid(rand(), true), 0, 15);

        $array = [
            "metre_number" => $this->metre_number,
            "validation_extra_info" => $this->validation_extra_info,
            "plan" => $automation_plan_id,
            "amount" => $this->amount,
            "reference" => $custom_ref,
        ];
        $encoded_array = json_encode($array);
        $header_array = array(
            'Authorization: Token '.$this->token,
            'Content-Type: application/json',
            'Accept: application/json',
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://foxdatahub.com/api/v1/user/buy_electricity',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $encoded_array,
            CURLOPT_HTTPHEADER => $header_array,
        ));

        $response = curl_exec($curl);
        $response_dec = json_decode($response, true);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if(isset($response_dec['status']) && $response_dec['status'] == true ){
            return [
            'status' => 1,
            'user_message' => $response_dec['message'] ?? 'Congratulations, your transaction was successfully processed',
            'admin_message' => $response,
            'token' => $response_dec['data']['token'] ?? null,
            ];
        }else{
            $realresponse = $response_dec['message'] ??  "Sorry, transaction failed with code: $httpcode";
            return [
            'status' => -1,
            'user_message' => $realresponse,
            'admin_message' => $response,
            ];
        }
      
    }

    public function buyCableTv(){
        
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

        $automation_plan_id = $plan_details->automation_product_plan_id; 
        $custom_ref = substr(uniqid(rand(), true), 0, 15);
        $array = [
            "smart_card_number"=>$this->smart_card_number,
            "plan"=> $automation_plan_id,
            "validation_customer_name" => $this->validation_extra_info,
            "reference"=>$custom_ref,
        ];
        $encoded_array = json_encode($array);
        $header_array = array(
            'Authorization: Token '.$this->token,
            'Content-Type: application/json',
            'Accept: application/json',
        );
        $header_json = json_encode($header_array);

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://foxdatahub.com/api/v1/user/buy_cable_tv',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$encoded_array,
        CURLOPT_HTTPHEADER => $header_array,
        ));

        $response = curl_exec($curl);

        curl_close($curl);
      
        $response_dec = json_decode($response,true);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // if(isset($response_dec['status']) && $response_dec['status'] == 'success'){
        if(isset($response_dec['status']) && $response_dec['status'] == true ){
            //success
            return [
                'status' => 1,
                'user_message' => $response_dec['message'] ?? 'Congratulations, your transaction was successfully processed',
                'admin_message' => $response,
            ];
        }else{

            // $usermsg = isset($response_dec['message']) ? $response_dec['message'].'sss' : "Sorry, transaction failed. Please try again";
            $realresponse = $response_dec['message'] ??  "Sorry, transaction failed with code: $httpcode";
            return [
                'status' => -1,
                'user_message' =>$realresponse,
                'admin_message' => $response,
            ];

        }        
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

        $automation_plan_id = $plan_details->automation_product_plan_id; 
        $custom_ref = substr(uniqid(rand(), true), 0, 15);
        $array = [
            "mobile_number"=>$this->mobile_number,
            "plan"=> $automation_plan_id,
            "amount"=> $this->amount,
            "reference"=>$custom_ref,
        ];
        $encoded_array = json_encode($array);
        $header_array = array(
            'Authorization: Token '.$this->token,
            'Content-Type: application/json',
            'Accept: application/json',
        );
        $header_json = json_encode($header_array);

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://foxdatahub.com/api/v1/user/buy_airtime',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$encoded_array,
        CURLOPT_HTTPHEADER => $header_array,
        ));

        $response = curl_exec($curl);

        curl_close($curl);
      
        $response_dec = json_decode($response,true);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);


        // if(isset($response_dec['status']) && $response_dec['status'] == 'success'){
        if(isset($response_dec['status']) && $response_dec['status'] == true ){
            //success
            return [
                'status' => 1,
                'user_message' => $response_dec['message'] ?? 'Congratulations, your transaction was successfully processed',
                'admin_message' => $response,
            ];
        }else{

            // $usermsg = isset($response_dec['message']) ? $response_dec['message'].'sss' : "Sorry, transaction failed. Please try again";
            $realresponse = $response_dec['message'] ??  "Sorry, transaction failed with code: $httpcode";
            return [
                'status' => -1,
                'user_message' =>$realresponse,
                'admin_message' => $response,
            ];

        }        
    }

    public function validateCableTv(){
        
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

    

        $automation_plan_id = $plan_details->automation_product_plan_id; 
        $custom_ref = substr(uniqid(rand(), true), 0, 15);
        $array = [
            "smart_card_number"=>$this->smart_card_number,
            "plan"=> $automation_plan_id,
            "reference"=>$custom_ref,
        ];
        $encoded_array = json_encode($array);
        $header_array = array(
            'Authorization: Token '.$this->token,
            'Content-Type: application/json',
            'Accept: application/json',
        );
        $header_json = json_encode($header_array);

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://foxdatahub.com/api/v1/user/validate_cable_tv',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$encoded_array,
        CURLOPT_HTTPHEADER => $header_array,
        ));

        $response = curl_exec($curl);

        logger('cable validation response TTT: '.$response.' '.$this->token);

        curl_close($curl);
      
        $response_dec = json_decode($response,true);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);   
        
        if(isset($response_dec['status']) && $response_dec['status'] == true ){
            //success
            return [
                'status' => 1,
                'user_message' => $response_dec['message'] ?? 'Congratulations, your validation was successful',
                'admin_message' => $response,
                'name' => $response_dec['data']['name'] ?? "",
                'address' => $response_dec['data']['address'] ?? "",
            ];
        }else{  

            $realresponse = $response_dec['message'] ??  "Sorry, validation failed with code: $httpcode";
            return [
                'status' => -1,
                'user_message' =>$realresponse,
                'admin_message' => $response,
                'name' => $response_dec['data']['name'] ?? "",
                'address' => $response_dec['data']['address'] ?? "",
            ];

        }   

    }

    public function validateMetreNumber(){
        
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
    
        
    
            $automation_plan_id = $plan_details->automation_product_plan_id; 
            $custom_ref = substr(uniqid(rand(), true), 0, 15);
            $array = [
                "metre_number"=>$this->metre_number,
                "plan"=> $automation_plan_id,
                "reference"=>$custom_ref,
            ];
            $encoded_array = json_encode($array);
            $header_array = array(
                'Authorization: Token '.$this->token,
                'Content-Type: application/json',
                'Accept: application/json',
            );
            $header_json = json_encode($header_array);
    
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://foxdatahub.com/api/v1/user/validate_metre_number',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$encoded_array,
            CURLOPT_HTTPHEADER => $header_array,
            ));
    
            $response = curl_exec($curl);
    
            logger('metre validation response BBB: '.$response.' '.$this->token);
    
            curl_close($curl);
          
            $response_dec = json_decode($response,true);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);   
            
            if(isset($response_dec['status']) && $response_dec['status'] == true ){
                //success
                return [
                    'status' => 1,
                    'user_message' => $response_dec['message'] ?? 'Congratulations, your validation was successful',
                    'admin_message' => $response,
                    'name' => $response_dec['data']['name'] ?? "",
                    'address' => $response_dec['data']['address'] ?? "",
                ];
            }else{  
    
                $realresponse = $response_dec['message'] ??  "Sorry, validation failed with code: $httpcode";
                return [
                    'status' => -1,
                    'user_message' =>$realresponse,
                    'admin_message' => $response,
                    'name' => $response_dec['data']['name'] ?? "",
                    'address' => $response_dec['data']['address'] ?? "",
                ];
    
            }   
    
        }  


}
