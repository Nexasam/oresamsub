<?php

namespace App\Services\Opay;

use InvalidArgumentException;

final class OpayCanonicalJson
{
    public function canonicalize(array|string|null $payload): string
    {
        if ($payload === null) {
            return '{}';
        }

        if (is_array($payload)) {
            return $this->encode($payload);
        }

        $decoded = json_decode($payload, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            throw new InvalidArgumentException('OPay request payload must be a JSON object or array.');
        }

        return $this->encode($decoded);
    }

    private function encode(array $payload): string
    {
        $encoded = json_encode($payload, JSON_UNESCAPED_SLASHES);

        if ($encoded === false) {
            throw new InvalidArgumentException('OPay request payload could not be JSON encoded.');
        }

        return $encoded;
    }
}
