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

    private function extractPhone(array $payload): ?string
    {
        return
            data_get(
                $payload,
                'entry.0.changes.0.value.messages.0.from'
            )
            ?? data_get(
                $payload,
                'entry.0.changes.0.value.statuses.0.recipient_id'
            );
    }
    
    private function whatsappStatus(array $payload): ?string
    {
        return data_get(
            $payload,
            'entry.0.changes.0.value.statuses.0.status'
        );
    }
    
    private function extractText2(array $payload): ?string
    {
        /*
        Normal text message
        */
        $text = data_get(
            $payload,
            'entry.0.changes.0.value.messages.0.text.body'
        );
    
        if (!empty($text)) {
            return strtolower(trim($text));
        }
    
        /*
        Interactive button reply
        */
        $buttonId = data_get(
            $payload,
            'entry.0.changes.0.value.messages.0.interactive.button_reply.id'
        );
    
        if ($buttonId === 'data_confirm_purchase') {
            return 'yes';
        }
    
        if ($buttonId === 'data_cancel_purchase') {
            return 'no';
        }
    
        return null;
    }

    private function extractText(array $payload): ?string
    {
        /*
        Normal text message
        */
        $text = data_get(
            $payload,
            'entry.0.changes.0.value.messages.0.text.body'
        );
    
        if (!empty($text)) {
            return strtolower(trim($text));
        }
    
        /*
        Shared contact
        */
        $contactPhone = data_get(
            $payload,
            'entry.0.changes.0.value.messages.0.contacts.0.phones.0.phone'
        );
    
        if (!empty($contactPhone)) {
            return preg_replace('/\D/', '', $contactPhone);
        }
    
        /*
        Interactive buttons
        */
        $buttonId = data_get(
            $payload,
            'entry.0.changes.0.value.messages.0.interactive.button_reply.id'
        );
    
        return [
            'data_confirm_purchase' => 'yes',
            'data_cancel_purchase'  => 'no',
            'retry_purchase'        => 'yes',
            'start_over'            => 'start',
        ][$buttonId] ?? null;
    }

    private function extractPhoneFromContact(
        array $payload
    ): ?string
    {
        return data_get(
            $payload,
            'entry.0.changes.0.value.messages.0.contacts.0.phones.0.phone'
        );
    }

    public function receive(Request $request)
    {

        $payload = $request->all();

        $phone = $this->extractPhone($payload);
        $text  = $this->extractText($payload);
    
        logger(json_encode([
            'phone' => $phone,
            'text' => $text,
            'status' => $this->whatsappStatus($payload),
        ]));
    
        /*
        Ignore delivery/read/sent webhooks
        */
        if (empty($text) || empty($phone)) {
    
            return response()->json([
                'ok' => true
            ]);
        }

  


        $greetings = [
            'start',
        
            // Standard
            'hi',
            'hello',
            'hey',
            'yo',
            'howdy',
        
            // Nigerian
            'sup',
            'whatsup',
            "what's up",
            'how far',
            'how you dey',
            'how u dey',
            'how body',
            'wetin dey',
        
            // Yoruba
            'bawo ni',
            'eku',
            'eku ojo',
            'eku aro',
            'sanu',
            'pele',
        
            // Time-based
            'good morning',
            'good afternoon',
            'good evening',
        ];
        
            if (preg_match(
                '/^(start|hi|hello|hey|yo|sup|whats?up|howdy|how far|how you dey|how u dey|how body|wetin dey|bawo ni|sanu|pele|good morning|good afternoon|good evening)$/i',
                trim($text)
            )) {
        
            cache()->forget("wa_session:$phone");
        
            app(Whatsappsender::class)->send(
                $phone,
                "👋 Welcome to OresamSub\n\n"
                . "I'm your personal vtu assistant.\n\n"
                . "📶 Buy Data\n"
                . "📞 Buy Airtime\n"
                . "📋 Repeat Recent Purchases\n"
                . "💰 Check Balance\n"
                . "🆘 Get Support\n\n"
                . "Try:\n"
                . "• mtn 1gb weekly\n"
                . "• glo 2 gb 3 days\n"
                . "• airtime 1000 MTN\n"
                . "• recent\n"
                . "• buy again\n"
                . "• fav\n"
                . "• balance\n\n"
                . "What would you like to do today?"
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
        if(! empty($phone) ){
            $user = app(WhatsappUserResolver::class)
            ->resolve($phone);    
        }


        $conversation = app(
            WhatsappConversationService::class
        );

        if ($session) {

            // logger('this raaan1 in session.'.json_encode($session));
            // $user = app(WhatsappUserResolver::class)
            // ->resolve($session['whatsapp_phone']);

            // logger('Lets see session content: '.json_encode($session));
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

                'favorites_selection'
                => $conversation->handleFavoriteSelection(
                    $text,
                    $session,
                    $phone
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
            // logger('omo..na this one oh');
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
