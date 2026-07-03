<?php

namespace App\Services\Whatsapp;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WhatsappInteractiveService
{
    public function __construct(
        protected Whatsappsender $sender
    ) {}

    public function handle(
        string $phone,
        string $replyId,
        $user
    )
    {
        Log::info('WhatsApp Interactive Reply', [
            'interactive_reply' => $replyId,
            'phone' => $phone,
        ]);

        switch ($replyId) {

            case 'product_data':

                Cache::put(
                    "wa_interactive:$phone",
                    [
                        'product' => 'data',
                    ],
                    now()->addMinutes(20)
                );

                $this->sender->sendNetworkList($phone);

                break;

            case 'product_airtime':

                Cache::put(
                    "wa_interactive:$phone",
                    [
                        'product' => 'airtime',
                    ],
                    now()->addMinutes(20)
                );

                // Airtime network list later
                $this->sender->sendNetworkList($phone);

                break;
        }

        return true;
    }
}