<?php
namespace App\Http\Controllers\Api\v1\VendorUsersApi;

use App\Http\Controllers\Controller;
use App\Services\Whatsapp\WhatsappSender;
use App\Traits\JsonResponseWrapper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
// use App\Http\Services\Api\v1\VendorUsersApi\Products\ProductsService;
// use App\Services\Api\Automation\MegaSubPlugAutomation\MegaSubCableTV;

class WhatsappWebhookController extends Controller
{
 
    use JsonResponseWrapper;

    public function receive(
        Request $request,
        WhatsappSender $sender
    ) {
    
        $message = $request->input(
            'entry.0.changes.0.value.messages.0'
        );
    
        if (!$message) {
            return response()->json([
                'success' => true
            ]);
        }
    
        $phone = $message['from'] ?? null;
        $text = trim($message['text']['body'] ?? '');
    
        switch (strtoupper($text)) {
    
            case 'PING':
                $response = 'PONG 🚀';
                break;
    
            case 'HELLO':
            case 'HI':
                $response = "Hello 👋\nWelcome to Oresamsub";
                break;
    
            case 'HELP':
                $response = "Available commands:\nPING\nHELP";
                break;
    
            default:
                $response = "You said: {$text}";
                break;
        }
    
        $sender->send($phone, $response);
    
        return response()->json([
            'success' => true
        ]);
    }
   

    // public function whatsappHook(Request $request){

    // }
   

}
