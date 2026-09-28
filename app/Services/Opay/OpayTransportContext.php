<?php

namespace App\Services\Opay;

final readonly class OpayTransportContext
{
    public function __construct(
        public string $app,
        public string $platform,
        public string $versionName,
        public string $deviceId,
        public string $token,
        /** @var array<string, string> */
        public array $extraHeaders = [],
    ) {}
}
