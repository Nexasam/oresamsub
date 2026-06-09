<?php

namespace App\Services\Automation;

use App\Models\User;
use App\Models\Network;
use App\Models\Automation;
use App\Models\ProductPlan;
use App\Models\RecurringFailedMessagePattern;

class DataAutomation{


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



    public function buyData($vendor_record = null,$input_phone_number = '',$vendor_plan_id,$ported_number = true,$input_network = '', $reference){
        
        $this->input_phone_number = $input_phone_number;
        $this->vendor_record = $vendor_record;
        $this->vendor_plan_id = $vendor_plan_id;
        $this->ported_number = $ported_number;
        $this->input_network = $input_network;
        $this->reference = $reference;

        $network_details = Network::where('visibility',1)->where('network_name',$this->input_network)->first();
        if(! $network_details){
            return [
                'status' => -1,
                'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
                'admin_message' => 'Network ID is likely not available or set to hidden',
            ];
        }

        $request_url = $vendor_record->endpoint_url;
        $request_params = $vendor_record->request_params;
        $request_params_decode = json_decode($request_params,true);

        $headers_params = $vendor_record->headers;
        $headers_params_decode = json_decode($headers_params,true);

        $networkdecode = json_decode($vendor_record->network_plans,true);
        $network = $networkdecode[$this->input_network] ?? '1'; //should not run default

        $success_conditions = $vendor_record->success_conditions; // stored as JSON
        $success_conditions_decode = json_decode($success_conditions,true); // stored as JSON

        
        $plan = $this->vendor_plan_id;
        $request_url = $vendor_record->endpoint_url;
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
            } elseif($value == 1){  // ported number
                $new_request_params[$key] = true;
            } elseif($value == 'plan'){
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
        // logger('response: '.$response);
        curl_close($curl);

        $response_dec = json_decode($response,true);
        $allPassed = true;
        foreach ($success_conditions_decode as $scondition) {
            $temp = $response_dec;
            foreach (explode('.', $scondition['key']) as $k) {
                if (!isset($temp[$k])) { $temp = null; break; }
                $temp = $temp[$k];
            }
            if ($temp != $scondition['value']) { $allPassed = false; break; }
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
            ]
            : [
                'status' => -1,
                'user_message' => $failed_message,
                'admin_message' => $response,
            ];
        


    }

   
}
