<?php
namespace App\Services\Whatsapp;

use App\Models\WhatsappConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Whatsappsender
{
    public function send(string $phone, string $message): array
    {
        $wconfig = WhatsappConfig::first();
        $phone_number_id = $wconfig->phone_number_id ?? '323';
        $token = $wconfig->token ?? '434';
        // $token = config('services.whatsapp.token');
        $response = Http::withToken(
            $token
        )->post(
            "https://graph.facebook.com/v23.0/" .
             $phone_number_id .
            "/messages",
            [
                'messaging_product' => 'whatsapp',
                'to' => $phone,
                'type' => 'text',
                'text' => [
                    'body' => $message
                ]
            ]
        );

        Log::info('WhatsApp Response', [
            'phone' => $phone,
            'response' => $response->json()
        ]);

        return $response->json();
    }

    public function sendAccountButtons(
        string $phone,
        string $message
    )
    {
        $wconfig = WhatsappConfig::first();
    
        $url = "https://graph.facebook.com/v23.0/{$wconfig->phone_number_id}/messages";
    
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $phone,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => [
                    'text' => $message,
                ],
              'action' => [
                    'buttons' => [
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'account_refresh_balance',
                                'title' => 'Refresh',
                            ],
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'account_data_help',
                                'title' => 'Buy Data',
                            ],
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'account_airtime_help',
                                'title' => 'Airtime',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    
        return Http::withToken($wconfig->token)
            ->post($url, $payload)
            ->json();
    }

    public function sendConfirmationButtons(
        string $phone,
        string $message
    )
    {
        $wconfig = WhatsappConfig::first();
    
        $url = "https://graph.facebook.com/v23.0/{$wconfig->phone_number_id}/messages";
    
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $phone,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => [
                    'text' => $message,
                ],
                'action' => [
                    'buttons' => [
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'data_confirm_purchase',
                                'title' => 'Confirm',
                            ],
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'data_cancel_purchase',
                                'title' => 'Cancel',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    
        return Http::withToken($wconfig->token)
            ->post($url, $payload)
            ->json();
    }


    public function sendRetryButtons(
        string $phone,
        string $message
    )
    {
        $wconfig = WhatsappConfig::first();
    
        $url = "https://graph.facebook.com/v23.0/{$wconfig->phone_number_id}/messages";

        return Http::withToken($wconfig->token)
            ->post(
               $url,
                [
                    'messaging_product' => 'whatsapp',
                    'to' => $phone,
                    'type' => 'interactive',
    
                    'interactive' => [
                        'type' => 'button',
    
                        'body' => [
                            'text' => $message,
                        ],
    
                        'action' => [
                            'buttons' => [
    
                                [
                                    'type' => 'reply',
                                    'reply' => [
                                        'id' => 'retry_purchase',
                                        'title' => '🔄 Retry',
                                    ],
                                ],
    
                                [
                                    'type' => 'reply',
                                    'reply' => [
                                        'id' => 'start_over',
                                        'title' => '🏠 Start',
                                    ],
                                ],
    
                            ],
                        ],
                    ],
                ]
            );
    }

    public function sendStartButton(
        string $phone,
        string $message
    )
    {
        $wconfig = WhatsappConfig::first();
    
        $url = "https://graph.facebook.com/v23.0/{$wconfig->phone_number_id}/messages";

        return Http::withToken($wconfig->token)
            ->post(
               $url,
                [
                    'messaging_product' => 'whatsapp',
                    'to' => $phone,
                    'type' => 'interactive',
    
                    'interactive' => [
                        'type' => 'button',
    
                        'body' => [
                            'text' => $message,
                        ],
    
                        'action' => [
                            'buttons' => [
                                [
                                    'type' => 'reply',
                                    'reply' => [
                                        'id' => 'start_over',
                                        'title' => '🏠 Start Again',
                                    ],
                                ],
    
                            ],
                        ],
                    ],
                ]
            );
    }

    public function sendSaveContactButtons(
        string $phone,
        string $message
    )
    {
        $wconfig = WhatsappConfig::first();
    
        $url = "https://graph.facebook.com/v23.0/{$wconfig->phone_number_id}/messages";
    
        return Http::withToken($wconfig->token)
            ->post($url, [
                'messaging_product' => 'whatsapp',
                'to' => $phone,
                'type' => 'interactive',
    
                'interactive' => [
                    'type' => 'button',
    
                    'body' => [
                        'text' => $message,
                    ],
    
                    'action' => [
                        'buttons' => [
                            [
                                'type' => 'reply',
                                'reply' => [
                                    'id' => 'save_contact_yes',
                                    'title' => '💾 Save Contact',
                                ],
                            ],
    
                            [
                                'type' => 'reply',
                                'reply' => [
                                    'id' => 'save_contact_no',
                                    'title' => 'Skip',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
    }
    

   
}