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

    public function receive(Request $request)
    {
        $message = $request->input(
            'entry.0.changes.0.value.messages.0'
        );
    
        if (!$message) {
            return response()->json([
                'success' => true
            ]);
        }
    
        $phone = $message['from'] ?? null;
    
        $text = $message['text']['body'] ?? null;
    
        Log::info('Incoming WhatsApp Message', [
            'phone' => $phone,
            'text' => $text,
        ]);
    
        // Later:
        // ProcessWhatsappMessage::dispatch($phone, $text);
    
        return response()->json([
            'success' => true
        ]);
    }
   

    // public function whatsappHook(Request $request){

    // }
   

}
