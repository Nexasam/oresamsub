<?php

namespace App\Services\Opay;

final readonly class OpayEncryptedRequest
{
    /**
     * @param  array<string, string>  $headers
     * @param  array{encrypt_data: string, encrypt_aes_key: string}  $body
     */
    public function __construct(
        public string $canonicalJson,
        public array $headers,
        public array $body,
        public string $aesKey,
    ) {}
}
