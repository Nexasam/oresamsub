<?php
namespace App\Http\Controllers\Api\v1\VendorUsersApi;

use App\Http\Controllers\Controller;
use App\Traits\JsonResponseWrapper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
// use App\Http\Services\Api\v1\VendorUsersApi\Products\ProductsService;
// use App\Services\Api\Automation\MegaSubPlugAutomation\MegaSubCableTV;

class WhatsappWebhookController extends Controller
{
 
    use JsonResponseWrapper;

    public function receive(Request $request){
        Log::info(
            'WHATSAPP WEBHOOK',
            json_decode(
                json_encode($request->all()),
                true
            )
        );
    
        return response()->json([
            'success' => true
        ]);
    }
   

    // public function whatsappHook(Request $request){

    // }
   

}
