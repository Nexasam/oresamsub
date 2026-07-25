<?php

namespace App\Services\Whatsapp;

use App\Models\WhatsappConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MegaWhatsappService
{
    protected function config(): WhatsappConfig
    {
        return WhatsappConfig::firstOrFail();
    }

    protected function endpoint(): string
    {
        $config = $this->config();

        return sprintf(
            'https://graph.facebook.com/v23.0/%s/messages',
            $config->phone_number_id
        );
    }

    protected function token(): string
    {
        return $this->config()->token;
    }

    protected function sendPayload(
        array $payload
    ): array {

        $response = Http::withToken(
            $this->token()
        )->post(
            $this->endpoint(),
            $payload
        );

        Log::info(
            'Mega WhatsApp Response',
            [
                'payload' => $payload,
                'response' => $response->json(),
            ]
        );

        return $response->json();
    }

    public function sendText(
        string $phone,
        string $message
    ): array {

        return $this->sendPayload([
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'text',
            'text' => [
                'preview_url' => false,
                'body' => $message,
            ],
        ]);
    }

    public function sendButtons(
        string $phone,
        string $message,
        array $buttons
    ): array {

        $metaButtons = [];

        foreach ($buttons as $button) {

            $metaButtons[] = [
                'type' => 'reply',
                'reply' => [
                    'id' => $button['id'],
                    'title' => $button['title'],
                ],
            ];
        }

        return $this->sendPayload([
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
                    'buttons' => $metaButtons,
                ],
            ],
        ]);
    }

    public function sendList(
        string $phone,
        string $message,
        array $rows,
        string $buttonText = 'Select'
    ): array {

        $formattedRows = [];

        foreach ($rows as $row) {

            $formattedRows[] = [
                'id' => (string) $row['id'],
                'title' => $row['title'],
                'description' => $row['description'] ?? '',
            ];
        }

        return $this->sendPayload([
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $phone,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'list',
                'body' => [
                    'text' => $message,
                ],
                'action' => [
                    'button' => $buttonText,
                    'sections' => [
                        [
                            'title' => 'Available Options',
                            'rows' => $formattedRows,
                        ],
                    ],
                ],
            ],
        ]);
    }
}