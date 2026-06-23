<?php
namespace App\Http\Controllers\Api\v1\VendorUsersApi;

use Illuminate\Http\Request;
use App\Traits\JsonResponseWrapper;
use App\Http\Controllers\Controller;
// use App\Http\Services\Api\v1\VendorUsersApi\Products\ProductsService;
// use App\Services\Api\Automation\MegaSubPlugAutomation\MegaSubCableTV;

class WhatsappWebhookController extends Controller
{
 
    use JsonResponseWrapper;

    public function receive(Request $request){
        \Log::info('WHATSAPP WEBHOOK', [
            'payload' => $request->all()
        ]);
    
        return response()->json([
            'success' => true
        ]);
    }
   

    // public function whatsappHook(Request $request){

    // }
   

}
