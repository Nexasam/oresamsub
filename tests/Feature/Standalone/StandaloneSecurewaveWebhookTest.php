<?php

use App\Models\FundingOption;
use App\Models\StandaloneFundingEvent;
use App\Models\StandaloneVirtualAccount;
use App\Models\StandaloneWebsite;
use Illuminate\Support\Facades\Http;

it('routes a matched SecureWave payment to a standalone exactly once', function () {
    $site = StandaloneWebsite::create([
        'slug' => 'mega-sub', 'business_name' => 'Mega Sub', 'contact_first_name' => 'Mega', 'contact_last_name' => 'Owner',
        'email' => 'owner@mega.test', 'phone' => '2348012345678', 'website_url' => 'https://mega.test.8.8.8.8.nip.io',
        'callback_url' => 'https://8.8.8.8/hook', 'bvn' => '22222222222', 'api_token_digest' => hash('sha256', 'token'),
        'api_token_prefix' => 'token', 'webhook_signing_secret' => 'secret', 'webhook_secret_hint' => 'sec••••cret', 'status' => 'active',
    ]);
    $option = FundingOption::create(['funding_option_name' => 'SecureWave', 'slug' => 'securewaveng', 'activation_status' => 1, 'api_secret_key' => 'provider-secret']);
    StandaloneVirtualAccount::create(['standalone_website_id' => $site->id, 'funding_option_id' => $option->id, 'account_reference' => 'VA-1', 'account_number' => '1234567890', 'bank_code' => '1', 'bank_name' => 'Kolomoni']);
    Http::fake(['https://8.8.8.8/hook' => Http::response(['ok' => true])]);
    $payload = [
        'transaction_status' => 'success', 'provider_reference' => 'SW-UNIQUE-1', 'amount' => 1000, 'fees' => 10, 'settlement_amount' => 990,
        'currency' => 'NGN', 'paid_at' => '2026-09-08T15:30:00+01:00', 'receiver' => ['bank' => 'Kolomoni', 'account_number' => '1234567890', 'name' => 'Mega Sub'],
        'customer' => ['email' => 'owner@mega.test'],
    ];
    $raw = json_encode($payload);
    $headers = ['X-Signature' => hash_hmac('sha256', $raw, 'provider-secret'), 'Content-Type' => 'application/json'];

    $this->call('POST', '/api/admin/wallets/securewaveng_hook/test', [], [], [], $this->transformHeadersToServerVars($headers), $raw)->assertOk();
    $this->call('POST', '/api/admin/wallets/securewaveng_hook/test', [], [], [], $this->transformHeadersToServerVars($headers), $raw)->assertOk();

    expect(StandaloneFundingEvent::count())->toBe(1);
    $event = StandaloneFundingEvent::sole();
    expect($event->amount_gross)->toBe('1000.00')->and($event->fees)->toBe('10.00')->and($event->amount_settled)->toBe('990.00')->and($event->delivery_status)->toBe('delivered');
    Http::assertSentCount(1);
});
