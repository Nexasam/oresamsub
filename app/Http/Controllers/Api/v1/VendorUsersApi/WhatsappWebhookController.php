<?php
namespace App\Http\Controllers\Api\v1\VendorUsersApi;

use App\Http\Controllers\Controller;
use App\Models\WhatsappConfig;
use App\Services\Whatsapp\IntentRouter;
use App\Services\Whatsapp\WhatsappConversationService;
use App\Services\Whatsapp\WhatsappIntentParser;
use App\Services\Whatsapp\WhatsappIntentResolver;
use App\Services\Whatsapp\Whatsappsender;
use App\Services\Whatsapp\WhatsappUserResolver;
use App\Traits\JsonResponseWrapper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
// use App\Http\Services\Api\v1\VendorUsersApi\Products\ProductsService;
// use App\Services\Api\Automation\MegaSubPlugAutomation\MegaSubCableTV;

class WhatsappWebhookController extends Controller
{
 
    use JsonResponseWrapper;

    public function updateConfig($phone_number_id,$token){
           WhatsappConfig::updateOrCreate([
             'phone_number_id' => $phone_number_id,
           ],[
            'token' => $token,

           ]);
    }

    public function receive(Request $request)
    {
        $phone = $request->input(
            'entry.0.changes.0.value.messages.0.from'
        );

        $text = $request->input(
            'entry.0.changes.0.value.messages.0.text.body'
        );

        $text = strtolower(trim($text));

        
        // if (!$phone || !$text) {
        
        //     logger('Ignoring non-message webhook', $request->all());
        
        //     // return response()->json(['ok' => true]);
        // }

        logger('phone and text: '.$phone.'---'.$text);

        /*
        Reset conversation
        */
        if ($text === 'start') {

               

            cache()->forget("wa_session:$phone");

            app(Whatsappsender::class)->send(
                $phone,
                "Welcome to OresamSub 👋\n\nWhat would you like to buy today?"
            );

            return response()->json(['ok' => true]);
        }

       

     

        /*
        Existing conversation?
        */
        $session = cache()->get("wa_session:$phone");

         /*
        Load whatsapp user
        */

        $user = app(WhatsappUserResolver::class)
        ->resolve($phone);
        logger('userrr: '.$user);


        $conversation = app(
            WhatsappConversationService::class
        );

        if ($session) {

            logger('Lets see session content: '.$session);
            return match ($session['status']) {

                'data_network_required'
                    => $conversation->handleDataNetworkSelection(
                        $text,
                        $session
                    ),

                'data_size_required'
                    => $conversation->handleDataSizeSelection(
                        $text,
                        $session
                    ),

                'data_phone_required'
                    => $conversation->handleDataPhoneInput(
                        $text,
                        $session
                    ),

                'data_multiple_options'
                    => $conversation->handleDataPlanSelection(
                        $text,
                        $session,
                        $phone
                    ),

                'data_awaiting_confirmation'
                    => $conversation->handleConfirmation(
                        $text,
                        $user,
                        $session
                    ),

                'unlinked_user'
                    => $conversation->handleUnlinkedUser(
                        $text,
                        $session
                    ),

                default => response()->json([
                    'ok' => true
                ]),
            };
        }

        /*
        Fresh request
        */
        $intent = app(WhatsappIntentParser::class)
            ->parse($text);

        $result = app(WhatsappIntentResolver::class)
            ->resolve(
                $intent,
                $user,
                $phone
            );

        /*
        Save conversation state
        */
        cache()->put(
            "wa_session:$phone",
            array_merge(
                $result,
                [
                    'whatsapp_phone' => $phone,
                    'intent' => $intent,
                ]
            ),
            now()->addMinutes(10)
        );

        /*
        Send reply
        */
        app(Whatsappsender::class)->send(
            $phone,
            $result['message']
        );

        return response()->json([
            'ok' => true
        ]);
    }

    public function receiveold(Request $request, Whatsappsender $sender, IntentRouter $router)
    {
        $message = $request->input('entry.0.changes.0.value.messages.0');

        if (!$message) {
            logger('omo..na this one oh');
            return response()->json(['success' => true]);
        }


        logger('omo2');

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

    public function receivenewold(Request $request)
    {
        $phone = $request->input('entry.0.changes.0.value.messages.0.from');
        $text  = $request->input('entry.0.changes.0.value.messages.0.text.body');
    
        $text = strtolower(trim($text)); #customer text

        $text = strtolower(trim($text));

        if ($text === 'start') {

            cache()->forget("wa_session:$phone");

            app(Whatsappsender::class)->send(
                $phone,
                "Welcome to OresamSub 👋\n\nWhat would you like to buy today?"
            );

            return response()->json(['ok' => true]);
        }
    
        // STEP 1: Load user
        $user = app(WhatsappUserResolver::class)->resolve($phone);
    
        // STEP 2: Check if user is in a pending transaction state
        $session = cache()->get("wa_session:$phone");
    
     

        $conversation = (new WhatsappConversationService());

        if ($session) {

            return match ($session['status']) {
        
                'data_network_required'
                    => $conversation->handleDataNetworkSelection($text, $session),
        
                'data_size_required'
                    => $conversation->handleDataSizeSelection($text, $session),
        
                'data_phone_required'
                    => $conversation->handleDataPhoneInput($text, $session),
        
                'data_multiple_options'
                    => $conversation->handleDataPlanSelection($text, $session),
        
                'data_awaiting_confirmation'
                    => $conversation->handleConfirmation($text, $user, $session),
        
                'unlinked_user'
                    => $conversation->handleUnlinkedUser($text, $session),
        
                default => null,
            };
        }
    
        // STEP 3: Normal flow → intent parsing
        $intent = app(WhatsappIntentParser::class)->parse($text);
    
        // , $user, $phone
        $result =app(WhatsappIntentResolver::class)->resolve($intent,$user,$phone);
    
        // STEP 4: store session (important)
        cache()->put("wa_session:$phone", $result, now()->addMinutes(10));
    
        // STEP 5: send response
        app(Whatsappsender::class)->send($phone, $result['message']);
    
        return response()->json(['ok' => true]);
    }
   

   

}
