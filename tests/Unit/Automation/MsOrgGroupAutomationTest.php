<?php

use App\Services\Automation\MsOrgGroupAutomation\MsOrgGroupAutomation;

it('preserves the JSON POST body when the provider endpoint redirects', function () {
    expect(method_exists(MsOrgGroupAutomation::class, 'redirectPostMode'))->toBeTrue()
        ->and(MsOrgGroupAutomation::redirectPostMode())->toBe(CURL_REDIR_POST_ALL);
});
