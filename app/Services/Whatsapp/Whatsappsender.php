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
                                'id' => 'account_data_airtime_help',
                                'title' => 'Buy Data/Airtime',
                            ],
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'start_over',
                                'title' => 'Start',
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
    




    //////////////////////
    //////////////////////
    ////interactive buttons for whatsapp

    public function sendMainMenu(
        string $phone,
        string $firstName = 'there'
    )
    {
        $wconfig = WhatsappConfig::first();
    
        $url = "https://graph.facebook.com/v23.0/{$wconfig->phone_number_id}/messages";
    
        $message =
            "👋 Welcome " . $firstName . "!\n\n"
            . "Welcome to OresamSub ⚡\n\n"
            . "Your fastest VTU platform for Airtime & Data in Nigeria.\n\n"
            . "Choose an option below to continue or simply type your request.\n\n"
            . "⚡ POWER USERS EXAMPLES:\n"
            . "• MTN 1GB\n"
            . "• Airtel Airtime 1000\n"
            . "• Glo 2GB Monthly\n\n"
            . "🚀 QUICK TIP:\n"
            . "You can type naturally OR use buttons below.";
    
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
    
                    'footer' => [
                        'text' => 'OresamSub • Fast | Reliable | Secure'
                    ],
    
                    'action' => [
                        'buttons' => [
    
                            [
                                'type' => 'reply',
                                'reply' => [
                                    'id' => 'product_data',
                                    'title' => '📱 Data',
                                ],
                            ],
    
                            [
                                'type' => 'reply',
                                'reply' => [
                                    'id' => 'product_airtime',
                                    'title' => '📞 Airtime',
                                ],
                            ],
    
                            [
                                'type' => 'reply',
                                'reply' => [
                                    'id' => 'how_to',
                                    'title' => '❓ Help',
                                ],
                            ],
    
                        ],
                    ],
                ],
            ]);
    }



    public function sendNetworkList(
        string $phone
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
                        'type' => 'list',
    
                        'header' => [
                            'type' => 'text',
                            'text' => '📱 Buy Data'
                        ],
    
                        'body' => [
                            'text' => 'Select your preferred network'
                        ],
    
                        'footer' => [
                            'text' => 'OresamSub'
                        ],
    
                        'action' => [
                            'button' => 'Choose Network',
    
                            'sections' => [
                                [
                                    'title' => 'Available Networks',
    
                                    'rows' => [
    
                                        [
                                            'id' => 'network_1',
                                            'title' => 'MTN',
                                            'description' => 'MTN Data Plans'
                                        ],
    
                                        [
                                            'id' => 'network_2',
                                            'title' => 'Airtel',
                                            'description' => 'Airtel Data Plans'
                                        ],
    
                                        [
                                            'id' => 'network_3',
                                            'title' => 'Glo',
                                            'description' => 'Glo Data Plans'
                                        ],
    
                                        [
                                            'id' => 'network_4',
                                            'title' => '9mobile',
                                            'description' => '9mobile Data Plans'
                                        ],
    
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );
    }


    public function sendDataSizeList(
        string $phone,
        array $sizes
    )
    {
        $wconfig = WhatsappConfig::first();
    
        $url = "https://graph.facebook.com/v23.0/{$wconfig->phone_number_id}/messages";
    
        $rows = [];
    
        foreach ($sizes as $size) {
    
            $label = $size >= 1000
                ? ($size / 1000).'GB'
                : $size.'MB';
    
            $rows[] = [
                'id' => "size_{$size}",
                'title' => $label,
                'description' => 'Select size',
            ];
        }
    
        return Http::withToken($wconfig->token)
            ->post(
                $url,
                [
                    'messaging_product' => 'whatsapp',
                    'to' => $phone,
                    'type' => 'interactive',
    
                    'interactive' => [
                        'type' => 'list',
    
                        'header' => [
                            'type' => 'text',
                            'text' => '📶 Data Size'
                        ],
    
                        'body' => [
                            'text' => 'Select your preferred data size'
                        ],
    
                        'footer' => [
                            'text' => 'OresamSub'
                        ],
    
                        'action' => [
                            'button' => 'Choose Size',
    
                            'sections' => [
                                [
                                    'title' => 'Available Sizes',
                                    'rows' => $rows
                                ]
                            ]
                        ]
                    ]
                ]
            );
    }

   
}