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
//     public function product_webhook($id,Request $request){

//         //log for now, to see the flow:
//         //for just oresamsub for now
//         header('Content-Type: application/json');
//         // $response = file_get_contents('php://input');
//         $requestData = json_decode($request->getContent(), true);
//         logger($request->getContent());
//         exit;

//         // if(env('APP_NAME') == 'OresamSub'){
//         //             header('Content-Type: application/json');
//         //             $response = file_get_contents('php://input');
//         //             $response_decode = json_decode($response,true);
//         //             logger('testing webhook start');
//         //             logger($response);
            
//         //             DB::beginTransaction();
//         //             try{
            
//         //                 // if( ($response_decode['event_data']['Detail']['success'] == 'true' &&  ($response_decode['event_data']['Detail']['info']['Balance_before'] > $response_decode['event_data']['Detail']['info']['Balance_after'] )  ) ){          
//         //                 ProductWebhook::create([
//         //                     'product_type' => $response_decode['event_data']['Detail']['info']['type'],
//         //                     'status' => $response_decode['event_data']['Detail']['success'],
//         //                     'response' => $response,
//         //                 ]);  
//         //                 // }else{
//         //                 //   logger('This webhook did not update wallet because its likely that the payment has been processed before');
//         //                 // }
            
//         //             }catch(Exception $ex){
//         //                 logger($ex->getMessage().' on line '.$ex->getLine());
//         //                 DB::rollBack();
//         //             }
                
//         //             logger('testing webhook end');
//         // }

       
//   }
}
