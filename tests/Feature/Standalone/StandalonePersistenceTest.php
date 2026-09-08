<?php

use App\Models\FundingOption;
use App\Models\StandaloneFundingEvent;
use App\Models\StandaloneVirtualAccount;
use App\Models\StandaloneWebsite;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Crypt;

function standaloneSiteAttributes(array $overrides = []): array
{
    return array_merge([
        'slug' => 'mega-sub',
        'business_name' => 'Mega Sub Limited',
        'contact_first_name' => 'Mega',
        'contact_last_name' => 'Owner',
        'email' => 'owner@megasub.test',
        'phone' => '2348012345678',
        'website_url' => 'https://megasub.test',
        'callback_url' => 'https://megasub.test/api/oresamsub/funding',
        'bvn' => '22222222222',
        'api_token_digest' => hash('sha256', 'ors_live_test-token'),
        'api_token_prefix' => 'ors_live_test-to',
        'webhook_signing_secret' => 'ors_whsec_test-secret',
        'webhook_secret_hint' => 'ors_whsec_te••••cret',
        'status' => 'active',
    ], $overrides);
}

it('stores sensitive standalone fields encrypted and finds a site by API token', function () {
    $site = StandaloneWebsite::create(standaloneSiteAttributes());

    $raw = DB::table('standalone_websites')->where('id', $site->id)->first();

    expect($raw->bvn)->not->toBe('22222222222')
        ->and(Crypt::decryptString($raw->bvn))->toBe('22222222222')
        ->and($raw->webhook_signing_secret)->not->toBe('ors_whsec_test-secret')
        ->and(StandaloneWebsite::findByApiToken('ors_live_test-token')?->is($site))->toBeTrue()
        ->and(StandaloneWebsite::findByApiToken('wrong-token'))->toBeNull()
        ->and($site->toArray())->not->toHaveKeys(['bvn', 'webhook_signing_secret', 'api_token_digest']);
});

it('relates one virtual account and many funding events to a standalone', function () {
    $site = StandaloneWebsite::create(standaloneSiteAttributes());
    $fundingOption = FundingOption::create([
        'funding_option_name' => 'SecureWave',
        'slug' => 'securewaveng',
        'activation_status' => 1,
    ]);

    StandaloneVirtualAccount::create([
        'standalone_website_id' => $site->id,
        'funding_option_id' => $fundingOption->id,
        'account_reference' => 'VA-001',
        'account_number' => '1234567890',
        'bank_code' => '1',
        'bank_name' => 'Kolomoni',
    ]);

    StandaloneFundingEvent::create([
        'standalone_website_id' => $site->id,
        'event_id' => 'evt_001',
        'provider_reference' => 'provider_001',
        'reference' => 'ORS-FUND-001',
        'amount_gross' => '1000.00',
        'fees' => '10.00',
        'amount_settled' => '990.00',
        'currency' => 'NGN',
        'payment_status' => 'success',
        'delivery_status' => 'pending',
        'callback_payload' => ['event_id' => 'evt_001'],
    ]);

    expect($site->virtualAccount->account_number)->toBe('1234567890')
        ->and($site->fundingEvents)->toHaveCount(1)
        ->and($site->fundingEvents->first()->amount_settled)->toBe('990.00');
});

it('enforces unique standalone provider and account references', function () {
    $site = StandaloneWebsite::create(standaloneSiteAttributes());
    $fundingOption = FundingOption::create([
        'funding_option_name' => 'SecureWave',
        'slug' => 'securewaveng',
        'activation_status' => 1,
    ]);

    StandaloneVirtualAccount::create([
        'standalone_website_id' => $site->id,
        'funding_option_id' => $fundingOption->id,
        'account_reference' => 'VA-UNIQUE',
        'account_number' => '1234567890',
        'bank_code' => '1',
    ]);

    expect(fn () => StandaloneVirtualAccount::create([
        'standalone_website_id' => $site->id,
        'funding_option_id' => $fundingOption->id,
        'account_reference' => 'VA-UNIQUE',
        'account_number' => '9999999999',
        'bank_code' => '1',
    ]))->toThrow(QueryException::class);
});
