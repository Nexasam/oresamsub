<?php

use App\Models\Automation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

function telecomAbodeAdmin(): User
{
    $role = Role::firstOrCreate(['role_name' => 'Admin']);

    return User::factory()->create(['role_id' => $role->id]);
}

function telecomAbodeAutomation(array $overrides = []): Automation
{
    return Automation::create(array_merge([
        'automation_name' => 'Telecom Abode Production',
        'slug' => 'telecom-abode',
        'api_public_key' => 'production-token',
        'api_secret_key' => 'nil',
        'api_password' => 'nil',
        'endpoint_url' => 'https://telecomabode.com.ng/api',
        'domain_url' => 'https://telecomabode.com.ng/api',
        'data_url' => 'https://telecomabode.com.ng/api/data/',
    ], $overrides));
}

it('shows Telecom Abode plans using the matching automation production key', function () {
    $admin = telecomAbodeAdmin();

    telecomAbodeAutomation();

    Http::fake([
        'telecomabode.com.ng/api/data/data_plans' => Http::response([
            'status' => 'success',
            'data' => [
                ['id' => 17, 'network' => 'MTN', 'name' => '1GB Monthly', 'price' => 500],
            ],
        ]),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.telecom-abode.plans'))
        ->assertOk()
        ->assertSee('Telecom Abode Data Plans')
        ->assertSee('1GB Monthly')
        ->assertSee('MTN')
        ->assertDontSee('production-token');

    Http::assertSent(fn (Request $request): bool =>
        $request->url() === 'https://telecomabode.com.ng/api/data/data_plans'
        && $request->hasHeader('Authorization', 'Token production-token')
    );
});

it('shows a safe error when no Telecom Abode production key is configured', function () {
    $admin = telecomAbodeAdmin();

    $this->actingAs($admin)
        ->get(route('admin.telecom-abode.plans'))
        ->assertServiceUnavailable()
        ->assertSee('Telecom Abode API key is not configured');
});

it('shows a safe error when Telecom Abode cannot return plans', function () {
    $admin = telecomAbodeAdmin();

    telecomAbodeAutomation([
        'automation_name' => 'TelecomAbode',
        'slug' => 'telecomabode-production',
    ]);

    Http::fake([
        'telecomabode.com.ng/api/data/data_plans' => Http::response(['detail' => 'Unauthorized'], 401),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.telecom-abode.plans'))
        ->assertStatus(502)
        ->assertSee('Telecom Abode could not return the plans')
        ->assertDontSee('production-token');
});
