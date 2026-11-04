<?php

namespace App\Services\Whatsapp;

use App\Models\OreWhatsappConfig;
use App\Models\WhatsappConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MegaWhatsappService
{
    protected function config(): object
    {
        return WhatsappConfig::first()
            ?? OreWhatsappConfig::first()
            ?? (object) [
                'token' => config('services.whatsapp.token'),
                'phone_number_id' => config('services.whatsapp.phone_number_id'),
            ];
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

        $responseBody = $response->json();
        $context = [
            'message_type' => $payload['type'] ?? 'unknown',
            'recipient_last_four' => substr((string) ($payload['to'] ?? ''), -4),
            'http_status' => $response->status(),
            'meta_message_id' => data_get($responseBody, 'messages.0.id'),
            'error_code' => data_get($responseBody, 'error.code'),
        ];

        if ($response->successful()) {
            Log::info('Mega WhatsApp request accepted.', $context);
        } else {
            Log::warning('Mega WhatsApp request rejected.', $context);
        }

        return is_array($responseBody) ? $responseBody : [];
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

    public function sendTemplate(
        string $phone,
        string $templateName,
        array $bodyParameters,
        string $language = 'en'
    ): array {
        return $this->sendPayload([
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $phone,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $language,
                ],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => array_map(
                            fn ($value): array => [
                                'type' => 'text',
                                'text' => (string) $value,
                            ],
                            $bodyParameters
                        ),
                    ],
                ],
            ],
        ]);
    }
}
