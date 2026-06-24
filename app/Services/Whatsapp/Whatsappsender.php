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
}