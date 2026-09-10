<?php

use App\Services\Automation\DataAutomation;

it('always sends Rossy ported number with the exact required key', function () {
    $provider = (object) [
        'slug' => 'custom-provider-name',
        'data_url' => 'https://rossytechs.com/api/data/',
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

it('always sends JSON content headers to data providers', function () {
    $provider = (object) [
        'request_headers' => [
            ['key' => 'Authorization', 'value' => 'Token provider-key'],
            ['key' => 'Content-Type', 'value' => 'text/plain'],
            ['key' => 'Accept', 'value' => '*/*'],
        ],
    ];

    expect(method_exists(DataAutomation::class, 'buildRequestHeaders'))->toBeTrue()
        ->and((new DataAutomation())->buildRequestHeaders($provider))->toContain(
            'Authorization: Token provider-key',
            'Content-Type: application/json',
            'Accept: application/json',
        )->not->toContain('Content-Type: text/plain', 'Accept: */*');
});
