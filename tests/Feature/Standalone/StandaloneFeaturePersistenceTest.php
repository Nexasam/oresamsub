<?php

use App\Models\StandaloneFeature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('provides the standalone feature catalogue and subscription schema', function (): void {
    expect(Schema::hasColumns('standalone_websites', [
        'price_level',
    ]))->toBeTrue()
        ->and(Schema::hasColumns('standalone_features', [
            'slug', 'name', 'billing_type', 'default_price', 'level_1_price', 'level_2_price',
            'level_3_price', 'level_4_price', 'default_monthly_price', 'level_1_monthly_price',
            'level_2_monthly_price', 'level_3_monthly_price', 'level_4_monthly_price', 'is_active', 'sort_order',
        ]))->toBeTrue()
        ->and(Schema::hasColumns('standalone_feature_subscriptions', [
            'standalone_website_id', 'standalone_feature_id', 'status', 'current_period_starts_at',
            'current_period_ends_at', 'grace_ends_at', 'cancel_at_period_end', 'cancelled_at',
        ]))->toBeTrue()
        ->and(Schema::hasColumns('standalone_feature_purchases', [
            'standalone_website_id', 'standalone_feature_id', 'standalone_wallet_entry_id',
            'transaction_id', 'client_reference', 'billing_event', 'amount', 'applied_price_level',
            'period_starts_at', 'period_ends_at',
        ]))->toBeTrue();
});

it('uses the standalone global level and falls back to the default feature price', function (): void {
    $feature = StandaloneFeature::where('slug', 'data-provider-integration')->sole();

    expect($feature->priceFor(null))->toBe('40000.00')
        ->and($feature->priceFor(1))->toBe('38000.00')
        ->and($feature->priceFor(2))->toBe('37000.00')
        ->and($feature->priceFor(3))->toBe('36000.00')
        ->and($feature->priceFor(4))->toBe('35000.00');
});
