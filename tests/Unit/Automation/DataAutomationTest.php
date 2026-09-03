<?php

use App\Services\Automation\DataAutomation;

it('always sends Rossy ported number with the exact required key', function () {
    $provider = (object) [
        'slug' => 'rosytelecoms',
        'request_params' => [
            ['key' => 'network', 'value' => 'network'],
            ['key' => 'mobile_number', 'value' => 'phone_number'],
            ['key' => 'plan', 'value' => 'plan'],
        ],
    ];

    $payload = (new DataAutomation())->buildRequestParameters(
        $provider,
        '09037346247',
        '1',
        '42',
        true,
        'ref-123',
    );

    expect($payload)->toBe([
        'network' => '1',
        'mobile_number' => '09037346247',
        'plan' => '42',
        'Ported_number' => true,
    ]);
});
