<?php

namespace App\Services\Automation\MegaSubPlugAutomation;

use App\Models\Automation;
use App\Models\ProductPlan;
use App\Models\RecurringFailedMessagePattern;

class MegaSubVendData{

    private $mobile_number;

    private $network_api_id;

    private $data_api_id;


    private $automation_slug = 'megasubplug';

    private $duplication_check = 1;

    private $validatephonenetwork = 0;

    private $plan_id = '';


    private $api_key = '';
    private $api_password = '';

    public function __construct($mobile_number,$plan_id,$validatephonenetwork = 0){
        $this->mobile_number = $mobile_number;
        $this->plan_id = $plan_id;
        $this->validatephonenetwork = 0;
        $this->api_key = Automation::where('slug','megasubplug')->first()->api_public_key;
        $this->api_password = Automation::where('slug','megasubplug')->first()->api_password;
      
    }
    

    protected function getNetworkApiID($network_name){
        $network_name = strtolower($network_name);
        if($network_name == 'mtn'){
            return 1;
        }

        if($network_name == 'glo'){
            return 2;
        }

        if($network_name == 'airtel'){
            return 3;
        }

        if($network_name == '9mobile'){
            return 4;
        }

        return -1;
    }
    public function buyData(){
        
        $plan_details = ProductPlan::with('product_plan_category.network')
        ->where('visibility',1)
        ->where('id',$this->plan_id)
        ->first();
      
        //debugging/testing
        // return [
        //     'status' => -1,
        //     'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
        //     'admin_message' => 'Wrong plan Id',
        //     'testing' => $plan_details
        // ];

        if(! $plan_details){
            return [
                'status' => -1,
                'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
                'admin_message' => 'Wrong plan Id',
            ];
        }

     

        $network_id = $plan_details->product_plan_category->network->id;
        $network_name = $plan_details->product_plan_category->network->network_name;
        
        $data_api_id = $plan_details->automation_product_plan_id;
        $network_api_id = $this->getNetworkApiID($network_name);

        if($network_api_id == -1){
            //not found
            return [
                'status' => -1,
                'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
                'admin_message' => 'Api Id supplied is not found - 1:mtn 2:glo 3:airtel 4:9mobile',
            ];

        }

        $this->network_api_id = $network_api_id;
        $this->data_api_id = $data_api_id;

        $curl = curl_init();
        $url = 'https://megasubplug.com/API/?action=buy_data&mobile_number='.$this->mobile_number.'&network_api_id='.$this->network_api_id.'&data_api_id='.$this->data_api_id.'&validatephonenetwork='.$this->validatephonenetwork.'&duplication_check='.$this->duplication_check.'';
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array('action' => 'buy_data','mobile_number' => $this->mobile_number,'network_api_id' => $this->network_api_id,'data_api_id' => $this->data_api_id,'validatephonenetwork' => $this->validatephonenetwork,'duplication_check' => $this->duplication_check),
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$this->api_key,
            'Password: '.$this->api_password,
            // 'Cookie: PHPSESSID=h2vh7clslap9nukf5kt5qagh0d'
          ),
        ));
        
        $response = curl_exec($curl);
        // logger($response);
        
        curl_close($curl);
       
        $response_decode = json_decode($response,true);

        if(isset($response_decode['Status']) && $response_decode['Status'] == 'Success' && isset($response_decode['Detail']['info']['Success']) 
         &&  $response_decode['Detail']['info']['Success'] == '1'  ){
            //successful transaction: initially real_response
            return [
                'status' => 1,
                'user_message' => isset($response_decode['Detail']['message']) ? $response_decode['Detail']['message'] :  'Transaction was successful',
                'admin_message' => $response
            ];
        }
 
        $error = isset($response_decode['Detail']['error']) ? $response_decode['Detail']['error'] : ''; 


        $usermsg = isset($response_decode['Detail']['message']) ? $response_decode['Detail']['message'].'_'.$error :  'Transaction failed_'.$error;

        if(env('APP_NAME') == 'OresamSub'){
            $status = 0;
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

                // option 2 - LATER
                // //it means the response exists, so PEND.
                // return [
                //     'status' => 0,
                //     'user_message' => 'Transaction is being processed',
                //     'admin_message' => $response,
                // ];
            }
        }
        
        return [
            'status' => -1,
            'user_message' => $usermsg,
            'admin_message' => $response,
        ];
        
    }
}
