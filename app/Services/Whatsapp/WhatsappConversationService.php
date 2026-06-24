<?php
namespace App\Services\Whatsapp;

use App\Http\Services\DataPlansService;
use App\Models\ProductPlan;
use Illuminate\Http\Request;

class WhatsappConversationService{
    public function handleConfirmation(string $text, $user, array $session)
    {

        //you must specify if its data or airtime or ccable or elecc later oh.

        $phone = $session['phone'];
        $text = strtolower($text);
    
        if ($text !== 'yes') {
    
            cache()->forget("wa_session:$phone");
    
            return app(Whatsappsender::class)->send(
                $phone,
                "Transaction cancelled."
            );
        }

        if (!$session) {
            return app(Whatsappsender::class)->send(
                $phone,
                "Session expired. Please start again."
            );
        }

        $request = new Request([
            'product_plan_id' => $session['product_plan_id'],
            'phone_number' => $session['phone'],
            'network_id' => $session['network_id'],
            'wallet_category' => 'main_wallet',
            'validatephonenetwork' => 0,
            'pin' =>$user->pin,
        ]);

        $result = app(\App\Http\Controllers\DataController::class)
            ->buy_data_action($request);

        // clear session
        cache()->forget("wa_session:$phone");

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
        if ((int)$status === 1) {

            $reply = "✅ Transaction Successful\n\n" . $message;

        } else {

            $reply = "❌ Transaction Failed\n\n" . $message;
        }

        /*
        Send WhatsApp response
        */
        app(\App\Services\Whatsapp\Whatsappsender::class)->send(
            $phone,
            $reply
        );

        return response()->json(['ok' => true]);
    }
}

