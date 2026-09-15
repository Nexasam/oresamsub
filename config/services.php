<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'securewave' => [
        'webhook_secret1' => env('SECUREWAVESECRET1'),
        'webhook_secret2' =>  env('SECUREWAVESECRET2'),
        'balance_url' => env('SECUREWAVE_BALANCE_URL', 'https://securewaveng.com/api/balance'),
        'customer_create_url' => env('SECUREWAVE_CUSTOMER_CREATE_URL', 'https://securewaveng.com/api/customers/create'),
        'customer_fund_url' => env('SECUREWAVE_CUSTOMER_FUND_URL', 'https://securewaveng.com/api/customer_withdrawals/withdraw'),
    ],

    'whatsapp' => [
        'token' => env('WHATSAPP_ACCESS_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
