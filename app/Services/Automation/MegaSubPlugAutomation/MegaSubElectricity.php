<?php

namespace App\Services\Automation\MegaSubPlugAutomation;

use App\Models\User;
use App\Models\Automation;
use App\Models\ProductPlan;
use Illuminate\Support\Facades\Http;

class MegaSubElectricity{

    private $metre_number;
    private $electricity_plan_api_id;
    private $amount;
    private $phone_number;
    private $automation_slug = 'megasubplug';
    private $validatephonenetwork = 1;
    private $plan_id = '';
    private $extra_info = '';
    private $no_of_slots = '';

    private $product_plan_category_name = '';
    private $duplication_check = 0;

    private $api_key = '';
    private $api_password = '';

    private $user_id = '';


    public function __construct($metre_number,$plan_id = null,$amount = null,$extra_info = null,$no_of_slots = 1,$product_plan_category_name = null,$phone_number = null, $user_id = null){
        $this->metre_number = $metre_number; //metreno
        $this->user_id = $user_id;
        $this->extra_info = $extra_info;
        $this->plan_id = $plan_id;
        $this->phone_number = $phone_number;
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
        if($product_plan_category_name == 'prepaid'){
            return 23;
        }

        if($product_plan_category_name == 'postpaid'){
            return 31;
        }

        return -1;
    }


    public function validateMetreNumber(){
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
        $metre_number = $this->metre_number;
        logger('API ID'.$automation_product_plan_id);

        
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://megasubplug.com/API/?action=validate_metre&metre_number='.$metre_number.'&electricity_plan_api_id='.$automation_product_plan_id.'',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Password: '.$this->api_password,
            'Authorization: '.$this->api_key,
            'Cookie: PHPSESSID=j9408e536n6vurmrla0omj4hob'
        ),
        ));

        $response = curl_exec($curl);

        logger($response);

        curl_close($curl);

        $response_decode = json_decode($response,true);

        // && isset($response_decode['Detail']['customer']['status']) 
        // &&  $response_decode['Detail']['customer']['status'] == 200 
        if(isset($response_decode['Status']) && $response_decode['Status'] == 'Success' ){
           //successful transaction
           return [
               'status' => 1,
               'address' => isset($response_decode['Detail']['customer']['customerName']) ? $response_decode['Detail']['customer']['customerAddress']  :  'Address not found',
               'name' => isset($response_decode['Detail']['customer']['customerName']) ? $response_decode['Detail']['customer']['customerName']  :  'Defaulted to: '.$user_details->first_name.' '.$user_details->last_name,
               'message' =>  isset($response_decode['Detail']['customer']['customerName']) ? $response_decode['Detail']['customer']['customerName']  :  'Defaulted to: '.$user_details->first_name.' '.$user_details->last_name,
           ];
       }else{
            
            return [
                'status' => 1,
                'address' =>  'Address not found',
                'name' => 'Defaulted to: '.$user_details->first_name.' '.$user_details->last_name,
                'message' =>  isset($response_decode['Detail']['customer']['customerName']) ? $response_decode['Detail']['customer']['customerName']  :  'Defaulted to: '.$user_details->first_name.' '.$user_details->last_name,

           ];
       }

    }


    public function buyElectricity(){
        
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

        $this->electricity_plan_api_id = $plan_details->automation_product_plan_id;

        $metre_number = $this->metre_number;
        $extra_info = urlencode($this->extra_info);
        $electricity_plan_api_id = $this->electricity_plan_api_id;
        $phone_number = $this->phone_number;
        $amount = $this->amount;

        // return [
        //     'extra_info' => $this->extra_info,
        //     'metre_number' => $this->metre_number,
        //     'electricity_plan_api_id' => $electricity_plan_api_id,
        //     'phone_number' => $this->phone_number,
        // ];
       
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://megasubplug.com/API/?action=buy_electricity&metre_number=$metre_number&phone_number=$phone_number&amount=$amount&validation_extra_info=$extra_info&electricity_plan_api_id=$electricity_plan_api_id&duplication_check=1",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('action' => 'buy_electricity','metre_number' => $metre_number,'phone_number' => $phone_number,'amount' => $amount,'validation_extra_info' => $extra_info,'electricity_plan_api_id' => $electricity_plan_api_id,'duplication_check' => '1'),
        CURLOPT_HTTPHEADER => array(
            'Authorization: '.$this->api_key,
            'Password: '.$this->api_password,
            // 'Cookie: PHPSESSID=49f8lqv734slho0cegjgk3rlsk'
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
                'user_message' => isset($response_decode['Detail']['info']['Detail']) ? 'Transaction was successfully processed'  :  'Transaction was successful',
                'admin_message' => $response
            ];
        }

        $error = isset($response_decode['Detail']['error']) ? $response_decode['Detail']['error'] : 'Sorry something went wrong'; 
        return [
            'status' => -1,
            'user_message' => isset($response_decode['Detail']['message']) ? $response_decode['Detail']['message'].'_'.$error :  'Transaction failed_'.$error,
            'admin_message' => $response
        ];

        //REAL
        
    }
}
