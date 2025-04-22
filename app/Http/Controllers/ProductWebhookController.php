<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\ProductWebhook;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ProductWebhookController extends Controller
{
    public function product_webhook(Request $request){

        //log for now, to see the flow:
        //for just oresamsub for now
        // header('Content-Type: application/json');
        // $response = file_get_contents('php://input');
        // $requestData = json_decode($request->getContent(), true);
        // logger($response);

        // [
        //     {
        //        "id":"13597637",
        //        "recipient":"Phone No. 07032044022 Plan:2: MTN:SME-1GB-1 Month Validity Price@257.00",
        //        "bal0":"9659.38",
        //        "bal1":"9402.38",
        //        "success":"1",
        //        "type":"Buy_Data",
        //        "amt":"257.00",
        //        "reason":"Successful",
        //        "detail":"Attempt to Buy_Data For Phone No. 07032044022 Plan:2: MTN:SME-1GB-1 Month Validity Price@257.00, <br> Old Balance:9659.38 <br>New Balance: 9402.38 <br>
        //  \twhich was Successful ",
        //        "periodn":"2025-02-06 19:13:55",
        //        "period":"2025-02-06 19:13:55",
        //        "userid":"Oresam",
        //        "realresponse":"Dear Customer, You have successfully shared 1GB Data to 2347032044022. Thankyou Your SME data balance is N\/AGB. Thankyou"
        //     }
        //  ]
    

        //payload response is VERY INCONSISTENT... hold on for now

        // $jsonString = '[{"id":"13597637","recipient":"Phone No. 07032044022 Plan:2: MTN:SME-1GB-1 Month Validity Price@257.00","bal0":"9659.38","bal1":"9402.38","success":"1","type":"Buy_Data","amt":"257.00","reason":"Successful","detail":"Attempt to Buy_Data For Phone No. 07032044022 Plan:2: MTN:SME-1GB-1 Month Validity Price@257.00, <br> Old Balance:9659.38 <br>New Balance: 9402.38 <br>\twhich was Successful ","periodn":"2025-02-06 19:13:55","period":"2025-02-06 19:13:55","userid":"Oresam","realresponse":"Dear Customer, You have successfully shared 1GB Data to 2347032044022. Thankyou Your SME data balance is N\/AGB. Thankyou"}]';
        // // Decode JSON
        // $data = json_decode($jsonString, true);
        // // Check if decoding was successful
        // if (json_last_error() === JSON_ERROR_NONE) {
        // echo "Decoded JSON:\n";
        // echo $data[0]['id']; // Print the decoded array
        // } else {
        // echo "Invalid JSON: " . json_last_error_msg();
        // }


        // if(env('APP_NAME') == 'OresamSub'){
        //             header('Content-Type: application/json');
        //             $response = file_get_contents('php://input');
        //             $response_decode = json_decode($response,true);
        //             logger('testing product webhook start');
        //             logger($response);
            
        //             DB::beginTransaction();
        //             try{
        //                 logger($response_decode['id']);
        //                 // if( ($response_decode['Detail']['success'] == 'true' &&  ($response_decode['Detail']['info']['Balance_before'] > $response_decode['Detail']['info']['Balance_after'] )  ) ){          
        //                 ProductWebhook::create([
        //                     'product_type' => $response_decode['type'] ?? 'nil',
        //                     'status' => $response_decode['status'],
        //                     'response' => $response,
        //                 ]);  

        //                 DB::commit();
        //                 // }else{
        //                 //   logger('This webhook did not update wallet because its likely that the payment has been processed before');
        //                 // }
            
        //             }catch(Exception $ex){
        //                 logger($ex->getMessage().' on line '.$ex->getLine());
        //                 DB::rollBack();
        //             }
                
        //             logger('testing product webhook end');
        // }

       
  }
}
