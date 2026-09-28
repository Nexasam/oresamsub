<?php

namespace App\Services\Opay;

final readonly class OpayCatalogueClient
{
    public const MOBILE_DATA_CALCULATE_PATH = '/api/v1/mobiledata/calculate';

    public function __construct(
        private OpayEncryptedRequestFactory $encryptedRequestFactory = new OpayEncryptedRequestFactory(),
        private string $baseUrl = '',
        private string $publicKeyPem = '',
        private string $packageName = 'team.opay.pay',
        private string $versionCode = '8458272',
    ) {}

    public function prepareMobileDataCalculate(
        OpayTransportContext $context,
        ?int $timestamp = null,
        ?string $aesKey = null,
    ): OpayPreparedRequest {
        $encrypted = $this->encryptedRequestFactory->make(
            payload: null,
            context: $context,
            publicKeyPem: $this->publicKeyPem,
            timestamp: $timestamp,
            aesKey: $aesKey,
        );

        return new OpayPreparedRequest(
            method: 'POST',
            url: $this->normalizedBaseUrl().self::MOBILE_DATA_CALCULATE_PATH,
            path: self::MOBILE_DATA_CALCULATE_PATH,
            headers: [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'pn' => $this->packageName,
                'version_code' => $this->versionCode,
                'version_name' => $context->versionName,
                'token' => $context->token,
                ...$encrypted->headers,
            ],
            body: $encrypted->body,
            canonicalJson: $encrypted->canonicalJson,
            aesKey: $encrypted->aesKey,
        );
    }

    private function normalizedBaseUrl(): string
    {
        $baseUrl = $this->baseUrl !== ''
            ? $this->baseUrl
            : (string) config('opay.base_url', 'https://wallet-living.opayweb.com');

        return rtrim($baseUrl, '/');
    }
}
