<?php
namespace App\Services\Whatsapp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappSender
{
    public function send(string $phone, string $message): array
    {
        $response = Http::withToken(
            config('services.whatsapp.token')
        )->post(
            "https://graph.facebook.com/v23.0/" .
            config('services.whatsapp.phone_number_id') .
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
}