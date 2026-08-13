<?php

use App\Services\Automation\AutomationLogic;

it('preserves an explicit data provider failure', function () {
    putenv('APP_NAME=OresamSub');
    $_ENV['APP_NAME'] = 'OresamSub';
    $_SERVER['APP_NAME'] = 'OresamSub';

    $automation = (object) [
        'id' => 'automation-id',
        'slug' => 'unsupported-provider',
        'automation_group' => 'legacy',
        'api_public_key' => 'public-key',
        'api_secret_key' => 'secret-key',
        'data_url' => 'https://provider.test/data',
        'automation_product_plan_id' => 'provider-plan',
    ];

    $result = AutomationLogic::initiateDataPurchase([
        'phone_number' => '123',
        'automation_details' => $automation,
        'network_id' => 'network-id',
        'plan_id' => 'plan-id',
        'validatephonenetwork' => 0,
    ]);

    expect($result['status'])->toBe(-1)
        ->and($result['set_for_manual'] ?? 0)->toBe(0)
        ->and($result['user_message'])->toBe('This number is not a valid number: 123');
});
