<?php

namespace App\Services\Standalone;

use Illuminate\Support\Str;

class StandaloneCredentialService
{
    public function issueBootstrapToken(): array
    {
        return $this->issue('ors_bootstrap_', 'bootstrap', now()->addMinutes(20), true);
    }

    public function issueOperationalToken(): array
    {
        return $this->issue('ors_live_', 'operational', null, false);
    }

    /** @deprecated Use issueOperationalToken. */
    public function issueApiToken(): array
    {
        return $this->issueOperationalToken();
    }

    private function issue(string $prefix, string $type, mixed $expiresAt, bool $mustRotate): array
    {
        $plainText = $prefix.Str::random(64);

        return [
            'plain_text' => $plainText,
            'digest' => hash('sha256', $plainText),
            'prefix' => substr($plainText, 0, 16),
            'type' => $type,
            'must_rotate' => $mustRotate,
            'expires_at' => $expiresAt,
        ];
    }
}
