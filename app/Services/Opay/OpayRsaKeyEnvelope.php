<?php

namespace App\Services\Opay;

use RuntimeException;

final class OpayRsaKeyEnvelope
{
    public function encryptAsciiKey(string $asciiKey, string $publicKeyPem): string
    {
        $publicKey = openssl_pkey_get_public($publicKeyPem);

        if ($publicKey === false) {
            throw new RuntimeException('Invalid OPay RSA public key.');
        }

        $ok = openssl_public_encrypt(
            $asciiKey,
            $encrypted,
            $publicKey,
            OPENSSL_PKCS1_PADDING,
        );

        if (! $ok) {
            throw new RuntimeException('Unable to RSA encrypt OPay AES key.');
        }

        return str_replace(["\r", "\n", '\\'], '', base64_encode($encrypted));
    }
}
