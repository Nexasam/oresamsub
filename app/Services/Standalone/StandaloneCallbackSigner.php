<?php

namespace App\Services\Standalone;

class StandaloneCallbackSigner
{
    public function sign(string $rawBody, int $timestamp, string $secret): string
    {
        return hash_hmac('sha256', $timestamp.'.'.$rawBody, $secret);
    }
}
