<?php
namespace App\Services\Whatsapp;

use App\Http\Services\DataPlansService;
use App\Models\ProductPlan;
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
    
        public function handleDataPhoneInput(
            string $text,
            array $session
        ) {
            $intent = $session['intent'];

            $textt = $this->normalizeWhatsappNumber($text);
    
            $intent['phone'] = trim($textt);

            logger('phone input: '.$textt);
    
            return $this->updateSessionAndResolve(
                $session,
                $intent,
                $session['whatsapp_phone']
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
                    "Invalid selection. Reply with one of the numbers shown."
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

            logger(json_encode([
                'intent' => $intent,
                'intent_phone' => $intent['phone'] ?? null,
            ]));

            $intent['selected_plan_id'] = $planId;

      

            $user = app(WhatsappUserResolver::class)
                ->resolve($phone);
            logger(json_encode([
                'DATA selection user' => $user,
            ]));
    
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
                'product_plan_id' => $plan->id,
                'network_id' => $plan->product_plan_category->network->id,
                'phone' => $recphone,
                'price' => $price,
                'intent' => $intent,
                'message' =>
                    "Confirm Purchase\n\n"
                    . "{$plan->product_plan_name}\n"
                    . "Phone: {$intent['phone']}\n"
                    . "Price: ₦" . number_format($price)
                    . "\n\nReply YES to continue or NO to cancel."
            ];
    

          
           
            cache()->put(
                "wa_session:" . $session['whatsapp_phone'],
                $result,
                now()->addMinutes(10)
            );

            //this means we already captured the plan beforer asking for the number.
            if (empty($intent['phone'])) {

                cache()->put(
                    "wa_session:" . $session['whatsapp_phone'],
                    [
                        'status' => 'data_phone_required',
                        'intent' => $intent,
                        'whatsapp_phone' => $session['whatsapp_phone'],
                    ],
                    now()->addMinutes(10)
                );

                app(Whatsappsender::class)->send(
                    $session['whatsapp_phone'],
                    "Enter the phone number to receive this data."
                );

                return response()->json(['ok' => true]);
            }

          

    
            app(Whatsappsender::class)->send(
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
    
            app(Whatsappsender::class)->send(
                $chatPhone,
                "❌ Transaction cancelled."
            );
    
            return response()->json(['ok' => true]);
        }

        /*
        Invalid response
        */
        if ($text !== 'yes') {

            app(Whatsappsender::class)->send(
                $chatPhone,
                "Reply YES to continue or NO to cancel."
            );

            return response()->json(['ok' => true]);
        }


        if (!$session) {
            return app(Whatsappsender::class)->send(
                $chatPhone,
                "Session expired. Please type 'start' again."
            );
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
    
                cache()->forget("wa_session:$chatPhone");
    
                $reply =
                    "✅ Transaction Successful\n\n"
                    . $message;
    
            }
            /*
            FAILED / REFUNDED / ISSUE FOUND
            */
            elseif ($status === 2) {
    
                $reply =
                    "❌ Transaction Failed\n\n"
                    . $message
                    . "\n\nReply YES to retry or START to begin again.";
    
            }
            /*
            VALIDATION / SYSTEM ERROR
            */
            else {
    
                $reply =
                    "⚠️ Transaction Error\n\n"
                    . $message
                    . "\n\nReply YES to retry or START to begin again.";
            }
    
            /*
            Send WhatsApp response
            */
            app(\App\Services\Whatsapp\Whatsappsender::class)->send(
                $chatPhone,
                $reply
            );
    
            return response()->json(['ok' => true]);
        }catch (\Throwable $e) {

            logger('WhatsApp confirmation error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
    
            app(Whatsappsender::class)->send(
                $chatPhone,
                "⚠️ Something went wrong while processing your request.\n\nReply YES to retry or START to begin again."
            );
    
            return response()->json([
                'ok' => true
            ]);
        }

       
    }
}

