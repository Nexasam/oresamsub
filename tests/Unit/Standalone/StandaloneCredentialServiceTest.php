<?php

use App\Services\Standalone\StandaloneCredentialService;

it('issues expiring bootstrap and non-expiring operational credentials', function () {
    $service = new StandaloneCredentialService;

    $bootstrap = $service->issueBootstrapToken();
    $operational = $service->issueOperationalToken();

    expect($bootstrap['plain_text'])->toStartWith('ors_bootstrap_')
        ->and($bootstrap['digest'])->toBe(hash('sha256', $bootstrap['plain_text']))
        ->and($bootstrap['type'])->toBe('bootstrap')
        ->and($bootstrap['must_rotate'])->toBeTrue()
        ->and($bootstrap['expires_at']->isBetween(now()->addMinutes(19), now()->addMinutes(21)))->toBeTrue()
        ->and($operational['plain_text'])->toStartWith('ors_live_')
        ->and($operational['type'])->toBe('operational')
        ->and($operational['must_rotate'])->toBeFalse()
        ->and($operational['expires_at'])->toBeNull();
});
