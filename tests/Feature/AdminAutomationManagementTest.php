<?php

use App\Models\Automation;
use App\Models\Role;
use App\Models\User;

function automationAdmin(): User
{
    $role = Role::firstOrCreate(['role_name' => 'Admin']);

    return User::factory()->create(['role_id' => $role->id]);
}

function v2Automation(array $overrides = []): Automation
{
    return Automation::create(array_merge([
        'automation_name' => 'Rosy Telecoms',
        'slug' => 'rosytelecoms',
        'automation_group' => 'v2',
        'api_public_key' => 'old-token',
        'api_secret_key' => 'nil',
        'api_password' => 'nil',
        'endpoint_url' => 'https://old.example/api',
        'domain_url' => 'https://old.example/api',
        'data_url' => 'https://old.example/api/data/',
        'http_verb' => 'POST',
        'network_plans' => ['MTN' => '1', 'GLO' => '2', 'AIRTEL' => '4', '9MOBILE' => '3'],
        'request_params' => [
            ['key' => 'network', 'value' => 'network'],
            ['key' => 'mobile_number', 'value' => 'phone_number'],
        ],
        'request_headers' => [
            ['key' => 'Authorization', 'value' => 'Token old-token'],
        ],
        'success_condition' => [
            ['key' => 'Status', 'value' => 'successful'],
        ],
        'success_response' => 'api_response',
        'failed_response' => 'api_response',
        'success_code' => '200',
        'failure_code' => '400',
        'bank_name' => 'Old Bank',
        'bank_accounts' => '0000000000',
    ], $overrides));
}

it('shows the full edit controls for automations created with the v2 form', function () {
    $admin = automationAdmin();
    $automation = v2Automation();

    $this->actingAs($admin)
        ->get(route('admin.automation.index'))
        ->assertOk()
        ->assertSee('Edit')
        ->assertSee('Edit Automation: Rosy Telecoms')
        ->assertSee('https://old.example/api/data/', false)
        ->assertSee('Token old-token', false)
        ->assertSee('Status', false)
        ->assertSee((string) $automation->id, false);
});

it('updates every v2 automation setting without changing its slug', function () {
    $admin = automationAdmin();
    $automation = v2Automation();

    $payload = [
        'id' => $automation->id,
        'automation_name' => 'Rossy Telecoms Updated',
        'slug' => 'attempted-slug-change',
        'automation_group' => 'v2',
        'api_public_key' => 'new-token',
        'api_secret_key' => 'new-secret',
        'api_password' => 'new-password',
        'endpoint_url' => 'https://rossytechs.com/api',
        'data_url' => 'https://rossytechs.com/api/data/',
        'airtime_url' => 'https://rossytechs.com/api/topup/',
        'cable_url' => null,
        'electricity_url' => null,
        'whatsapp_support_link' => 'https://wa.me/2348000000000',
        'bank_name' => 'New Bank',
        'bank_accounts' => '1111111111,2222222222',
        'http_verb' => 'POST',
        'network_plans' => ['MTN' => '10', 'GLO' => '20', 'AIRTEL' => '40', '9MOBILE' => '30'],
        'request_params' => [
            ['key' => 'network', 'value' => 'network'],
            ['key' => 'mobile_number', 'value' => 'phone_number'],
            ['key' => 'plan', 'value' => 'plan'],
            ['key' => 'Ported_number', 'value' => 'ported_number'],
        ],
        'request_headers' => [
            ['key' => 'Authorization', 'value' => 'Token new-token'],
            ['key' => 'Content-Type', 'value' => 'application/json'],
        ],
        'success_condition' => [
            ['key' => 'Status', 'value' => 'successful'],
        ],
        'success_response' => 'api_response',
        'failed_response' => 'error.message',
        'success_code' => '200',
        'failure_code' => '422',
    ];

    $this->actingAs($admin)
        ->from(route('admin.automation.index'))
        ->post(route('admin.automation.update'), $payload)
        ->assertRedirect(route('admin.automation.index'))
        ->assertSessionHas('success');

    $automation->refresh();

    expect($automation->automation_name)->toBe('Rossy Telecoms Updated')
        ->and($automation->slug)->toBe('rosytelecoms')
        ->and($automation->domain_url)->toBe('https://rossytechs.com/api')
        ->and($automation->data_url)->toBe('https://rossytechs.com/api/data/')
        ->and($automation->network_plans)->toBe($payload['network_plans'])
        ->and($automation->request_params)->toBe($payload['request_params'])
        ->and($automation->request_headers)->toBe($payload['request_headers'])
        ->and($automation->success_condition)->toBe($payload['success_condition'])
        ->and($automation->failed_response)->toBe('error.message')
        ->and($automation->failure_code)->toBe('422')
        ->and($automation->bank_accounts)->toBe('1111111111,2222222222');
});

it('rejects an invalid v2 automation configuration', function () {
    $admin = automationAdmin();
    $automation = v2Automation();

    $this->actingAs($admin)
        ->from(route('admin.automation.index'))
        ->post(route('admin.automation.update'), [
            'id' => $automation->id,
            'automation_name' => 'Rosy Telecoms',
            'automation_group' => 'v2',
            'api_public_key' => 'token',
            'http_verb' => 'DELETE',
            'network_plans' => [],
            'request_params' => [],
            'request_headers' => [],
            'success_condition' => [],
        ])
        ->assertRedirect(route('admin.automation.index'))
        ->assertSessionHasErrors(['http_verb', 'network_plans', 'request_params', 'request_headers', 'success_condition']);
});
