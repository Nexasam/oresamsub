<?php

namespace App\Services\Opay;

use RuntimeException;

final readonly class OpayResponseDecoder
{
    public function __construct(
        private OpayAesCipher $aes = new OpayAesCipher(),
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function decode(string $body, string $aesKey): array
    {
        $body = trim($body);

        if ($body === '') {
            throw new RuntimeException('OPay response body is empty.');
        }

        $plainJson = $this->decodeJson($body);

        if ($plainJson !== null) {
            return $plainJson;
        }

        return $this->requireArrayJson($this->aes->decrypt($body, $aesKey));
    }

    /**
     * @param  array<string, mixed>  $response
     * @return array<string, mixed>
     */
    public function authSummary(array $response): array
    {
        $data = is_array($response['data'] ?? null) ? $response['data'] : [];
        $accessToken = is_array($data['authAccessToken'] ?? null) ? $data['authAccessToken'] : [];

        return [
            'ret_code' => (string) ($response['retCode'] ?? $response['code'] ?? ''),
            'message' => (string) ($response['message'] ?? $response['msg'] ?? ''),
            'has_access_token' => filled((string) ($accessToken['value'] ?? '')),
            'access_token_expires_at' => $accessToken['expires_at'] ?? null,
            'has_refresh_token' => filled((string) ($data['refreshToken'] ?? '')),
            'has_security_id' => filled((string) ($data['securityId'] ?? '')),
            'has_val_chain_id' => filled((string) ($data['valChainId'] ?? '')),
            'validation_types' => $this->validationTypes($data),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodeJson(string $body): ?array
    {
        $decoded = json_decode($body, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function requireArrayJson(string $json): array
    {
        $decoded = json_decode($json, true);

        if (! is_array($decoded)) {
            throw new RuntimeException('OPay response payload is not valid JSON.');
        }

        return $decoded;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return list<string>
     */
    private function validationTypes(array $data): array
    {
        $steps = is_array($data['valStepInfos'] ?? null) ? $data['valStepInfos'] : [];

        return collect($steps)
            ->map(fn (mixed $step): string => is_array($step)
                ? (string) ($step['validationType'] ?? $step['type'] ?? '')
                : '')
            ->filter()
            ->values()
            ->all();
    }
}
