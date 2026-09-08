<?php

use App\Services\Standalone\StandaloneCredentialService;

it('issues independent API and webhook credentials with safe metadata', function () {
    $service = new StandaloneCredentialService;

    $api = $service->issueApiToken();
    $secret = $service->issueSigningSecret();

    expect($api['plain_text'])->toStartWith('ors_live_')
        ->and($api['digest'])->toBe(hash('sha256', $api['plain_text']))
        ->and($api['prefix'])->toBe(substr($api['plain_text'], 0, 16))
        ->and($secret['plain_text'])->toStartWith('ors_whsec_')
        ->and($secret['encrypted'])->toBe($secret['plain_text'])
        ->and($secret['hint'])->toContain('••••')
        ->and($secret['plain_text'])->not->toBe($api['plain_text']);
});
