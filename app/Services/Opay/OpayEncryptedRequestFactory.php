<?php

namespace App\Services\Opay;

final readonly class OpayEncryptedRequestFactory
{
    public function __construct(
        private OpayCanonicalJson $canonicalJson = new OpayCanonicalJson(),
        private OpaySigner $signer = new OpaySigner(),
        private OpayAesCipher $aes = new OpayAesCipher(),
        private OpayRsaKeyEnvelope $rsa = new OpayRsaKeyEnvelope(),
    ) {}

    public function make(
        array|string|null $payload,
        OpayTransportContext $context,
        string $publicKeyPem,
        ?int $timestamp = null,
        ?string $aesKey = null,
    ): OpayEncryptedRequest {
        $canonicalJson = $this->canonicalJson->canonicalize($payload);
        $timestamp ??= (int) floor(microtime(true) * 1000);
        $aesKey ??= $this->aes->generateAsciiKey();

        $headers = [
            'app' => $context->app,
            'platform' => $context->platform,
            'version_name' => $context->versionName,
            'device_id' => $context->deviceId,
            'timestamp' => (string) $timestamp,
            'signV3' => $this->signer->signV3(
                canonicalJson: $canonicalJson,
                app: $context->app,
                platform: $context->platform,
                versionName: $context->versionName,
                deviceId: $context->deviceId,
                timestamp: $timestamp,
                token: $context->token,
            ),
        ];

        return new OpayEncryptedRequest(
            canonicalJson: $canonicalJson,
            headers: $headers,
            body: [
                'encrypt_data' => $this->aes->encrypt($canonicalJson, $aesKey),
                'encrypt_aes_key' => $this->rsa->encryptAsciiKey($aesKey, $publicKeyPem),
            ],
            aesKey: $aesKey,
        );
    }
}
