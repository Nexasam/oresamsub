<?php

use App\Models\StandaloneFeature;
use App\Models\StandaloneFeaturePurchase;
use App\Models\StandaloneFeatureSubscription;
use App\Models\StandaloneWalletEntry;
use App\Models\StandaloneWebsite;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function standaloneFeatureApiSite(array $overrides = []): array
{
    $token = 'ors_live_feature-token';
    $site = StandaloneWebsite::create(array_merge([
        'slug' => 'feature-sub', 'business_name' => 'Feature Sub', 'contact_first_name' => 'Feature', 'contact_last_name' => 'Owner',
        'email' => 'feature@example.test', 'phone' => '2348012345678', 'website_url' => 'https://feature.test', 'bvn' => '22222222222',
        'api_token_digest' => hash('sha256', $token), 'api_token_prefix' => substr($token, 0, 16), 'api_token_type' => 'operational',
        'api_token_must_rotate' => false, 'webhook_signing_secret' => '', 'webhook_secret_hint' => '', 'status' => 'active',
        'master_wallet' => '50000.00', 'price_level' => 1,
    ], $overrides));

    return [$site, $token];
}

it('lists active features using the authenticated standalone global price level', function (): void {
    [, $token] = standaloneFeatureApiSite(['price_level' => 4]);

    $response = $this->withToken($token)->getJson('/api/v1/standalone/features')->assertOk();
    $response->assertJsonPath('data.0.slug', 'data-provider-integration')
        ->assertJsonPath('data.0.price', '35000.00')->assertJsonPath('data.0.applied_price_level', 'level_4')
        ->assertJsonPath('data.0.billing_type', 'one_time')->assertJsonPath('data.0.status', 'available');
});

it('purchases a one-time feature atomically and replays the same reference without charging twice', function (): void {
    [$site, $token] = standaloneFeatureApiSite();
    $payload = ['reference' => 'feature-order-1001', 'slot_name' => 'FoxDataHub'];

    $this->withToken($token)->postJson('/api/v1/standalone/features/data-provider-integration/purchase', $payload)
        ->assertCreated()->assertJsonPath('data.amount', '38000.00')->assertJsonPath('data.status', 'active')
        ->assertJsonPath('meta.idempotent_replay', false);
    $this->withToken($token)->postJson('/api/v1/standalone/features/data-provider-integration/purchase', $payload)
        ->assertOk()->assertJsonPath('meta.idempotent_replay', true);

    expect($site->fresh()->master_wallet)->toBe('12000.00')
        ->and(StandaloneFeaturePurchase::count())->toBe(1)
        ->and(StandaloneFeatureSubscription::sole()->status)->toBe('active')
        ->and(StandaloneWalletEntry::count())->toBe(1)
        ->and(StandaloneWalletEntry::sole()->amount)->toBe('38000.00');
    $this->withToken($token)->getJson('/api/v1/standalone/features/purchases')->assertOk()
        ->assertJsonPath('data.0.reference', 'feature-order-1001')->assertJsonPath('data.0.feature', 'data-provider-integration')
        ->assertJsonPath('data.0.slot_name', 'FoxDataHub');
});

it('allows unlimited differently named slots but prevents duplicate normalized slot ownership', function (): void {
    [$site, $token] = standaloneFeatureApiSite(['master_wallet' => '120000.00', 'price_level' => null]);

    $this->withToken($token)->postJson('/api/v1/standalone/features/data-provider-integration/purchase', [
        'reference' => 'fox-slot', 'slot_name' => 'FoxDataHub',
    ])->assertCreated();
    $this->withToken($token)->postJson('/api/v1/standalone/features/data-provider-integration/purchase', [
        'reference' => 'affatech-slot', 'slot_name' => 'Affatech',
    ])->assertCreated();
    $this->withToken($token)->postJson('/api/v1/standalone/features/data-provider-integration/purchase', [
        'reference' => 'fox-duplicate', 'slot_name' => 'fox data hub',
    ])->assertStatus(409)->assertJsonPath('message', 'This named feature slot is already active.');

    $this->withToken($token)->getJson('/api/v1/standalone/features/data-provider-integration')->assertOk()
        ->assertJsonCount(2, 'data.purchased_slots')->assertJsonFragment(['slot_name' => 'FoxDataHub'])
        ->assertJsonFragment(['slot_name' => 'Affatech']);
    expect($site->fresh()->master_wallet)->toBe('40000.00')->and(StandaloneFeatureSubscription::count())->toBe(2);
});

it('requires a slot name only for slot-based features', function (): void {
    [, $token] = standaloneFeatureApiSite();

    $this->withToken($token)->postJson('/api/v1/standalone/features/data-provider-integration/purchase', ['reference' => 'missing-slot'])
        ->assertUnprocessable()->assertJsonValidationErrors('slot_name');
});

it('rejects a feature purchase when the master wallet is insufficient', function (): void {
    [$site, $token] = standaloneFeatureApiSite(['master_wallet' => '1000.00']);

    $this->withToken($token)->postJson('/api/v1/standalone/features/data-provider-integration/purchase', ['reference' => 'feature-order-low', 'slot_name' => 'FoxDataHub'])
        ->assertStatus(422)->assertJsonPath('message', 'Insufficient master wallet balance.');

    expect($site->fresh()->master_wallet)->toBe('1000.00')->and(StandaloneFeaturePurchase::count())->toBe(0);
});

it('renews monthly features and uses the current global price level', function (): void {
    [$site, $token] = standaloneFeatureApiSite(['master_wallet' => '1000.00', 'price_level' => 1]);
    $feature = StandaloneFeature::create(['slug' => 'monthly-tools', 'name' => 'Monthly tools', 'billing_type' => 'monthly',
        'default_price' => 100, 'level_1_price' => 90, 'level_2_price' => 80, 'level_3_price' => 70, 'level_4_price' => 60, 'is_active' => true]);
    $this->withToken($token)->postJson('/api/v1/standalone/features/monthly-tools/purchase', ['reference' => 'monthly-start'])->assertCreated();
    $site->update(['price_level' => 4]);
    $subscription = StandaloneFeatureSubscription::where('standalone_feature_id', $feature->id)->sole();
    $this->travelTo($subscription->current_period_ends_at->copy()->addMinute());

    $this->artisan('standalones:renew-features')->assertSuccessful();

    expect($site->fresh()->master_wallet)->toBe('850.00')
        ->and($subscription->fresh()->status)->toBe('active')
        ->and($subscription->fresh()->grace_ends_at)->toBeNull()
        ->and(StandaloneFeaturePurchase::where('billing_event', 'renewal')->sole()->amount)->toBe('60.00')
        ->and(StandaloneFeaturePurchase::where('billing_event', 'renewal')->sole()->applied_price_level)->toBe('level_4');
});

it('gives monthly subscriptions seven days grace then suspends and restores after funding', function (): void {
    [$site, $token] = standaloneFeatureApiSite(['master_wallet' => '100.00', 'price_level' => null]);
    StandaloneFeature::create(['slug' => 'monthly-bot', 'name' => 'Monthly bot', 'billing_type' => 'monthly',
        'default_price' => 100, 'level_1_price' => 90, 'level_2_price' => 80, 'level_3_price' => 70, 'level_4_price' => 60, 'is_active' => true]);
    $this->withToken($token)->postJson('/api/v1/standalone/features/monthly-bot/purchase', ['reference' => 'bot-start'])->assertCreated();
    $subscription = StandaloneFeatureSubscription::sole();
    $this->travelTo($subscription->current_period_ends_at->copy()->addMinute());
    $this->artisan('standalones:renew-features')->assertSuccessful();
    expect($subscription->fresh()->status)->toBe('past_due')->and(now()->diffInDays($subscription->fresh()->grace_ends_at))->toBe(7.0);

    $this->travel(8)->days();
    $this->artisan('standalones:renew-features')->assertSuccessful();
    expect($subscription->fresh()->status)->toBe('suspended');

    $site->refresh()->update(['master_wallet' => '100.00']);
    $this->artisan('standalones:renew-features')->assertSuccessful();
    expect($subscription->fresh()->status)->toBe('active')->and($site->fresh()->master_wallet)->toBe('0.00');
});

it('cancels a monthly feature at the end of its paid period', function (): void {
    [, $token] = standaloneFeatureApiSite(['master_wallet' => '1000.00']);
    StandaloneFeature::create(['slug' => 'monthly-reports', 'name' => 'Monthly reports', 'billing_type' => 'monthly',
        'default_price' => 100, 'level_1_price' => 90, 'level_2_price' => 80, 'level_3_price' => 70, 'level_4_price' => 60, 'is_active' => true]);
    $this->withToken($token)->postJson('/api/v1/standalone/features/monthly-reports/purchase', ['reference' => 'reports-start'])->assertCreated();
    $this->withToken($token)->postJson('/api/v1/standalone/features/monthly-reports/cancel')->assertOk()
        ->assertJsonPath('data.cancel_at_period_end', true);
    $subscription = StandaloneFeatureSubscription::sole();
    $this->travelTo($subscription->current_period_ends_at->copy()->addMinute());
    $this->artisan('standalones:renew-features')->assertSuccessful();

    expect($subscription->fresh()->status)->toBe('cancelled')->and($subscription->fresh()->cancelled_at)->not->toBeNull()
        ->and(StandaloneFeaturePurchase::where('billing_event', 'renewal')->count())->toBe(0);
});

it('charges separate setup and monthly prices for a hybrid feature', function (): void {
    [$site, $token] = standaloneFeatureApiSite(['master_wallet' => '1000.00', 'price_level' => 2]);
    StandaloneFeature::create(['slug' => 'hybrid-tool', 'name' => 'Hybrid tool', 'billing_type' => 'one_time_plus_monthly',
        'default_price' => 500, 'level_1_price' => 450, 'level_2_price' => 400, 'level_3_price' => 350, 'level_4_price' => 300,
        'default_monthly_price' => 100, 'level_1_monthly_price' => 90, 'level_2_monthly_price' => 80,
        'level_3_monthly_price' => 70, 'level_4_monthly_price' => 60, 'is_active' => true]);

    $this->withToken($token)->postJson('/api/v1/standalone/features/hybrid-tool/purchase', ['reference' => 'hybrid-start'])
        ->assertCreated()->assertJsonPath('data.amount', '400.00');
    $subscription = StandaloneFeatureSubscription::sole();
    $this->travelTo($subscription->current_period_ends_at->copy()->addMinute());
    $this->artisan('standalones:renew-features')->assertSuccessful();

    expect($site->fresh()->master_wallet)->toBe('520.00')
        ->and(StandaloneFeaturePurchase::where('billing_event', 'renewal')->sole()->amount)->toBe('80.00');
});

it('activates a free feature without debiting the wallet', function (): void {
    [$site, $token] = standaloneFeatureApiSite();
    StandaloneFeature::create(['slug' => 'free-tool', 'name' => 'Free tool', 'billing_type' => 'free',
        'default_price' => 999, 'level_1_price' => 999, 'level_2_price' => 999, 'level_3_price' => 999, 'level_4_price' => 999, 'is_active' => true]);

    $this->withToken($token)->postJson('/api/v1/standalone/features/free-tool/purchase', ['reference' => 'free-start'])
        ->assertCreated()->assertJsonPath('data.amount', '0.00');

    expect($site->fresh()->master_wallet)->toBe('50000.00')->and(StandaloneWalletEntry::count())->toBe(0);
});
