<?php

namespace App\Services\Automation;

use App\Models\User;
use App\Models\Network;
use App\Models\Automation;
use App\Models\ProductPlan;
use App\Models\RecurringFailedMessagePattern;

class PaultechsAutomation{

    private $network_id;

    private $automation_id;

    private $api_id;

    private $plan_api_id;

    private $mobile_number;

    private $token;

    private $url;


    // private $ported_number;


    public function __construct($data){
        $this->automation_id = $data['automation_id'];
        $this->network_id = $data['network_id']  ?? '';
        $this->smart_card_number = $data['smart_card_number']  ?? '';
        $this->plan_id = $data['plan_id'];
        $this->mobile_number = $data['phone_number'] ?? '';
        $this->token = $data['token'] ?? '';
        $this->url = $data['url'] ?? '';
        $this->amount = $data['amount'] ?? 0;
        $this->user_id = $data['user_id'] ?? '';
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
        $network_name = $network_details->network_name; 
        $array = [
            "plan"=> $automation_plan_id,
            "mobile_number"=>$this->mobile_number,
            "type"=> strtoupper($network_name)
        ];
        $encoded_array = json_encode($array);
        $header_array = array(
            'Authorization: Token '.$this->token,
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

       if(isset($response_dec['status']) && $response_dec['status'] == 'Successful'){
            //success
            return [
                'status' => 1,
                'user_message' => $response_dec['api_response'] ?? 'Successfully done transaction',
                'admin_message' => $response,
            ];
        }else{
            $usermsg = $response_dec['message'] ?? 'Transaction failed...';
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
        $product_namee = strtolower($plan_details->product_plan_category->product_plan_category_name);
        logger('API ID: '.$product_namee);


        //test
        //HHARDCODED
        $array = [
            "cablename" => $product_namee,
            // "cableplan" => $automation_product_plan_id,
            "iuc_number" => $smart_card_number
        ];
        $encoded_array = json_encode($array);
        logger($encoded_array);

        $header_array = array(
            'Authorization: Token '.$this->token,
            'Content-Type: application/json'
        );
        $header_json = json_encode($header_array);
        logger($header_json);

        $curl = curl_init();
        curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => "https://paultechs.com/api/validate/iuc/",
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
        curl_close($curl);
        logger('PAUTECHSCABLE: '.$response);


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

    // public function buyAirtime(){
        
    //     $plan_details = ProductPlan::with('product_plan_category.network')
    //     ->where('visibility',1)
    //     ->where('id',$this->plan_id)->first();
    //     if(! $plan_details){
    //         return [
    //             'status' => -1,
    //             'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
    //             'admin_message' => 'Wrong plan Id',
    //         ];
    //     }

    //     $network_details = Network::where('visibility',1)->where('id',$this->network_id)->first();
    //     if(! $network_details){
    //         return [
    //             'status' => -1,
    //             'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
    //             'admin_message' => 'Network ID is likely not available or set to hidden',
    //         ];
    //     }


    //     $network_name = $network_details->network_name;
    //     $api_network_id = $this->getNetworkId($network_name);
    //     if($api_network_id == -1){
    //         //not found
    //         return [
    //             'status' => -1,
    //             'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
    //             'admin_message' => 'Api Network ID is wrong',
    //         ];
    //     }


    //     $automation_plan_id = $plan_details->automation_product_plan_id; 
    //     $array = [
    //         "network"=>$api_network_id,
    //         "amount"=>$this->amount,
    //         "mobile_number"=>$this->mobile_number,
    //         "airtime_type"=> $automation_plan_id,
    //         "Ported_number"=>false
    //     ];

    //     $encoded_array = json_encode($array);
    //     $header_array = array(
    //         'Authorization: Token '.$this->token,
    //         'Content-Type: application/json'
    //     );
    //     $header_json = json_encode($header_array);

    //     $curl = curl_init();
    //     curl_setopt_array(
    //     $curl,
    //     array(
    //         CURLOPT_URL => "$this->url",
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => '',
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 0,
    //         CURLOPT_FOLLOWLOCATION => true,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST => 'POST',
    //         CURLOPT_POSTFIELDS => $encoded_array,
    //         CURLOPT_HTTPHEADER => $header_array
    //     )
    //     );
    //     $response = curl_exec($curl);
    //     $response_dec = json_decode($response,true);
    //     $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    //     logger($response);

    //     if(isset($response_dec['Status']) && $response_dec['Status'] == 'successful'){
    //         //success
    //         return [
    //             'status' => 1,
    //             'user_message' => $response_dec['api_response'],
    //             'admin_message' => $response,
    //         ];
    //     }else{

    //         $usermsg = isset($response_dec['Status']) ? $response_dec['Status'] : "Sorry, transaction failed. Please try again";
    //         if(env('APP_NAME') == 'OresamSub'){
             
    //             //ORESAMSUB ONLY FOR NOW
    //             $user_message_to_check_with_pattern = $usermsg;
    //             $check_if_response_matches = RecurringFailedMessagePattern::where('message','like','%'.$user_message_to_check_with_pattern.'%')->first();
    //             if($check_if_response_matches){
    //                 // option 1
    //                 $plan_details->update([
    //                     'visibility' => 0,
    //                     'public_visibility' => 0,
    //                     'active_status' => 0,
    //                 ]);
    //                 $usermsg = 'Sorry, transaction failed. Please try again.';
                    
    //                 // option 2 - LATER
    //                 // //it means the response exists, so PEND.
    //                 // return [
    //                 //     'status' => 0,
    //                 //     'user_message' => 'Transaction is being processed',
    //                 //     'admin_message' => $response,
    //                 // ];
    //             }
    //         }

    //         return [
    //             'status' => -1,
    //             'user_message' =>$usermsg,
    //             'admin_message' => $response,
    //         ];
    //     }        
    // }

}
