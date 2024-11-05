<?php

namespace App\Services\Automation\MegaSubPlugAutomation;

use App\Models\User;
use App\Models\Automation;
use App\Models\ProductPlan;
use Illuminate\Support\Facades\Http;

class MegaSubCableTV{

    private $smart_card_number;

    private $cable_plan_api_id;

    private $amount;

    private $data_api_id;

    private $automation_slug = 'megasubplug';

    private $validatephonenetwork = 1;

    private $plan_id = '';

    private $validation_customer_name = '';
    private $no_of_slots = '';

    private $product_plan_category_name = '';
    private $duplication_check = 0;

    private $api_key = '';
    private $api_password = '';

    private $user_id = '';

    public function __construct($smart_card_number = null,$plan_id = null,$amount= null,$validation_customer_name = null,$no_of_slots = null,$product_plan_category_name = null,$user_id = null){
        $this->smart_card_number = $smart_card_number;
        $this->validation_customer_name = $validation_customer_name;
        $this->user_id = $user_id;
        $this->plan_id = $plan_id;
        $this->no_of_slots = $no_of_slots;
        $this->product_plan_category_name = $product_plan_category_name;
        $this->amount = $amount;
        $this->duplication_check = '0';
        $this->api_key = Automation::where('slug','megasubplug')->first()->api_public_key;
        $this->api_password = Automation::where('slug','megasubplug')->first()->api_password;
      
    }
    

    protected function getProviderApiID($product_plan_category_name){
        //TODO: optimize this later... very important
        $product_plan_category_name = strtolower($product_plan_category_name);
        if($product_plan_category_name == 'gotv'){
            return 9;
        }

        if($product_plan_category_name == 'dstv'){
            return 10;
        }

        if($product_plan_category_name == 'startimes'){
            return 11;
        }


        return -1;
    }

    public function validateSmartCardNumber(){
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
                'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
                'admin_message' => 'Wrong plan Id',
            ];
        }

           // $this->cable_plan_api_id = $this->getProviderApiID($plan_details->product_plan_category->product_plan_category_name);
           $this->cable_plan_api_id = $plan_details->automation_product_plan_id;
           $smart_card_number = $this->smart_card_number;
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://megasubplug.com/API/?action=validate_cable&smart_card_number='.$smart_card_number.'&cable_plan_api_id='.$this->cable_plan_api_id.'',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Password: '.$this->api_password,
            'Authorization: '.$this->api_key
        ),
        ));

        $response = curl_exec($curl);

        // logger($response);

        curl_close($curl);

        $response_decode = json_decode($response,true);

        if(isset($response_decode['Detail']['success']) && $response_decode['Detail']['success'] == true && isset($response_decode['Status']) && $response_decode['Status'] == 'Success' && isset($response_decode['Detail']['customer_name']) 
        &&  $response_decode['Detail']['customer_name'] != ''  ){
           //successful transaction
           return [
               'status' => 1,
               'address' => isset($response_decode['Detail']['customer_address']) ? $response_decode['Detail']['customer_address']  :  'Address not found',
               'name' => isset($response_decode['Detail']['customer_name']) ? $response_decode['Detail']['customer_name']  :  'Defaulted to: '.$user_details->first_name.' '.$user_details->last_name,
           ];
       }else{
            
            return [
                'status' => 1,
                'address' =>  'Address not found1',
                'name' => 'Defaulted to: '.$user_details->first_name.' '.$user_details->last_name,
           ];
       }

    }
    public function buyCable(){
        
        $plan_details = ProductPlan::with('product_plan_category')
        ->where('visibility',1)
        ->where('id',$this->plan_id)->first();
      
        if(! $plan_details){
            return [
                'status' => -1,
                'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
                'admin_message' => 'Wrong plan Id',
            ];
        }

        $this->cable_plan_api_id = $plan_details->automation_product_plan_id;

        $smart_card_number = $this->smart_card_number;
        $validation_customer_name = urlencode($this->validation_customer_name);
        $cable_plan_api_id = $this->cable_plan_api_id;
        // $duplication_check = $this->duplication_check;
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://megasubplug.com/API/?action=buy_cable&smart_card_number=$smart_card_number&validation_customer_name=$validation_customer_name&cable_plan_api_id=$cable_plan_api_id&duplication_check=0",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('action' => 'buy_cable','smart_card_number' => $smart_card_number,'validation_customer_name' => $validation_customer_name,'cable_plan_api_id' => $cable_plan_api_id,'duplication_check' => '0'),
        CURLOPT_HTTPHEADER => array(
            'Authorization: '.$this->api_key,
            'Password: '.$this->api_password,
            'Cookie: PHPSESSID=49f8lqv734slho0cegjgk3rlsk'
        ),
        ));

        $response = curl_exec($curl);

        logger($response);

        curl_close($curl);
    
        $response_decode = json_decode($response,true);
        

        if(isset($response_decode['Status']) && $response_decode['Status'] == 'Success' && isset($response_decode['Detail']['info']['Success']) 
         &&  $response_decode['Detail']['info']['Success'] == '1'  ){
            //successful transaction
            return [
                'status' => 1,
                'user_message' => isset($response_decode['Detail']['info']['Detail']) ? 'Transaction was successfully processed' :  'Transaction was successful',
                'admin_message' => $response
            ];
        }

        $error = isset($response_decode['Detail']['error']) ? $response_decode['Detail']['error'] : ''; 
        return [
            'status' => -1,
            'user_message' => isset($response_decode['Detail']['message']) ? $response_decode['Detail']['message'].'_'.$error :  'Transaction failed_'.$error,
            'admin_message' => $response
        ];

        //REAL
        
    }
}
