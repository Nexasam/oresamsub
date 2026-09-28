<?php

namespace App\Services\Opay;

final readonly class OpayPreparedRequest
{
    /**
     * @param  array<string, string>  $headers
     * @param  array{encrypt_data: string, encrypt_aes_key: string}  $body
     */
    public function __construct(
        public string $method,
        public string $url,
        public string $path,
        public array $headers,
        public array $body,
        public string $canonicalJson,
        public string $aesKey,
    ) {}
}
