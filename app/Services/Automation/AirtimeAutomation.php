<?php
namespace App\Services\Automation;

use App\Models\Network;
use App\Models\ProductPlan;

class AirtimeAutomation{


    private $input_phone_number;

    private $vendor_record;

    private $vendor_plan_id;

    private $ported_number;

    private $input_network;

    private $reference;

    //below are still old for now


    private function getNestedValue($array, $path) {
        $keys = explode('.', $path);
        $temp = $array;
        foreach ($keys as $k) {
            if (!isset($temp[$k])) return null;
            $temp = $temp[$k];
        }
        return $temp;
    }

    private function safeDecode($value) {
        return is_array($value) ? $value : json_decode($value, true);
    }



    //work on this later
    public function buyAirtime($vendor_record = null,$input_phone_number = '',$vendor_plan_id,$ported_number = true,$input_network = '', $reference){
        
        $this->input_phone_number = $input_phone_number;
        $this->vendor_record = $vendor_record;
        $this->vendor_plan_id = $vendor_plan_id;
        $this->ported_number = $ported_number;
        $this->input_network = $input_network;
        $this->reference = uniqid('sdd-'.time().rand(111111,999999));

        $network_details = Network::where('visibility',1)->where('network_name',$this->input_network)->first();
        if(! $network_details){
            return [
                'status' => -1,
                'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
                'admin_message' => 'Network ID is likely not available or set to hidden',
            ];
        }

      

        $request_params_decode = $vendor_record->request_params;
        // $request_params_decode = json_decode($request_params,true);

        $headers_params_decode = $vendor_record->request_headers;
        // $headers_params_decode = json_decode($headers_params,true);

        $networkdecode = $vendor_record->network_plans;
        $network = $networkdecode[$this->input_network] ?? '1'; //should not run default

        $success_conditions_decode = $vendor_record->success_condition; // stored as JSON
        // $success_conditions_decode = json_decode($success_conditions,true); 

        
        $plan = $this->vendor_plan_id;
        $request_url = $vendor_record->data_url;
        $new_request_params = [];


        //now lets loop request params
        $new_request_params = [];



        foreach($request_params_decode as $param){
            $key = $param['key'];
            $value = $param['value'];
        
            if($value == 'phone_number'){
                $new_request_params[$key] = $input_phone_number;
            } elseif($value == 'network'){
                $new_request_params[$key] = $network;
            }
            elseif($value == 'reference'){
                $new_request_params[$key] = $this->reference;
            } 
            elseif($value == 'ported_number'){
                $new_request_params[$key] = true; //true
            } 
            elseif($value == "action"){  // ported number

                //unique only to bilink
                if($vendor_record->slug == 'bilink'){
                    $new_request_params[$key] = "vend";
                }else{
                    $new_request_params[$key] = true;
                }
            } 
            
            elseif($value == 'plan'){
                $new_request_params[$key] = $plan;
            } else {
                $new_request_params[$key] = $value;
            }
        }
        $encoded_array = json_encode($new_request_params);

        

        //now lets loop header params
        $new_headers_arr = [];
        foreach($headers_params_decode as $item){
            $keyy = $item['key'];
            $valuee = $item['value'];
            $new_headers_arr[] =  "$keyy:$valuee";
        }
      


        $curl = curl_init();
        curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => "$request_url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $encoded_array,
            CURLOPT_HTTPHEADER => $new_headers_arr
        )
        );
        $response = curl_exec($curl);
        $curl_error = curl_error($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // logger('curl http code: '.$httpcode);
        // logger('curl error: '.$curl_error);
        logger('dataaaaaresponse: '.$response);
        curl_close($curl);

        $response_dec = json_decode($response,true);
     
     
     
        //former
        // $allPassed = true;
        // foreach ($success_conditions_decode as $scondition) {
        //     $temp = $response_dec;
        //     foreach (explode('.', $scondition['key']) as $k) {
        //         if (!isset($temp[$k])) { $temp = null; break; }
        //         $temp = $temp[$k];
        //     }
        //     if ($temp != $scondition['value']) { $allPassed = false; break; }
        // }


        //updated
        $allPassed = true;

        foreach ($success_conditions_decode as $scondition) {

            $temp = $response_dec;

            foreach (explode('.', $scondition['key']) as $k) {
                if (!isset($temp[$k])) {
                    $temp = null;
                    break;
                }
                $temp = $temp[$k];
            }

            // 🔥 normalize ONLY here
            $actual = $temp;
            $expected = $scondition['value'];

            if (is_string($actual)) {
                $actual = strtolower(trim($actual));
                if ($actual === 'true') $actual = true;
                if ($actual === 'false') $actual = false;
            }

            if (is_string($expected)) {
                $expected = strtolower(trim($expected));
                if ($expected === 'true') $expected = true;
                if ($expected === 'false') $expected = false;
            }

            if ($actual != $expected) {
                $allPassed = false;
                break;
            }
        }
        
        $success_message = match(count($steps = explode('.', $vendor_record->success_response))) {
            1 => $response_dec[$steps[0]] ?? 'Transaction was successful',
            2 => $response_dec[$steps[0]][$steps[1]] ?? 'Transaction was successful',
            3 => $response_dec[$steps[0]][$steps[1]][$steps[2]] ?? 'Transaction was successful',
            default => 'Transaction was successful',
        };
        
        $failed_message = match(count($steps = explode('.', $vendor_record->failed_response))) {
            1 => $response_dec[$steps[0]] ?? 'Transaction failed',
            2 => $response_dec[$steps[0]][$steps[1]] ?? 'Transaction failed',
            3 => $response_dec[$steps[0]][$steps[1]][$steps[2]] ?? 'Transaction failed',
            default => 'Transaction failed',
        };
        
        return $allPassed
            ? [
                'status' => 1,
                'user_message' => $success_message,
                'admin_message' => $response,
                // 'token' => $
            ]
            : [
                'status' => -1,
                'user_message' => $failed_message,
                'admin_message' => $response,
            ];
        


    }


    public function buySimServerAirtime($vendor_record = null,$input_phone_number = '',$amount,$vendor_plan_id,$ported_number = true, $reference){
        
        $plan_details = ProductPlan::with('product_plan_category.network')
        ->where('visibility',1)
        ->where('id',$vendor_plan_id)->first();
        if(! $plan_details){
            return [
                'status' => -1,
                'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
                'admin_message' => 'Wrong plan Id',
            ];
        }

    
        $ref = uniqid('simserver_');
        $automation_plan_id = $plan_details->automation_product_plan_id; 
        $array = [
            // "network"=>$api_network_id,
            // "amount"=>$this->amount,
            // "mobile_number"=>$this->mobile_number,
            // "airtime_type"=> $automation_plan_id,
            // "Ported_number"=>false
            "amount"=>$amount,
            "product_code"=>$automation_plan_id,
            "phone_number"=>$input_phone_number,
            "action"=>"vend",
            "user_reference"=>$reference,
            "async"=>$ported_number,
            "callback"=>"https://bilink.ng/autobiz_vending_index.php"
        ];

        $encoded_array = json_encode($array);
        logger('sim Encoded arr: '.$encoded_array);
        $header_array = array(
            'Authorization: Bearer '.$vendor_record->api_password,
            'Content-Type: application/json'
        );
        $header_json = json_encode($header_array);
        logger('sim header: '.$header_json);

        $curl = curl_init();
        curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => "$vendor_record->endpoint_url",
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

    public function buySimServerAirtimesdfs($vendor_record = null,$input_phone_number = '',$vendor_plan_id,$ported_number = true,$input_network = '', $reference){
        
        $this->input_phone_number = $input_phone_number;
        $this->vendor_record = $vendor_record;
        $this->vendor_plan_id = $vendor_plan_id;
        $this->ported_number = $ported_number;
        $this->input_network = $input_network;
        $this->reference = uniqid('sdd-'.time().rand(111111,999999));

        $network_details = Network::where('visibility',1)->where('network_name',$this->input_network)->first();
        if(! $network_details){
            return [
                'status' => -1,
                'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
                'admin_message' => 'Network ID is likely not available or set to hidden',
            ];
        }

      

        $request_params_decode = $vendor_record->request_params;
        // $request_params_decode = json_decode($request_params,true);

        $headers_params_decode = $vendor_record->request_headers;
        // $headers_params_decode = json_decode($headers_params,true);

        $networkdecode = $vendor_record->network_plans;
        $network = $networkdecode[$this->input_network] ?? '1'; //should not run default

        $success_conditions_decode = $vendor_record->success_condition; // stored as JSON
        // $success_conditions_decode = json_decode($success_conditions,true); 

        
        $plan = $this->vendor_plan_id;
        $request_url = $vendor_record->data_url;
        $new_request_params = [];


        //now lets loop request params
        $new_request_params = [];



        foreach($request_params_decode as $param){
            $key = $param['key'];
            $value = $param['value'];
        
            if($value == 'phone_number'){
                $new_request_params[$key] = $input_phone_number;
            } elseif($value == 'network'){
                $new_request_params[$key] = $network;
            }
            elseif($value == 'reference'){
                $new_request_params[$key] = $this->reference;
            } 
            elseif($value == 'ported_number'){
                $new_request_params[$key] = true; //true
            } 
            elseif($value == "action"){  // ported number

                //unique only to bilink
                if($vendor_record->slug == 'bilink'){
                    $new_request_params[$key] = "vend";
                }else{
                    $new_request_params[$key] = true;
                }
            } 
            
            elseif($value == 'plan'){
                $new_request_params[$key] = $plan;
            } else {
                $new_request_params[$key] = $value;
            }
        }
        $encoded_array = json_encode($new_request_params);

        

        //now lets loop header params
        $new_headers_arr = [];
        foreach($headers_params_decode as $item){
            $keyy = $item['key'];
            $valuee = $item['value'];
            $new_headers_arr[] =  "$keyy:$valuee";
        }
      


        $curl = curl_init();
        curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => "$request_url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $encoded_array,
            CURLOPT_HTTPHEADER => $new_headers_arr
        )
        );
        $response = curl_exec($curl);
        $curl_error = curl_error($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // logger('curl http code: '.$httpcode);
        // logger('curl error: '.$curl_error);
        logger('dataaaaaresponse: '.$response);
        curl_close($curl);

        $response_dec = json_decode($response,true);
     
     
     
        //former
        // $allPassed = true;
        // foreach ($success_conditions_decode as $scondition) {
        //     $temp = $response_dec;
        //     foreach (explode('.', $scondition['key']) as $k) {
        //         if (!isset($temp[$k])) { $temp = null; break; }
        //         $temp = $temp[$k];
        //     }
        //     if ($temp != $scondition['value']) { $allPassed = false; break; }
        // }


        //updated
        $allPassed = true;

        foreach ($success_conditions_decode as $scondition) {

            $temp = $response_dec;

            foreach (explode('.', $scondition['key']) as $k) {
                if (!isset($temp[$k])) {
                    $temp = null;
                    break;
                }
                $temp = $temp[$k];
            }

            // 🔥 normalize ONLY here
            $actual = $temp;
            $expected = $scondition['value'];

            if (is_string($actual)) {
                $actual = strtolower(trim($actual));
                if ($actual === 'true') $actual = true;
                if ($actual === 'false') $actual = false;
            }

            if (is_string($expected)) {
                $expected = strtolower(trim($expected));
                if ($expected === 'true') $expected = true;
                if ($expected === 'false') $expected = false;
            }

            if ($actual != $expected) {
                $allPassed = false;
                break;
            }
        }
        
        $success_message = match(count($steps = explode('.', $vendor_record->success_response))) {
            1 => $response_dec[$steps[0]] ?? 'Transaction was successful',
            2 => $response_dec[$steps[0]][$steps[1]] ?? 'Transaction was successful',
            3 => $response_dec[$steps[0]][$steps[1]][$steps[2]] ?? 'Transaction was successful',
            default => 'Transaction was successful',
        };
        
        $failed_message = match(count($steps = explode('.', $vendor_record->failed_response))) {
            1 => $response_dec[$steps[0]] ?? 'Transaction failed',
            2 => $response_dec[$steps[0]][$steps[1]] ?? 'Transaction failed',
            3 => $response_dec[$steps[0]][$steps[1]][$steps[2]] ?? 'Transaction failed',
            default => 'Transaction failed',
        };
        
        return $allPassed
            ? [
                'status' => 1,
                'user_message' => $success_message,
                'admin_message' => $response,
                // 'token' => $
            ]
            : [
                'status' => -1,
                'user_message' => $failed_message,
                'admin_message' => $response,
            ];
        


    }

    

   
}
