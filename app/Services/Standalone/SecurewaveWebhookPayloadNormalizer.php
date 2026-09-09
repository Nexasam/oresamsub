<?php

namespace App\Services\Standalone;

class SecurewaveWebhookPayloadNormalizer
{
    public function normalize(array $payload): array
    {
        $normalized = $payload;

        foreach ([data_get($payload, 'data'), data_get($payload, 'event_data.data')] as $candidate) {
            if (is_array($candidate) && $this->looksLikeTransaction($candidate)) {
                $normalized = array_replace($normalized, $candidate);
            }
        }

        return $normalized;
    }

    private function looksLikeTransaction(array $payload): bool
    {
        return collect(['transaction_status', 'provider_reference', 'transaction_reference', 'transaction_id', 'receiver', 'settlement_amount'])
            ->contains(fn (string $key): bool => array_key_exists($key, $payload));
    }
}
