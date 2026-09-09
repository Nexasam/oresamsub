<?php

use App\Models\FundingOption;
use App\Models\Role;
use App\Models\StandaloneFeature;
use App\Models\StandaloneWebsite;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

function standaloneAdminUser(string $email): User
{
    $role = Role::firstOrCreate(['role_name' => 'Admin']);

    return User::factory()->create(['role_id' => $role->id, 'email' => $email]);
}

function createStandaloneForApi(array $overrides = []): array
{
    $token = 'ors_live_known-token';
    $site = StandaloneWebsite::create(array_merge([
        'slug' => 'mega-sub', 'business_name' => 'Mega Sub Limited',
        'contact_first_name' => 'Mega', 'contact_last_name' => 'Owner',
        'email' => 'owner@megasub.test', 'phone' => '2348012345678',
        'website_url' => 'https://megasub.test', 'bvn' => '22222222222',
        'api_token_digest' => hash('sha256', $token), 'api_token_prefix' => substr($token, 0, 16),
        'api_token_type' => 'operational', 'api_token_must_rotate' => false,
        'webhook_signing_secret' => '', 'webhook_secret_hint' => '', 'status' => 'active',
    ], $overrides));

    return [$site, $token];
}

it('allows only the protected owner to create and manage a standalone in the shared admin UI', function () {
    $owner = standaloneAdminUser('ADEBSHOLEY4REAL@gmail.com');
    $other = standaloneAdminUser('other-admin@example.com');
    $this->actingAs($other)->get('/admin/standalones')->assertForbidden();

    $response = $this->actingAs($owner)->post('/admin/standalones', [
        'business_name' => 'Example Standalone', 'contact_first_name' => 'Ada', 'contact_last_name' => 'Owner',
        'email' => 'ada@example.test', 'phone' => '+234 801 234 5678',
        'website_url' => 'https://example.test', 'bvn' => '22222222222',
    ]);
    $site = StandaloneWebsite::sole();
    $response->assertRedirect(route('admin.standalones.credentials', $site));
    $this->actingAs($owner)->get(route('admin.standalones.credentials', $site))->assertOk()
        ->assertSee('main-content')->assertSee('ors_bootstrap_', false)->assertSee('20 minutes')->assertDontSee('ors_whsec_', false)
        ->assertSee('type="password"', false)->assertSee('Show token');
    $this->actingAs($owner)->get(route('admin.standalones.show', $site))->assertOk()
        ->assertSee('Master wallet')->assertSee('Wallet ledger')->assertDontSee('Webhook secret');

    expect($site->phone)->toBe('2348012345678')->and($site->api_token_type)->toBe('bootstrap')
        ->and($site->api_token_must_rotate)->toBeTrue()
        ->and($site->api_token_expires_at->isBetween(now()->addMinutes(19), now()->addMinutes(21)))->toBeTrue();
});

it('generates a fresh bootstrap token without changing the wallet', function () {
    $owner = standaloneAdminUser('adebsholey4real@gmail.com');
    [$site] = createStandaloneForApi(['master_wallet' => '123.45']);
    $oldDigest = $site->api_token_digest;
    $this->actingAs($owner)->post(route('admin.standalones.rotate-api-token', $site))->assertRedirect(route('admin.standalones.credentials', $site));
    $site->refresh();
    expect($site->api_token_digest)->not->toBe($oldDigest)->and($site->api_token_type)->toBe('bootstrap')
        ->and($site->api_token_must_rotate)->toBeTrue()->and($site->master_wallet)->toBe('123.45');
});

it('provisions only one Kolomoni account using an operational token', function () {
    [$site, $token] = createStandaloneForApi();
    FundingOption::create([
        'funding_option_name' => 'SecureWave', 'slug' => 'securewaveng', 'activation_status' => 1,
        'api_public_key' => 'public-key', 'api_secret_key' => 'secret-key', 'contract_code' => 'business-1',
    ]);
    Http::fake(['securewaveng.com/api/virtual_accounts/generate' => Http::response(['status' => true, 'data' => [[
        'status' => 1, 'account_reference' => 'VA-100', 'account_number' => '1234567890', 'bank_code' => '1',
        'account_bank' => 'Kolomoni', 'account_name' => 'Mega Sub', 'account_email' => 'owner@megasub.test',
    ]]])]);

    $this->withToken($token)->postJson('/api/v1/standalone/virtual-account')->assertCreated()->assertJsonPath('data.bank_code', '1');
    $this->withToken($token)->postJson('/api/v1/standalone/virtual-account')->assertOk();
    Http::assertSentCount(1);
    Http::assertSent(fn (Request $request) => $request['bank_code'] === [1]
        && $request->hasHeader('x-api-key', 'public-key') && $request->hasHeader('Authorization', 'Bearer secret-key'));
    expect($site->fresh()->virtualAccount->account_number)->toBe('1234567890');
});

it('logs missing SecureWave configuration without exposing credentials', function () {
    [$site, $token] = createStandaloneForApi();
    FundingOption::create([
        'funding_option_name' => 'SecureWave', 'slug' => 'securewaveng', 'activation_status' => 1,
        'api_public_key' => 'public-key', 'api_secret_key' => null, 'contract_code' => null,
    ]);
    Log::shouldReceive('warning')->once()->with('Standalone Kolomoni provisioning configuration is incomplete.', Mockery::on(
        fn (array $context): bool => $context['standalone_id'] === $site->id
            && $context['missing_configuration'] === ['api_secret_key', 'contract_code']
            && ! array_key_exists('api_public_key', $context)
            && ! array_key_exists('api_secret_key', $context)
    ));

    $this->withToken($token)->postJson('/api/v1/standalone/virtual-account')
        ->assertStatus(503)->assertJsonPath('message', 'SecureWave account generation is not configured.');
});

it('logs a safe provider response when Kolomoni generation is rejected', function () {
    [$site, $token] = createStandaloneForApi();
    FundingOption::create([
        'funding_option_name' => 'SecureWave', 'slug' => 'securewaveng', 'activation_status' => 1,
        'api_public_key' => 'public-key', 'api_secret_key' => 'secret-key', 'contract_code' => 'business-1',
    ]);
    Http::fake(['securewaveng.com/api/virtual_accounts/generate' => Http::response([
        'status' => false, 'message' => 'BVN validation failed', 'errors' => ['id_number' => ['Invalid BVN']],
    ], 422)]);
    Log::shouldReceive('warning')->once()->with('SecureWave rejected standalone Kolomoni provisioning.', Mockery::on(
        fn (array $context): bool => $context['standalone_id'] === $site->id
            && $context['http_status'] === 422
            && $context['provider_message'] === 'BVN validation failed'
            && $context['provider_errors'] === ['id_number' => ['Invalid BVN']]
            && ! array_key_exists('response', $context)
    ));

    $this->withToken($token)->postJson('/api/v1/standalone/virtual-account')
        ->assertStatus(503)->assertJsonPath('message', 'SecureWave could not generate the Kolomoni account.');
});

it('lets only the protected owner manage features and a standalone global price level', function (): void {
    $owner = standaloneAdminUser('adebsholey4real@gmail.com');
    $other = standaloneAdminUser('other-feature-admin@example.com');
    [$site] = createStandaloneForApi();

    $this->actingAs($other)->get('/admin/standalones/features')->assertForbidden();
    $this->actingAs($owner)->get('/admin/standalones/features')->assertOk()->assertSee('Data provider integration');
    $this->actingAs($owner)->put(route('admin.standalones.price-level', $site), ['price_level' => 3])
        ->assertRedirect();
    expect($site->fresh()->price_level)->toBe(3);

    $this->actingAs($owner)->post(route('admin.standalones.features.store'), [
        'name' => 'WhatsApp bot', 'slug' => 'whatsapp-bot', 'description' => 'Monthly WhatsApp automation.',
        'default_price' => 5000, 'level_1_price' => 4500, 'level_2_price' => 4250,
        'level_3_price' => 4000, 'level_4_price' => 3750, 'is_active' => 1,
    ])->assertRedirect();
    $feature = StandaloneFeature::where('slug', 'whatsapp-bot')->sole();
    expect($feature->billing_type)->toBe('one_time')->and($feature->level_3_price)->toBe('4000.00');
});
