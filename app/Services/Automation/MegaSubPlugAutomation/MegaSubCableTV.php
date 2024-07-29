<?php

namespace App\Services\Automation\MegaSubPlugAutomation;

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

    public function __construct($smart_card_number,$plan_id,$amount,$validation_customer_name,$no_of_slots,$product_plan_category_name){
        $this->smart_card_number = $smart_card_number;
        $this->validation_customer_name = $validation_customer_name;
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
    public function buyCable(){
        
        $plan_details = ProductPlan::with('product_plan_category')->where('id',$this->plan_id)->first();
      
        if(! $plan_details){
            return [
                'status' => -1,
                'user_message' => 'An error occurred while processing this transaction. Please try again or reach out to support',
                'admin_message' => 'Wrong plan Id',
            ];
        }

        $this->cable_plan_api_id = $plan_details->automation_product_plan_id;

        // Http::withUrlParameters([
        //     'endpoint' => 'https://megasubplug.com/API/?',
        //     'action' => 'buy_cable',
        //     'smart_card_number' => '02724993783',
        //     'validation_customer_name' => 'Awosope Sunday',
        //     'cable_plan_api_id' => '69',
        //     'duplication_check' => '1',
        // ])->post();

        // $response = Http::withUrlParameters([
        //         'endpoint' => 'https://megasubplug.com/API/',
        //         'action' => 'buy_cable',
        //         'smart_card_number' => '02724993783',
        //         'validation_customer_name' => 'Awosope Sunday',
        //         'cable_plan_api_id' => '69',
        //         'duplication_check' => '1',
        // ])
        // ->withHeaders([
        //     'Password' => 'inchristalone@NEW2024',
        //     'Authorization' => '102325246266435f47e344b'
        // ])->post('https://megasubplug.com/API/', [
        //     'action' => 'buy_cable',
        //     'smart_card_number' => '02724993783',
        //     'validation_customer_name' => 'Awosope Sunday',
        //     'cable_plan_api_id' => '69',
        //     'duplication_check' => '1',
        // ]);

        // dd($response);


        //REAL
        $arrrr = array(
            "Authorization" => "102325246266435f47e344b",
            "Password" => "inchristalone@NEW2024",
            "Cookie" => "PHPSESSID=h2vh7clslap9nukf5kt5qagh0d",
        );
        $encoded = json_encode($arrrr);
            
        // $curl = curl_init();
        $url = 'https://megasubplug.com/API/?action=buy_cable&smart_card_number='.$this->smart_card_number.'&validation_customer_name='.$this->validation_customer_name.'&cable_plan_api_id=69&duplication_check='.$this->duplication_check.'';

        // curl_setopt_array($curl, array(
        // CURLOPT_URL => $url,
        // CURLOPT_RETURNTRANSFER => true,
        // CURLOPT_ENCODING => '',
        // CURLOPT_MAXREDIRS => 10,
        // CURLOPT_TIMEOUT => 0,
        // CURLOPT_FOLLOWLOCATION => true,
        // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        // CURLOPT_CUSTOMREQUEST => 'POST',
        // CURLOPT_POSTFIELDS => array('action' => 'buy_cable','smart_card_number' => '02724993783','validation_customer_name' => 'Awosope Sunday','cable_plan_api_id' => '69','duplication_check' => '1'),
        // // CURLOPT_POSTFIELDS => array('action' => 'buy_cable','smart_card_number' => $this->smart_card_number,'validation_customer_name' => $this->validation_customer_name,'cable_plan_api_id' => '69','duplication_check' => '1'),
        // // CURLOPT_HTTPHEADER => $arrrr,
        // CURLOPT_HTTPHEADER => array(
        //     'Authorization: 102325246266435f47e344b',
        //     'Password: inchristalone@NEW2024',
        // ),
        // ));

        $curl = curl_init();
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, array('action' => 'buy_cable','smart_card_number' => '02724993783','validation_customer_name' => 'Awosope Sunday','cable_plan_api_id' => '69','duplication_check' => '1'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = [
        "Authorization: 102325246266435f47e344b",
        "Password: inchristalone@NEW2024",
        ];

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);
    
        // var_dump($resp);
        

        $response = curl_exec($curl);
        logger('start');
        logger($response);
        logger('end');
        curl_close($curl);
        return [
            'status' => -1,
            'message' => $response,
            'user_message' => $response,
            'admin_message' => $response,
        ];

        // curl_close($curl);
        // // echo $response;
    
        // $response_decode = json_decode($response,true);
        

        //DEBUGGING logger($response);
        // $arr = [
        //     'smart' => $this->smart_card_number,
        //     'validation_customer_name' => $this->validation_customer_name,
        //     'cable_plan_api_id' => $this->cable_plan_api_id,
        //     'duplication_check' => $this->duplication_check,
        // ];
        // return [
        //     'status' => -1,
        //     'user_message' => json_encode($arr),
        //     'admin_message' => json_encode($arr),
        // ];  

        // if(isset($response_decode['Status']) && $response_decode['Status'] == 'Success' && isset($response_decode['Detail']['info']['Success']) 
        //  &&  $response_decode['Detail']['info']['Success'] == '1'  ){
        //     //successful transaction
        //     return [
        //         'status' => 1,
        //         'user_message' => isset($response_decode['Detail']['info']['Detail']) ? $response_decode['Detail']['info']['Detail']  :  'Transaction was successful',
        //         'admin_message' => isset($response_decode['Detail']['info']['Detail']) ? $response_decode['Detail']['info']['Detail']  :  'Transaction was successful',
        //     ];
        // }

        // $error = isset($response_decode['Detail']['error']) ? $response_decode['Detail']['error'] : ''; 
        // return [
        //     'status' => -1,
        //     'user_message' => isset($response_decode['Detail']['message']) ? $response_decode['Detail']['message'].'_'.$error :  'Transaction failed_'.$error,
        //     'admin_message' => isset($response_decode['Detail']['message']) ? $response_decode['Detail']['message'].'_'.$error  :  'Transaction failed',
        // ];

        //REAL
        
    }
}
