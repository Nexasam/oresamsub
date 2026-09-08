<?php

use App\Models\FundingOption;
use App\Models\Role;
use App\Models\StandaloneFundingEvent;
use App\Models\StandaloneWebsite;
use App\Models\User;
use App\Services\Standalone\StandaloneFundingCallbackService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

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
        'api_token_digest' => hash('sha256', $token),
        'api_token_prefix' => substr($token, 0, 16),
        'webhook_signing_secret' => 'ors_whsec_test-secret',
        'webhook_secret_hint' => 'ors_whsec_te••••cret', 'status' => 'active',
        'callback_url' => null,
    ], $overrides));

    return [$site, $token];
}

it('allows only the protected owner to create and manage a standalone', function () {
    $owner = standaloneAdminUser('ADEBSHOLEY4REAL@gmail.com');
    $other = standaloneAdminUser('other-admin@example.com');

    $this->actingAs($other)->get('/admin/standalones')->assertForbidden();

    $response = $this->actingAs($owner)->post('/admin/standalones', [
        'business_name' => 'Example Standalone',
        'contact_first_name' => 'Ada',
        'contact_last_name' => 'Owner',
        'email' => 'ada@example.test',
        'phone' => '+234 801 234 5678',
        'website_url' => 'https://example.test',
        'bvn' => '22222222222',
    ]);

    $site = StandaloneWebsite::sole();
    $response->assertRedirect(route('admin.standalones.credentials', $site));
    $this->actingAs($owner)->get(route('admin.standalones.credentials', $site))
        ->assertOk()->assertSee('ors_live_', false)->assertSee('ors_whsec_', false);
    $this->actingAs($owner)->get(route('admin.standalones.show', $site))
        ->assertOk()->assertSee('Example Standalone')->assertDontSee('ors_live_known-token');
    expect($site->phone)->toBe('2348012345678');
});

it('authenticates standalone API requests and safely configures callbacks', function () {
    [$site, $token] = createStandaloneForApi();

    $this->getJson('/api/v1/standalone/callback')->assertUnauthorized();
    $this->withToken('wrong')->getJson('/api/v1/standalone/callback')->assertUnauthorized();
    $this->withToken($token)->putJson('/api/v1/standalone/callback', ['callback_url' => 'http://8.8.8.8/hook'])->assertUnprocessable();
    $this->withToken($token)->putJson('/api/v1/standalone/callback', ['callback_url' => 'https://127.0.0.1/hook'])->assertUnprocessable();
    $this->withToken($token)->putJson('/api/v1/standalone/callback', ['callback_url' => 'https://8.8.8.8/hook'])
        ->assertOk()->assertJsonPath('data.callback_url', 'https://8.8.8.8/hook');

    $site->update(['status' => 'suspended']);
    $this->withToken($token)->getJson('/api/v1/standalone/callback')->assertForbidden();
});

it('provisions only one Kolomoni account using SecureWave settings', function () {
    [$site, $token] = createStandaloneForApi();
    FundingOption::create([
        'funding_option_name' => 'SecureWave', 'slug' => 'securewaveng', 'activation_status' => 1,
        'api_public_key' => 'public-key', 'api_secret_key' => 'secret-key', 'contract_code' => 'business-1',
    ]);
    Http::fake(['securewaveng.com/api/virtual_accounts/generate' => Http::response([
        'status' => true,
        'data' => [[
            'status' => 1, 'account_reference' => 'VA-100', 'account_number' => '1234567890',
            'bank_code' => '1', 'account_bank' => 'Kolomoni', 'account_name' => 'Mega Sub',
            'account_email' => 'owner@megasub.test',
        ]],
    ])]);

    $this->withToken($token)->postJson('/api/v1/standalone/virtual-account')
        ->assertCreated()->assertJsonPath('data.bank_code', '1');
    $this->withToken($token)->postJson('/api/v1/standalone/virtual-account')->assertOk();

    Http::assertSentCount(1);
    Http::assertSent(fn (Request $request) => $request['bank_code'] === [1]
        && $request->hasHeader('x-api-key', 'public-key')
        && $request->hasHeader('Authorization', 'Bearer secret-key'));
    expect($site->fresh()->virtualAccount->account_number)->toBe('1234567890');
});

it('signs and records immediate funding callback attempts', function () {
    [$site] = createStandaloneForApi(['callback_url' => 'https://8.8.8.8/hook']);
    $event = StandaloneFundingEvent::create([
        'standalone_website_id' => $site->id, 'event_id' => 'evt_100', 'provider_reference' => 'provider_100',
        'reference' => 'ORS-FUND-100', 'amount_gross' => '1000.00', 'fees' => '10.00',
        'amount_settled' => '990.00', 'currency' => 'NGN', 'payment_status' => 'success',
        'callback_url' => 'https://8.8.8.8/hook', 'callback_payload' => [
            'version' => '1.0', 'event' => 'master_wallet.funded', 'event_id' => 'evt_100',
            'amount_settled' => '990.00',
        ], 'delivery_status' => 'pending',
    ]);
    Http::fake(['https://8.8.8.8/hook' => Http::response(['ok' => true], 200)]);

    app(StandaloneFundingCallbackService::class)->deliver($event);

    $event->refresh();
    expect($event->delivery_status)->toBe('delivered')->and($event->attempt_count)->toBe(1);
    Http::assertSent(fn (Request $request) => $request->hasHeader('X-Oresamsub-Event-ID', 'evt_100')
        && filled($request->header('X-Oresamsub-Signature')[0] ?? null));
});

it('lets only the protected owner resend a standalone funding event', function () {
    $owner = standaloneAdminUser('adebsholey4real@gmail.com');
    $other = standaloneAdminUser('other-resender@example.com');
    [$site] = createStandaloneForApi(['callback_url' => 'https://8.8.8.8/hook']);
    $event = StandaloneFundingEvent::create([
        'standalone_website_id' => $site->id, 'event_id' => 'evt_retry', 'provider_reference' => 'provider_retry', 'reference' => 'ORS-FUND-RETRY',
        'amount_gross' => '100.00', 'fees' => '1.00', 'amount_settled' => '99.00', 'currency' => 'NGN', 'payment_status' => 'success',
        'callback_url' => 'https://8.8.8.8/hook', 'callback_payload' => ['event_id' => 'evt_retry'], 'delivery_status' => 'failed', 'attempt_count' => 1,
    ]);
    Http::fake(['https://8.8.8.8/hook' => Http::response([], 204)]);
    $this->actingAs($other)->post(route('admin.standalones.funding-events.resend', [$site, $event]))->assertForbidden();
    $this->actingAs($owner)->post(route('admin.standalones.funding-events.resend', [$site, $event]))->assertRedirect();
    expect($event->fresh()->delivery_status)->toBe('delivered')->and($event->fresh()->attempt_count)->toBe(2);
});
