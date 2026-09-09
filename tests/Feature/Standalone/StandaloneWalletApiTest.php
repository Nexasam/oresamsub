<?php

use App\Models\StandaloneWalletEntry;
use App\Models\StandaloneWebsite;

function walletApiSite(string $token, array $overrides = []): StandaloneWebsite
{
    return StandaloneWebsite::create(array_merge([
        'slug' => 'wallet-site-'.strtolower(str()->random(6)),
        'business_name' => 'Wallet Site', 'contact_first_name' => 'Wallet', 'contact_last_name' => 'Owner',
        'email' => strtolower(str()->random(8)).'@wallet.test', 'phone' => '2348012345678',
        'website_url' => 'https://wallet.test', 'bvn' => '22222222222',
        'webhook_signing_secret' => '', 'webhook_secret_hint' => '',
        'api_token_digest' => hash('sha256', $token), 'api_token_prefix' => substr($token, 0, 16),
        'api_token_type' => 'operational', 'api_token_must_rotate' => false,
        'master_wallet' => '1000.00', 'status' => 'active',
    ], $overrides));
}

it('requires rotation before a bootstrap token can access the wallet', function () {
    $token = 'ors_bootstrap_restricted';
    walletApiSite($token, ['api_token_type' => 'bootstrap', 'api_token_must_rotate' => true, 'api_token_expires_at' => now()->addMinutes(20)]);

    $this->getJson('/api/v1/standalone/wallet')->assertUnauthorized();
    $this->withToken($token)->getJson('/api/v1/standalone/wallet')->assertForbidden()
        ->assertJsonPath('message', 'API token rotation is required before using this endpoint.');
});

it('rotates a bootstrap token once and rejects an expired bootstrap token', function () {
    $bootstrap = 'ors_bootstrap_rotate-me';
    $site = walletApiSite($bootstrap, ['api_token_type' => 'bootstrap', 'api_token_must_rotate' => true, 'api_token_expires_at' => now()->addMinutes(20)]);

    $response = $this->withToken($bootstrap)->postJson('/api/v1/standalone/credentials/api-token/rotate')->assertOk();
    $newToken = $response->json('data.api_token');
    expect($newToken)->toStartWith('ors_live_')->and($site->fresh()->api_token_type)->toBe('operational')
        ->and($site->fresh()->api_token_must_rotate)->toBeFalse()->and($site->fresh()->api_token_expires_at)->toBeNull();
    $this->withToken($bootstrap)->getJson('/api/v1/standalone/wallet')->assertUnauthorized();
    $this->withToken($newToken)->getJson('/api/v1/standalone/wallet')->assertOk()->assertJsonPath('data.available_balance', '1000.00');

    $expired = 'ors_bootstrap_expired';
    walletApiSite($expired, ['api_token_type' => 'bootstrap', 'api_token_must_rotate' => true, 'api_token_expires_at' => now()->subSecond()]);
    $this->withToken($expired)->postJson('/api/v1/standalone/credentials/api-token/rotate')->assertUnauthorized()
        ->assertJsonPath('message', 'This bootstrap API token has expired. Request a new one.');
});

it('deducts exactly once and rejects conflicting reference reuse', function () {
    $token = 'ors_live_wallet-deduction';
    $site = walletApiSite($token);
    $payload = ['amount' => '250.25', 'reference' => 'SHOP-DATA-001', 'purpose' => 'Purchase of MTN data'];

    $this->withToken($token)->postJson('/api/v1/standalone/wallet/deduct', $payload)->assertOk()
        ->assertJsonPath('data.balance_after', '749.75')->assertJsonPath('meta.idempotent_replay', false);
    $this->withToken($token)->postJson('/api/v1/standalone/wallet/deduct', $payload)->assertOk()
        ->assertJsonPath('data.balance_after', '749.75')->assertJsonPath('meta.idempotent_replay', true);
    $this->withToken($token)->postJson('/api/v1/standalone/wallet/deduct', [
        'amount' => '1.00', 'reference' => 'SHOP-DATA-001', 'purpose' => 'Different',
    ])->assertStatus(409);

    expect($site->fresh()->master_wallet)->toBe('749.75')
        ->and(StandaloneWalletEntry::where('standalone_website_id', $site->id)->count())->toBe(1);
});

it('rejects insufficient funds without creating a debit', function () {
    $token = 'ors_live_insufficient';
    $site = walletApiSite($token, ['master_wallet' => '100.00']);
    $this->withToken($token)->postJson('/api/v1/standalone/wallet/deduct', [
        'amount' => '100.01', 'reference' => 'TOO-MUCH', 'purpose' => 'Purchase',
    ])->assertUnprocessable()->assertJsonPath('message', 'Insufficient master wallet balance.');
    expect($site->fresh()->master_wallet)->toBe('100.00')->and($site->walletEntries()->count())->toBe(0);
});

it('lists and reconciles only the authenticated standalone entries', function () {
    $tokenA = 'ors_live_wallet-a';
    $tokenB = 'ors_live_wallet-b';
    walletApiSite($tokenA);
    walletApiSite($tokenB);
    $this->withToken($tokenA)->postJson('/api/v1/standalone/wallet/deduct', [
        'amount' => '20.00', 'reference' => 'TENANT-A-REF', 'purpose' => 'Tenant A purchase',
    ])->assertOk();
    $this->withToken($tokenA)->getJson('/api/v1/standalone/wallet/transactions')->assertOk()->assertJsonCount(1, 'data');
    $this->withToken($tokenA)->getJson('/api/v1/standalone/wallet/transactions/TENANT-A-REF')->assertOk();
    $this->withToken($tokenB)->getJson('/api/v1/standalone/wallet/transactions/TENANT-A-REF')->assertNotFound();
});
