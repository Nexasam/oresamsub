<?php
namespace App\Http\Controllers\Api\v1\VendorUsersApi;

use App\Http\Controllers\Controller;
use App\Services\Whatsapp\IntentRouter;
use App\Services\Whatsapp\Whatsappsender;
use App\Traits\JsonResponseWrapper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
// use App\Http\Services\Api\v1\VendorUsersApi\Products\ProductsService;
// use App\Services\Api\Automation\MegaSubPlugAutomation\MegaSubCableTV;

class WhatsappWebhookController extends Controller
{
 
    use JsonResponseWrapper;

    public function receive(Request $request, Whatsappsender $sender, IntentRouter $router)
    {
        $message = $request->input('entry.0.changes.0.value.messages.0');

        if (!$message) {
            return response()->json(['success' => true]);
        }

        $phone = $message['from'] ?? null;
        $text = trim($message['text']['body'] ?? '');

        $intentData = $router->resolve($text);

        $response = match ($intentData['intent']) {

            'account' => app(\App\Services\Whatsapp\AccountHandler::class)->handle($phone),

            'services' => app(\App\Services\Whatsapp\ServicesHandler::class)->handle($intentData['raw']),

            'favorites' => app(\App\Services\Whatsapp\FavouritesHandler::class)->handle($phone),

            'offers' => app(\App\Services\Whatsapp\OffersHandler::class)->handle($phone),

            'about' => app(\App\Services\Whatsapp\AboutHandler::class)->handle(),

            default => "🤖 I didn't understand that.\nTry: DATA, ACCOUNT, OFFERS"
        };

        Log::info('WhatsApp Outgoing Response', [
            'phone' => $phone,
            'response' => $response,
            'intent' => $intentData['intent'] ?? null,
        ]);

        $sender->send($phone, $response);

        return response()->json(['success' => true]);
    }

    public function receiveold(
        Request $request,
        Whatsappsender $sender
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
