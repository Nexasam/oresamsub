<?php
namespace App\Http\Controllers\Api\v1\VendorUsersApi;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WhatsappConfig;
use App\Services\Whatsapp\IntentRouter;
use App\Services\Whatsapp\WhatsappConversationService;
use App\Services\Whatsapp\WhatsappIntentParser;
use App\Services\Whatsapp\WhatsappIntentResolver;
use App\Services\Whatsapp\Whatsappsender;
use App\Services\Whatsapp\WhatsappUserResolver;
use App\Traits\JsonResponseWrapper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
    
        return match ($buttonId) {

            'data_confirm_purchase' => 'yes',
            'data_cancel_purchase' => 'no',
        
            'favorite_use_same_number'
                => 'favorite_use_same_number',
        
            'favorite_change_number'
                => 'favorite_change_number',

            // Account buttons
            'account_refresh_balance' => 'account_refresh_balance',
            'account_data_airtime_help'        => 'account_buy_data_airtime',
           
            'save_contact_yes' => 'save_contact_yes',
            'save_contact_no'  => 'save_contact_no',
        
            'start_over'
                => 'start',
        
            default => null,
        };
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
                '/^(start|hi|clear|hello|hey|yo|sup|whats?up|howdy|how far|how you dey|how u dey|how body|wetin dey|bawo ni|sanu|pele|good morning|good afternoon|good evening)$/i',
                trim($text)
            )) {
        
            cache()->forget("wa_session:$phone");

            $user = app(WhatsappUserResolver::class)
            ->resolve($phone); 

            $firstName = $user?->first_name ?? $user?->username;
        
            $welcome = $firstName
            ? "👋 Hi {$firstName}, welcome to OresamSub!\n\n"
            : "👋 Welcome to OresamSub!\n\n";
            
            app(Whatsappsender::class)->send(
                $phone,
                $welcome
                . "I'm your personal VTU assistant ⚡\n\n"
            
                . "Here's what I can help you with:\n\n"
            
                . "📶 Buy Data Bundles\n"
                . "📞 Buy Airtime\n"
                . "📋 Repeat Recent Purchases\n"
                . "⭐ Buy for Saved Contacts\n"
                . "💰 Check Wallet Balance\n"
                . "🆘 Contact Support\n\n"
            
                . "You can type commands in ANY format (not case-sensitive).\n\n"
            
                . "📶 DATA EXAMPLES\n"
                . "• MTN 1GB Weekly\n"
                . "• Glo 2GB 3 Days\n"
                . "• Airtel 5GB Monthly\n"
                . "• 1GB MTN Weekly\n"
                . "• MTN 1GB Weekly 09034556677\n\n"
              
            
                . "📞 AIRTIME EXAMPLES\n"
                . "• Airtime 1000 MTN\n"
                . "• MTN Airtime 500\n"
                . "• Airtel Airtime 2000\n"
                . "• MTN Airtime 300 09011223344\n\n"
            
                . "⭐ RECENT DATA TXNS\n"
                . "• Recent\n"
                . "• Buy Again\n"
                . "• Favourites\n"
                . "• fav\n"
                . "• Popular\n\n"
            
                . "💰 ACCOUNT\n"
                . "• Balance\n"
                . "• Wallet\n"
                . "• Account\n"
                . "• fund\n\n"
            
                . "🆘 HELP\n"
                . "• Support\n"
                . "• Help\n\n"
            
                // . "You can also use saved contact names:\n"
                // . "• MTN 1GB Weekly to Mom\n"
                // . "• Airtime 500 to John\n\n"
            
                . "What would you like to do today? 😊"
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

            if (!$user) {

                Cache::put(
                    "wa_session:{$phone}",
                    [
                        'status' => 'link_whatsapp',
                        'phone'  => $phone,
                    ],
                    now()->addMinutes(15)
                );
            
                app(Whatsappsender::class)->send(
                    $phone,
                    "⚠️ Sorry, we could not find an account associated with this WhatsApp number.\n\n"
                    . "If you already have an OresamSub account, please reply with your email address and we will send an OTP to verify your account and link this WhatsApp number to it."
                );
            
                return response()->json(['ok' => true]);
            }



            if ($session) {

                switch ($session['status']) {

                    case 'link_whatsapp':

                        $email = trim($text);

                        $user = User::where('email', $email)->first();

                        if (!$user) {

                            app(Whatsappsender::class)->send(
                                $phone,
                                "❌ No account was found with that email address. Please try again."
                            );

                            return response()->json(['ok' => true]);
                        }

                        $otp = rand(100000, 999999);

                        Cache::put(
                            "wa_link_otp:{$phone}",
                            [
                                'user_id' => $user->id,
                                'otp'     => $otp,
                            ],
                            now()->addMinutes(10)
                        );

                        Cache::put(
                            "wa_session:{$phone}",
                            [
                                'status' => 'verify_link_otp',
                                'user_id' => $user->id,
                            ],
                            now()->addMinutes(15)
                        );

                        // Send OTP via email here

                        app(Whatsappsender::class)->send(
                            $phone,
                            "📧 An OTP has been sent to {$email}.\n\nPlease reply with the OTP to continue."
                        );

                        return response()->json(['ok' => true]);

                    case 'verify_link_otp':

                        $otpData = Cache::get("wa_link_otp:{$phone}");

                        if (!$otpData || $otpData['otp'] != trim($text)) {

                            app(Whatsappsender::class)->send(
                                $phone,
                                "❌ Invalid OTP. Please try again."
                            );

                            return response()->json(['ok' => true]);
                        }

                        User::where('id', $otpData['user_id'])
                            ->update([
                                'whatsapp_number' => $phone,
                            ]);

                        Cache::forget("wa_session:{$phone}");
                        Cache::forget("wa_link_otp:{$phone}");

                        app(Whatsappsender::class)->send(
                            $phone,
                            "✅ Success! Your WhatsApp number has been linked to your OresamSub account. Type: START to begin using the service."
                        );

                        return response()->json(['ok' => true]);
                }
            }



        }


       


        if ($text === 'account_refresh_balance') {

            $result = app(WhatsappIntentResolver::class)
                ->resolveAccount($user, $phone);
        
            app(Whatsappsender::class)->sendAccountButtons(
                $phone,
                $result['message']
            );
        
            return response()->json(['ok' => true]);
        }
        
        if ($text === 'account_buy_data_airtime') {

            app(Whatsappsender::class)->send(
                $phone,
                "📶 *To Buy Data*\n\n"
        
                . "Examples:\n"
                . "• MTN 1GB Weekly\n"
                . "• Airtel 2GB Monthly\n"
                . "• Glo 500MB\n"
                . "• MTN 1GB Weekly 09034556677\n"
        
                . "📞 *To Buy Airtime*\n\n"
        
                . "Examples:\n"
                . "• MTN Airtime 500\n"
                . "• Airtime 1000 MTN\n"
                . "• MTN Airtime 300 09011223344\n"
        
                . "💡 Messages are not case-sensitive."
            );
        
            return response()->json(['ok' => true]);
        }




        /*
        |--------------------------------------------------------------------------
        | Global commands (work even during active sessions)
        |--------------------------------------------------------------------------
        */
        if (in_array($text, [
            'account',
            'balance',
            'wallet',
            'fund'
        ])) {

            $result = app(WhatsappIntentResolver::class)
                ->resolveAccount($user, $phone);

            app(Whatsappsender::class)->sendAccountButtons(
                $phone,
                $result['message']
            );

            return response()->json([
                'ok' => true
            ]);
        }

        if (in_array($text, [
            'help',
            'support',
            'contact',
            'agent',
            'customer care'
        ])) {

            $result = app(WhatsappIntentResolver::class)
                ->resolveHelp();

            app(Whatsappsender::class)->send(
                $phone,
                $result['message']
            );

            return response()->json([
                'ok' => true
            ]);
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

                'airtime_network_required'
                    => $conversation->handleAirtimeNetworkSelection(
                        $text,
                        $session
                    ),

                'airtime_amount_required'
                    => $conversation->handleAirtimeAmountInput(
                        $text,
                        $session
                    ),

                'airtime_phone_required'
                    => $conversation->handleAirtimePhoneInput(
                        $text,
                        $session
                    ),

                'airtime_awaiting_confirmation'
                    => $conversation->handleAirtimeConfirmation(
                        $text,
                        $user,
                        $session
                    ),

                'favorite_phone_required'
                => $conversation->handleFavoritePhoneInput(
                    $text,
                    $session,
                    $phone
                ),
                
                'contact_save_prompt'
                => $conversation->handleSaveContactPrompt(
                    $text,
                    $session
                ),

                'contact_name_required'
                => $conversation->handleSaveContactName(
                    $text,
                    $session,
                    $user
                ),

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


        /////cccount view
        if ($result['status'] === 'account_view') {

            app(Whatsappsender::class)->sendAccountButtons(
                $phone,
                $result['message']
            );
        
            return response()->json([
                'ok' => true
            ]);
        }

        /*
        Send reply
        */
        if (
            in_array(
                $result['status'] ?? '',
                [
                    'data_awaiting_confirmation',
                    'airtime_awaiting_confirmation',
                ]
            )
        ) {

            app(Whatsappsender::class)
                ->sendConfirmationButtons(
                    $phone,
                    $result['message']
                );

        } else {

            app(Whatsappsender::class)
                ->send(
                    $phone,
                    $result['message']
                );
        }

        return response()->json([
            'ok' => true
        ]);
    }

 

  
   

   

}
