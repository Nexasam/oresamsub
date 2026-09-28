<?php

namespace App\Services\Opay;

use RuntimeException;

final class OpayAesCipher
{
    public const IV = '2022111500123456';

    public function generateAsciiKey(): string
    {
        return strtoupper(bin2hex(random_bytes(16)));
    }

    public function encrypt(string $plaintext, string $asciiKey): string
    {
        $this->assertKey($asciiKey);

        $ciphertext = openssl_encrypt(
            $plaintext,
            'AES-256-CBC',
            $asciiKey,
            OPENSSL_RAW_DATA,
            self::IV,
        );

        if ($ciphertext === false) {
            throw new RuntimeException('Unable to AES encrypt OPay request payload.');
        }

        return $this->cleanBase64(base64_encode($ciphertext));
    }

    public function decrypt(string $base64Ciphertext, string $asciiKey): string
    {
        $this->assertKey($asciiKey);

        $ciphertext = base64_decode($base64Ciphertext, true);

        if ($ciphertext === false) {
            throw new RuntimeException('OPay encrypted payload is not valid Base64.');
        }

        $plaintext = openssl_decrypt(
            $ciphertext,
            'AES-256-CBC',
            $asciiKey,
            OPENSSL_RAW_DATA,
            self::IV,
        );

        if ($plaintext === false) {
            throw new RuntimeException('Unable to AES decrypt OPay payload.');
        }

        return $plaintext;
    }

    private function assertKey(string $asciiKey): void
    {
        if (! preg_match('/\A[0-9A-F]{32}\z/', $asciiKey)) {
            throw new RuntimeException('OPay AES key must be a 32-character uppercase hexadecimal string.');
        }
    }

    private function cleanBase64(string $value): string
    {
        return str_replace(["\r", "\n", '\\'], '', $value);
    }
}
