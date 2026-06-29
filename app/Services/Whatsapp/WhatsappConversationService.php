<?php
namespace App\Services\Whatsapp;

use App\Http\Services\DataPlansService;
use App\Models\ProductPlan;
use App\Models\UserContact;
use App\Services\Whatsapp\WhatsappIntentResolver;
use App\Services\Whatsapp\WhatsappUserResolver;
use Illuminate\Http\Request;

class WhatsappConversationService{

        protected function updateSessionAndResolve(
            array $session,
            array $intent,
            string $phone
        ) {
            $user = app(WhatsappUserResolver::class)
                ->resolve($phone);
    
            $result = app(WhatsappIntentResolver::class)
                ->resolveData($intent, $user, $phone);
    
            cache()->put(
                "wa_session:$phone",
                array_merge($result, [
                    'intent' => $intent
                ]),
                now()->addMinutes(10)
            );
    
            app(Whatsappsender::class)->send(
                $phone,
                $result['message']
            );
    
            return response()->json(['ok' => true]);
        }
    
        public function handleDataNetworkSelection(
            string $text,
            array $session
        ) {
            $intent = $session['intent'];
    
            $intent['network'] = strtolower(trim($text));
    
            return $this->updateSessionAndResolve(
                $session,
                $intent,
                $session['whatsapp_phone']
            );
        }
    
        public function handleDataSizeSelection(
            string $text,
            array $session
        ) {
            $intent = $session['intent'];
    
            $intent['data_size_in_mb'] =
                app(WhatsappIntentParser::class)
                    ->extractDataSize($text);
    
            return $this->updateSessionAndResolve(
                $session,
                $intent,
                $session['whatsapp_phone']
            );
        }

        private function normalizeWhatsappNumber(string $phone): string
        {
            $phone = preg_replace('/\D/', '', $phone);

            if (str_starts_with($phone, '0')) {
                $phone = '234' . substr($phone, 1);
            }

            return $phone;
        }
    
        public function handleDataPhoneInputold(
            string $text,
            array $session
        ) {
            $intent = $session['intent'];

            $textt = $this->normalizeWhatsappNumber($text);
    
            $intent['phone'] = trim($textt);

            // logger('phone input: '.$textt);
    
            return $this->updateSessionAndResolve(
                $session,
                $intent,
                $session['whatsapp_phone']
            );
        }

        protected function resolveSelectedPhone(
            string $text,
            array $session
        ): ?string
        {
            $option = (int) trim($text);
        
            /*
            Saved contact selected
            */
            if (isset($session['contacts'][$option])) {
                return $session['contacts'][$option]['phone_number'];
            }
        
            /*
            User typed a phone directly
            */
            $phone = preg_replace('/\D/', '', $text);
        
            if (strlen($phone) >= 10) {
                return $this->normalizeWhatsappNumber($phone);
            }
        
            return null;
        }

        public function handleFavoritePhoneInput(
            string $text,
            array $session,
            string $phone
        )
        {
            $recipientPhone = $this->resolveSelectedPhone(
                $text,
                $session
            );
        
            $plan = ProductPlan::with([
                'product_plan_category.product',
                'product_plan_category.network'
            ])->find($session['product_plan_id']);
        
            $user = app(WhatsappUserResolver::class)
                ->resolve($phone);
        
            $price = app(WhatsappIntentResolver::class)
            ->getCustomerPlanPrice(
                $plan,
                $user
            );
        
            $result = [
                'status' => 'data_awaiting_confirmation',
                'product_plan_id' => $plan->id,
                'network_id' => $plan->product_plan_category->network->id,
                'phone' => $recipientPhone,
                'price' => $price,
                'whatsapp_phone' => $session['whatsapp_phone'],
            ];
        
            cache()->put(
                "wa_session:{$session['whatsapp_phone']}",
                $result,
                now()->addMinutes(10)
            );
        
            app(Whatsappsender::class)->sendConfirmationButtons(
                $session['whatsapp_phone'],
                "🛒 Almost done!\n\n"
                . "📦 Plan: {$plan->product_plan_name}\n"
                . "📱 Number: {$recipientPhone}\n"
                . "💰 Amount: ₦" . number_format($price)
                . "\n\nPlease confirm to continue."
            );
        
            return response()->json(['ok' => true]);
        }

        public function handleDataPhoneInputnewestold(
            string $text,
            array $session
        )
        {
            $intent = $session['intent'];
        
            /*
            Selected saved contact
            */
            $option = (int) trim($text);
        
            if (
                isset($session['options']) &&
                isset($session['options'][$option])
            ) {
        
                $intent['phone'] =
                    $session['options'][$option];
        
                return $this->updateSessionAndResolve(
                    $session,
                    $intent,
                    $session['whatsapp_phone']
                );
            }
        
            /*
            Typed/shared number
            */
            $intent['phone'] = $this->normalizeWhatsappNumber($text);
        
            return $this->updateSessionAndResolve(
                $session,
                $intent,
                $session['whatsapp_phone']
            );
        }

        public function handleDataPhoneInput(
            string $text,
            array $session
            )
            {
            $intent = $session['intent'] ?? null;
            
        
            /*
            Saved contact selected
            */
            $option = (int) trim($text);
            
            if (
                isset($session['options']) &&
                isset($session['options'][$option])
            ) {
                $intent['phone']
                    = $session['options'][$option];
            } else {
            
                /*
                Typed/shared number
                */
                $intent['phone']
                    = $this->normalizeWhatsappNumber($text);
            }
            
            /*
            Must already have a selected plan
            */
            if (empty($intent['selected_plan_id'])) {
            
                app(Whatsappsender::class)->send(
                    $session['whatsapp_phone'],
                    "❌ Your session has expired. Please start again.\n\nExample:\nMTN 1GB Weekly"
                );
            
                cache()->forget(
                    "wa_session:" . $session['whatsapp_phone']
                );
            
                return response()->json([
                    'ok' => true
                ]);
            }
            
            $planId = $intent['selected_plan_id'];
            
            /*
            Continue directly to confirmation
            */
            return $this->showDataConfirmation(
                $planId,
                $intent['phone'],
                $session['whatsapp_phone'],
                $intent
            );
            
            
            }


            public function showDataConfirmation(
                 $planId,
                string $phoneNumber,
                string $whatsappPhone,
                array $intent
                )
                {
                $plan = ProductPlan::with([
                'product_plan_category.product',
                'product_plan_category.network'
                ])->find($planId);
                
                
                if (!$plan) {
                
                    app(Whatsappsender::class)->send(
                        $whatsappPhone,
                        "❌ Unable to find the selected plan. Please try again."
                    );
                
                    return response()->json([
                        'ok' => true
                    ]);
                }
                
                $user = app(WhatsappUserResolver::class)
                    ->resolve($whatsappPhone);
                
                $dat = [
                    'product_id'
                        => $plan->product_plan_category->product->id,
                
                    'network_id'
                        => $plan->product_plan_category->network->id,
                
                    'user'
                        => $user,
                
                    'plan_details'
                        => $plan,
                ];
                
                $price =
                    app(\App\Http\Services\DataPlansService::class)
                        ->get_customer_price_per_plan($dat)['message'];
                
                $result = [
                    'status' => 'data_awaiting_confirmation',
                    'product_plan_id' => $planId,
                    'network_id'
                        => $plan->product_plan_category->network->id,
                    'phone' => $phoneNumber,
                    'price' => $price,
                    'intent' => $intent,
                    'whatsapp_phone' => $whatsappPhone,
                    'message' =>
                        "🛒 Almost done!\n\n"
                        . "📦 Plan: {$plan->product_plan_name}\n"
                        . "📱 Number: {$phoneNumber}\n"
                        . "💰 Amount: ₦" . number_format($price) . "\n\n"
                        . "Please review the details above.\n\n"
                        . "✅ Reply YES to complete this purchase\n"
                        . "❌ Reply NO to cancel."
                ];
                
                cache()->put(
                    "wa_session:$whatsappPhone",
                    $result,
                    now()->addMinutes(10)
                );
                
                app(Whatsappsender::class)->sendConfirmationButtons(
                    $whatsappPhone,
                    $result['message']
                );
                
                return response()->json([
                    'ok' => true
                ]);
                
                
                }
                
            

    

        private function sendPhoneRequestWithContacts(
            $user,
            string $whatsappPhone,
            array $session
        )
        {
            $contacts = UserContact::query()
                ->where('user_id', $user->id)
                ->latest('last_used_at')
                ->take(5)
                ->get();

            $cachePayload = [
                ...$session,
                'status' => 'data_phone_required',
                'whatsapp_phone' => $whatsappPhone,
            ];

            /*
            Saved contacts available
            */
            if ($contacts->isNotEmpty()) {

                $message =
                    "📱 Who should receive this data?\n\n"
                    . "Saved Contacts:\n\n";

                $options = [];

                foreach ($contacts as $index => $contact) {

                    $number = $index + 1;

                    $message .=
                        "{$number}. {$contact->name} - {$contact->phone_number}\n";

                    $options[$number] = $contact->phone_number;
                }

                $message .=
                    "\nYou can:\n"
                    . "• Reply with a contact number above\n"
                    . "• Type a phone number\n"
                    . "• Share a WhatsApp contact";

                $cachePayload['options'] = $options;

                cache()->put(
                    "wa_session:$whatsappPhone",
                    $cachePayload,
                    now()->addMinutes(10)
                );

                app(Whatsappsender::class)->send(
                    $whatsappPhone,
                    $message
                );

                return;
            }

            /*
            No saved contacts
            */
            cache()->put(
                "wa_session:$whatsappPhone",
                $cachePayload,
                now()->addMinutes(10)
            );

            app(Whatsappsender::class)->send(
                $whatsappPhone,
                "📱 Who should receive this data?\n\n"
                . "You can:\n"
                . "• Type the phone number\n"
                . "• Share a WhatsApp contact\n\n"
                . "Example:\n"
                . "08168509044"
            );
        }
    
        public function handleDataPlanSelection(
            string $text,
            array $session,
            $phone
        ) {
            $option = (int) trim($text);
    
            if (!isset($session['options'][$option])) {
    
                app(Whatsappsender::class)->send(
                    $session['whatsapp_phone'],
                    "🤔 I couldn't match that selection.\n\nPlease choose one of the options above by replying with its number."
                );
    
                return response()->json(['ok' => true]);
            }
    
            $planId = $session['options'][$option];
    
            $plan = ProductPlan::with([
                'product_plan_category.product',
                'product_plan_category.network'
            ])->find($planId);
    
            $intent = $session['intent'];
    
            $intent['selected_plan_id'] = $planId;
            // $intent['product_plan_id'] = $planId;

            // logger(json_encode([
            //     'intent' => $intent,
            //     'intent_phone' => $intent['phone'] ?? null,
            // ]));

            // $intent['selected_plan_id'] = $planId;

      

            $user = app(WhatsappUserResolver::class)
                ->resolve($phone);
            // logger(json_encode([
            //     'DATA selection user' => $user,
            // ]));
    
            $dat = [
                'product_id' => $plan->product_plan_category->product->id,
                'network_id' => $plan->product_plan_category->network->id,
                'user' => $user,
                'plan_details' => $plan,
            ];
    
            $price =
                app(\App\Http\Services\DataPlansService::class)
                    ->get_customer_price_per_plan($dat)['message'];


    
            //if rec. phone exists, then normalize it
            if(!empty($intent['phone'])){
                $recphone = $this->normalizeWhatsappNumber($intent['phone']);
            }else{
                $recphone = null;
            }
            $result = [
                'status' => 'data_awaiting_confirmation',
                'product_plan_id' => $planId,
                'network_id' => $plan->product_plan_category->network->id,
                'phone' => $recphone,
                'price' => $price,
                'intent' => $intent,
                'message' =>
                    "🛒 Almost done!\n\n"
                    . "📦 Plan: {$plan->product_plan_name}\n"
                    . "📱 Number: {$recphone}\n"
                    . "💰 Amount: ₦" . number_format($price)
                    . "\n\n"
                    . "Please review the details above.\n\n"
                    . "✅ Reply YES to complete this purchase\n"
                    . "❌ Reply NO to cancel."
            ];
    

          
        //    logger('handle data plan selection: '.json_encode($session));
            $result['whatsapp_phone']
           = $session['whatsapp_phone'];
        
            cache()->put(
                "wa_session:" . $session['whatsapp_phone'],
                $result,
                now()->addMinutes(10)
            );

            //this means we already captured the plan beforer asking for the number.
            // if (empty($intent['phone'])) {

            //     cache()->put(
            //         "wa_session:" . $session['whatsapp_phone'],
            //         [
            //             'status' => 'data_phone_required',
            //             'intent' => $intent,
            //             'whatsapp_phone' => $session['whatsapp_phone'],
            //         ],
            //         now()->addMinutes(10)
            //     );

            //     app(Whatsappsender::class)->send(
            //         $session['whatsapp_phone'],
            //         "📱 Who should receive this data?\n\n"
            //         . "You can:\n"
            //         . "• Type the phone number\n"
            //         . "• Or share a contact from WhatsApp\n\n"
            //         . "Example:\n"
            //         . "08168509044"
            //     );

            //     return response()->json(['ok' => true]);
            // }
            if (empty($intent['phone'])) {

                $user = app(WhatsappUserResolver::class)
                    ->resolve($phone);
            
                $this->sendPhoneRequestWithContacts(
                    $user,
                    $session['whatsapp_phone'],
                    [
                        'intent' => $intent
                    ]
                );
            
                return response()->json(['ok' => true]);
            }

          

    
            // app(Whatsappsender::class)->send(
            //     $session['whatsapp_phone'],
            //     $result['message']
            // );
            app(Whatsappsender::class)->sendConfirmationButtons(
                $session['whatsapp_phone'],
                $result['message']
            );
    
            return response()->json(['ok' => true]);
        }
    
        public function handleUnlinkedUser(
            string $text,
            array $session
        ) {
            if (strtolower(trim($text)) === 'start') {
    
                cache()->forget(
                    "wa_session:" . $session['whatsapp_phone']
                );
    
                app(Whatsappsender::class)->send(
                    $session['whatsapp_phone'],
                    "Okay. Send your request again."
                );
            }
    
            return response()->json(['ok' => true]);
    }
    

    public function handleFavoriteSelection(
        string $text,
        array $session,
        string $phone
    )
    {
        $option = (int) trim($text);
    
        if (!isset($session['options'][$option])) {
    
            app(Whatsappsender::class)->send(
                $session['whatsapp_phone'],
                "Oops 😅\n\nThat option isn't on the list.\n\nPlease choose one of the numbers shown above."
            );
    
            return response()->json(['ok' => true]);
        }
    
        $selection = $session['options'][$option];
        logger('fav fix::::'.json_encode($selection));
    
        $planId = $selection['product_plan_id'];
        $recipientPhone = $selection['phone'] ?? null;
        $intent['selected_plan_id'] = $planId;
    
        $plan = ProductPlan::with([
            'product_plan_category.product',
            'product_plan_category.network'
        ])->find($planId);
    
        if (!$plan) {
    
            app(Whatsappsender::class)->send(
                $session['whatsapp_phone'],
                "Selected plan no longer exists."
            );
    
            return response()->json(['ok' => true]);
        }
    
        $user = app(WhatsappUserResolver::class)
            ->resolve($phone);
    
        $dat = [
            'product_id' => $plan->product_plan_category->product->id,
            'network_id' => $plan->product_plan_category->network->id,
            'user' => $user,
            'plan_details' => $plan,
        ];
    
        $price =
            app(DataPlansService::class)
                ->get_customer_price_per_plan($dat)['message'];
    
        $result = [
            'status' => 'favorite_phone_required',
            'product_plan_id' => $plan->id,
            'network_id' => $plan->product_plan_category->network->id,
            'whatsapp_phone' => $session['whatsapp_phone'],
            'phone' => $recipientPhone,
            'price' => $price,
            // 'intent' => [
            //     'selected_plan_id' => $planId,
            // ],
            'message' =>
                "Confirm Purchase\n\n"
                . "{$plan->product_plan_name}\n"
                . "Phone: {$recipientPhone}\n"
                . "Price: ₦" . number_format($price)
                . "\n\nReply YES/Confirm to continue or NO/Cancel to cancel."
        ];
    
        cache()->put(
            "wa_session:" . $session['whatsapp_phone'],
            $result,
            now()->addMinutes(10)
        );
    
        // app(Whatsappsender::class)->sendConfirmationButtons(
        //     $session['whatsapp_phone'],
        //     $result['message']
        // );

        $user = app(WhatsappUserResolver::class)
        ->resolve($phone);

        $this->sendPhoneRequestWithContacts(
            $user,
            $session['whatsapp_phone'],
            $result
        );
    
        return response()->json([
            'ok' => true
        ]);
    }

    

    public function handleSaveContactName(
        string $text,
        array $session,
        $user
    )
    {
        $name = trim($text);
    
        if (strlen($name) < 3) {
    
            app(Whatsappsender::class)->send(
                $session['whatsapp_phone'],
                "Please enter a valid contact name with atleast 3 characters."
            );
    
            return response()->json(['ok' => true]);
        }
    
        UserContact::updateOrCreate(
            [
                'user_id' => $user->id,
                'phone_number' => $session['phone'],
            ],
            [
                'name' => $name,
                'network_id' => $session['network_id'] ?? null,
                'product' => 'data',
                'product_plan_id' => $session['product_plan_id'] ?? null,
                'last_used_at' => now(),
            ]
        );
    
        cache()->forget(
            "wa_session:{$session['whatsapp_phone']}"
        );
    
        app(Whatsappsender::class)->sendStartButton(
            $session['whatsapp_phone'],
            "✅ Awesome. Contact Saved\n\n"
            . "{$name}\n"
            . "{$session['phone']}\n\n"
            . "Next time you can simply choose this contact instead of typing the number again."
        );
    
        return response()->json(['ok' => true]);
    }
    
    public function handleSaveContactPrompt(
        string $text,
        array $session
    )
    {
        if ($text === 'save_contact_no') {
    
            cache()->forget(
                "wa_session:{$session['whatsapp_phone']}"
            );
    
            app(Whatsappsender::class)->sendStartButton(
                $session['whatsapp_phone'],
                "👍 Alright.\n\nSee you again soon."
            );
    
            return response()->json(['ok' => true]);
        }
    
        if ($text !== 'save_contact_yes') {
    
            app(Whatsappsender::class)->sendSaveContactButtons(
                $session['whatsapp_phone'],
                "Would you like to save this number for future purchases?"
            );
    
            return response()->json(['ok' => true]);
        }
    
        cache()->put(
            "wa_session:{$session['whatsapp_phone']}",
            [
                ...$session,
                'status' => 'contact_name_required',
            ],
            now()->addMinutes(10)
        );
    
        app(Whatsappsender::class)->send(
            $session['whatsapp_phone'],
            "💾 Great!\n\nWhat name would you like to save this contact as?\n\nExamples:\n• Mum\n• Dad\n• John"
        );
    
        return response()->json(['ok' => true]);
    }
    public function handleConfirmation(string $text, $user, array $session)
    {

        //you must specify if its data or airtime or ccable or elecc later oh.

        $chatPhone = $session['whatsapp_phone'];
        $beneficiaryPhone = $session['phone'];
    
        $text = strtolower(trim($text));
    
        /*
        User cancelled
        */
        if ($text === 'no') {
    
            cache()->forget("wa_session:$chatPhone");
    
            app(Whatsappsender::class)->sendStartButton(
                $chatPhone,
                "❌ Purchase cancelled.\n\nNo worries — you can type *START* anytime to make another purchase."
            );
    
            return response()->json(['ok' => true]);
        }

        /*
        Invalid response
        */
        if ($text !== 'yes') {

            app(Whatsappsender::class)->sendConfirmationButtons(
                $chatPhone,
                "🤔 I didn't quite get that.\n\nPlease confirm whether you'd like to continue with this purchase."
            );

            return response()->json(['ok' => true]);
        }


        if (!$session) {
             app(Whatsappsender::class)->sendStartButton(
                $chatPhone,
                "⌛ This session has expired.\n\nType *START* to begin a new purchase."
            );
            return response()->json(['ok' => true]);

        }

        try {
            $request = new Request([
                'product_plan_id' => $session['product_plan_id'],
                'phone_number' => $session['phone'],
                'network_id' => $session['network_id'],
                'wallet_category' => 'main_wallet',
                'validatephonenetwork' => 0,
                'pin' =>$user->pin,
                'user' =>$user,
            ]);
    
            $result = app(\App\Http\Controllers\DataController::class)
                ->buy_data_action($request);
    
                
            /*
            Convert response object → array
            */
            $data = $result->getData(true);
    
            /*
            Read status
            */
            $status  = $data['status'] ?? -1;
            $message = $data['message'] ?? 'Transaction completed';
    
            /*
            Build WhatsApp message
            */
           /*
            SUCCESS
            */
            if ($status === 1) {

                cache()->put(
                    "wa_session:$chatPhone",
                    [
                        'status' => 'contact_save_prompt',
                        'phone' => $session['phone'],
                        'network_id' => $session['network_id'],
                        'product_plan_id' => $session['product_plan_id'],
                        'whatsapp_phone' => $chatPhone,
                    ],
                    now()->addMinutes(10)
                );
            
                app(Whatsappsender::class)->sendSaveContactButtons(
                    $chatPhone,
                    "🎉 Purchase Successful!\n\n"
                    . $message
                    . "\n\nWould you like to save this number for future purchases?"
                );
            
                return response()->json(['ok' => true]);
            }
            
            /*
            FAILED / REFUNDED / ISSUE FOUND
            */
            elseif ($status === 2) {
    
                $reply =
                "❌ Purchase Failed\n\n"
                . $message
                . "\n\nWould you like to try again?";

                
                app(Whatsappsender::class)->sendRetryButtons(
                    $chatPhone,
                    $reply
                );

               return response()->json(['ok' => true]);

    
            }
            /*
            VALIDATION / SYSTEM ERROR
            */
            else {
    
                $reply =
                "⚠️ We couldn't complete that request.\n\n"
                . $message
                . "\n\nYou can try again or start a new request.";

                app(Whatsappsender::class)->sendRetryButtons(
                    $chatPhone,
                    $reply
                );
               return response()->json(['ok' => true]);

            }
    
        }catch (\Throwable $e) {

            logger('WhatsApp confirmation error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
    
            app(Whatsappsender::class)->sendRetryButtons(
                $chatPhone,
                "⚠️ Something unexpected happened while processing your request.\n\nWould you like to try again?"
            );
    
            return response()->json([
                'ok' => true
            ]);
        }

       
    }
}

