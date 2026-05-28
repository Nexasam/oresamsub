<?php

namespace App\Services\Automation;

use App\Models\Network;
use App\Models\Automation;
use App\Models\ProductPlan;
use App\Models\RecurringFailedMessagePattern;

class DirectCouponAutomation{

    private $network_id;

    private $automation_id;
    private $automation_plan_id;
    private $plan_id;

    private $mobile_number;

    private $token;

    private $url;

    private $amount;
    private $coupon;
    private $api_key;
    private $api_secret;    
    private $automation_details;

    private $validatephonenetwork;


    // public function __construct($data){
    //     $this->automation_id = $data['automation_id'];
    //     $this->network_id = $data['network_id'];
    //     $this->plan_id = $data['plan_id'];
    //     $this->mobile_number = $data['phone_number'];
    //     $this->token = $data['token'];
    //     $this->url = $data['url'];
    //     $this->amount = $data['amount'] ?? 0;
    // }

    public function __construct(array $data)
    {
        $this->amount = $data['amount'] ?? 0;
    
        // ✅ standardize to ONE field
        $this->mobile_number = $data['phone_number'] ?? null;
    
        $this->coupon = $data['coupon'] ?? null;
        $this->plan_id = $data['plan_id'] ?? null;
    
        $this->validatephonenetwork = 0;
    
        // ✅ credentials
        $this->token = $data['token'] ?? null;
        $this->api_key = $data['api_key'] ?? null;
        $this->api_secret = $data['api_secret'] ?? null;
    
        // ✅ endpoint
        $this->url = $data['url'] ?? null;
    
        // ✅ automation mapping
        $this->automation_id = $data['automation_id'] ?? null;
        $this->automation_plan_id = $data['automation_plan_id'] ?? null;
    
        // ✅ optional extra config
        $this->automation_details = $data['automation_details'] ?? null;
    }

    public function buyData(){
        
        $plan_details = ProductPlan::with('product_plan_category.network')
        // ->where('visibility',1)
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

        $automation_plan_id = $this->automation_plan_id;
        //use default.
        if($automation_plan_id == 'nil'){
            $automation_plan_id =  $plan_details->automation_product_plan_id;
        } 
        
        $custom_ref = substr(uniqid(rand(), true), 0, 15);
        $array = [
            "custom_ref"=>$custom_ref,
            "phone_number"=>$this->mobile_number,
            "plan_id"=> $this->automation_plan_id,
            "ported_number"=>true
        ];
        $encoded_array = json_encode($array);
        $header_array = array(
            'Authorization: Token '.$this->api_key,
            'Content-Type: application/json'
        );
        $header_json = json_encode($header_array);

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
        // logger('DirectCoupon:'.$response);

        if(isset($response_dec['code']) && $response_dec['code'] == 'X000'){
            //success
            return [
                'status' => 1,
                'user_message' => $response_dec['rtr'] ?? 'Successfully done transaction',
                'admin_message' => $response,
            ];
        }else{

            $usermsg = isset($response_dec['desc']) ? $response_dec['desc'] : "Sorry, transaction failed. Please try again";
            if(env('APP_NAME') == 'OresamSub'){
             
                //ORESAMSUB ONLY FOR NOW
                $user_message_to_check_with_pattern = $usermsg;
                $check_if_response_matches = RecurringFailedMessagePattern::where('message','like','%'.$user_message_to_check_with_pattern.'%')->first();
                if($check_if_response_matches){
                    // option 1
                    $plan_details->update([
                        'visibility' => 0,
                        'public_visibility' => 0,
                        'active_status' => 0,
                    ]);
                    $usermsg = 'Sorry, transaction failed. Please try again.';
                }
            }

            return [
                'status' => -1,
                'user_message' =>$usermsg,
                'admin_message' => $response,
            ];
        }        
    }

}
