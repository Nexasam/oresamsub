<?php

namespace App\Services\Standalone;

use Illuminate\Support\Str;

class StandaloneCredentialService
{
    public function issueApiToken(): array
    {
        $plainText = 'ors_live_'.Str::random(64);

        return [
            'plain_text' => $plainText,
            'digest' => hash('sha256', $plainText),
            'prefix' => substr($plainText, 0, 16),
        ];
    }

    public function issueSigningSecret(): array
    {
        $plainText = 'ors_whsec_'.Str::random(64);

        return [
            'plain_text' => $plainText,
            'encrypted' => $plainText,
            'hint' => substr($plainText, 0, 12).'••••'.substr($plainText, -4),
        ];
    }
}
