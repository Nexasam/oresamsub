<?php

namespace App\Services\Automation;
use App\Models\ProductPlan;

class PayscribeAutomation{

    private $plan_id;

    private $mobile_number;

    private $token;

    private $url;
 
    private  $smart_card_number;
    
    private  $metre_number;

    private  $amount;

    private  $address;

    private  $customer_name;

    private  $email;

    // private $ported_number;


    public function __construct($data){
        $this->plan_id = $data['plan_id'];
        $this->token = $data['token'] ?? null;
        $this->url = $data['url'] ?? null;
        $this->smart_card_number = $data['smart_card_number'] ?? null;
        $this->metre_number = $data['metre_number'] ?? null;
        $this->amount = $data['amount'] ?? 0;
        $this->address = $data['address'] ?? null;
        $this->customer_name = $data['customer_name'] ?? 0;
        $this->mobile_number = $data['mobile_number'] ?? null;
        $this->email = $data['email'] ?? null;
    }

  
    //mighht not be needed eventually
    protected function getProviderCableSlug($product_plan_category_name){
        //TODO: optimize this later... very important
        $product_plan_category_name = strtolower($product_plan_category_name);
        if($product_plan_category_name == 'gotv'){
            return 'gotv';
        }

        if($product_plan_category_name == 'dstv'){
            return 'dstv';
        }

        if($product_plan_category_name == 'startimes'){
            return 'startimes';
        }


        return -1;
    }

    // protected function getProviderMeterSlug($product_plan_category_name){
    //     //TODO: optimize this later... very important
    //     $product_plan_category_name = strtolower($product_plan_category_name);
    //     if($product_plan_category_name == 'gotv'){
    //         return 'gotv';
    //     }

    //     if($product_plan_category_name == 'dstv'){
    //         return 'dstv';
    //     }

    //     if($product_plan_category_name == 'startimes'){
    //         return 'startimes';
    //     }

    //     return -1;
    // }


    public function validateSmartCard(){
        
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

        // $apikey = 'ps_pk_live_qkzRZMdBOe8JPsvVsswtZhAF93nKWaKgP1I';
        $apikey = $this->token;
        $random_value = rand(1111,9999).'_rand'.time();
        $service = $this->getProviderCableSlug($plan_details->product_plan_category->product_plan_category_name);
   
        $arr = [
            "service"=>$service, //either gotv/startimes/dstv
            "account"=>$this->smart_card_number,
            "month"=>1,
            "plan_id" =>$random_value
        ];
        $payload = json_encode($arr);
        logger('ps request: '.$payload);
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.payscribe.ng/api/v1//multichoice/validate',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$payload,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$apikey,
            'Content-Type: text/plain',
        ),
        ));
        $response = curl_exec($curl);
        $response_dec = json_decode($response,true);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        logger('PAYSCRIBE: '.$response);

        curl_close($curl);

        $response_decode = json_decode($response,true);

        if( isset($response_decode['status']) && $response_decode['status'] == TRUE){
            $mname = $response_decode['message']['details']['customer_name'];
            $mname = $response_decode['message']['details']['customer_name'];
            return [
                'status' => 1,
                'address' => $mname ?? 'Address not found',
                'name' => $mname ?? 'Name not found',
            ];
        }else{
            $mname = 'Name could not be found';
            return [
                'status' => 1,
                'address' => $mname ?? 'Address not found',
                'name' => $mname ?? 'Name not found',
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
                    'name' => 'Name not found',
                    'address' => 'Address not found',
                    'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
                    'admin_message' => 'Wrong plan Id',
                ];
            }
    
		    // $apikey = 'ps_pk_live_qkzRZMdBOe8JPsvVsswtZhAF93nKWaKgP1I';
		    $apikey = $this->token;
            $random_value = rand(1111,9999).'_rand'.time();
            $service = $plan_details->automation_product_plan_id;
       
            $arr = [
                "meter_number"=>$this->metre_number,
                "meter_type" =>"prepaid",
                "amount" =>$this->amount,
                "service" =>$service
            ];
            $payload = json_encode($arr);
            logger('ps request: '.$payload);
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.payscribe.ng/api/v1//electricity/validate',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$payload,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$apikey,
                'Content-Type: text/plain',
            ),
            ));
            $response = curl_exec($curl);
            $response_dec = json_decode($response,true);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            logger('PAYSCRIBE VAL. ELECT: '.$response);

            curl_close($curl);

            $response_decode = json_decode($response,true);

            if( isset($response_decode['status']) && $response_decode['status'] == TRUE){
                $mname = $response_decode['message']['details']['customer_name'] ?? 'Name not found';
                $address = $response_decode['message']['details']['address'] ?? 'Address not found';
                return [
                    'status' => 1,
                    'address' => $address,
                    'name' => $mname,
                    'message' => $mname,
                ];
            }else{
                $mname = 'Name could not be found';
                $address = 'Address could not be found';
                return [
                    'status' => 1,
                    'address' => $address ,
                    'name' => $mname,
                    'message' => $mname,
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

        // $apikey = 'ps_pk_live_qkzRZMdBOe8JPsvVsswtZhAF93nKWaKgP1I';
        $apikey = $this->token;
        $random_value = rand(1111,9999).'_rand'.time();
        $service = $plan_details->automation_product_plan_id;
   
        $arr = [
            "meter_number"=>$this->metre_number,
            "meter_type" =>"prepaid",
            "amount" =>$this->amount,
            "service" =>$service,
            "phone" =>$this->mobile_number, #change later
            "customer_name" =>$this->customer_name,
            "ref"=>$random_value
        ];
        $payload = json_encode($arr);
        logger('ps buy elect request: '.$payload);
        $curl = curl_init();
        curl_setopt_array($curl, array(
           CURLOPT_URL => 'https://api.payscribe.ng/api/v1/electricity/vend',
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_ENCODING => '',
           CURLOPT_MAXREDIRS => 10,
           CURLOPT_TIMEOUT => 0,
           CURLOPT_FOLLOWLOCATION => true,
           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
           CURLOPT_CUSTOMREQUEST => 'POST',
           CURLOPT_POSTFIELDS =>$payload,
           CURLOPT_HTTPHEADER => array(
              'Authorization: Bearer '.$this->token,
              'Content-Type: application/json',
              'Cookie: ci_session=b06a38f638b1c32feb86c93d281d5fd841e19670'
           ),
        ));
        
        $response = curl_exec($curl);       
        curl_close($curl);
        logger('PAYSCRIBE BUY. ELECT: '.$response);


        $response_decode = json_decode($response,true);

        if( isset($response_decode['status']) && $response_decode['status'] == TRUE){
            $message = $response_decode['description'] ?? 'Successful';
            $token = $response_decode['message']['details']['token'] ?? null;
            return [
                'status' => 1,
                'user_message' => $message,
                'admin_message' => $response,
                'token' => $token,
            ];
        }else{
            $message = $response_decode['description'] ?? 'Transaction Failed';
            return [
                'status' => -1,
                'user_message' => $message ,
                'admin_message' => $response,
                'token' => null,
            ];
        }

    }

    public function buyCable(){
        
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

        // $apikey = 'ps_pk_live_qkzRZMdBOe8JPsvVsswtZhAF93nKWaKgP1I';
        $apikey = $this->token;
        $random_value = rand(1111,9999).'_rand'.time();
        $plan_id = $plan_details->automation_product_plan_id;
        $service = $this->getProviderCableSlug($plan_details->product_plan_category->product_plan_category_name);
 
        $arr = [
            "plan_id" =>$plan_id,
            "customer_name"=>$this->customer_name,
            "account"=>$this->smart_card_number,
            "service"=>$service,
            "ref"=>$random_value,
            "phone"=>$this->mobile_number,
            "email"=>$this->email,
            "month"=>1
        ];
        $payload = json_encode($arr);
        logger('ps buy cable request: '.$payload);
        $curl = curl_init();
        curl_setopt_array($curl, array(
           CURLOPT_URL => 'https://api.payscribe.ng/api/v1/multichoice/vend',
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_ENCODING => '',
           CURLOPT_MAXREDIRS => 10,
           CURLOPT_TIMEOUT => 0,
           CURLOPT_FOLLOWLOCATION => true,
           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
           CURLOPT_CUSTOMREQUEST => 'POST',
           CURLOPT_POSTFIELDS =>$payload,
           CURLOPT_HTTPHEADER => array(
              'Authorization: Bearer '.$apikey,
              'Content-Type: application/json',
              'Cookie: ci_session=b06a38f638b1c32feb86c93d281d5fd841e19670'
           ),
        ));
        $response = curl_exec($curl);       
        curl_close($curl);
        logger('PAYSCRIBE BUY. CABLE: '.$response);
        $response_decode = json_decode($response,true);

        if( isset($response_decode['status']) && $response_decode['status'] == TRUE){
            $message = $response_decode['description'] ?? 'Successful';
            return [
                'status' => 1,
                'user_message' => $message,
                'admin_message' => $response,
            ];
        }else{
            $message = $response_decode['description'] ?? 'Transaction Failed';
            return [
                'status' => -1,
                'user_message' => $message,
                'admin_message' => $response, 
            ];
        }

    }


}
