<?php

use App\Models\Automation;
use App\Models\AutomationProductPlan;
use App\Models\Network;
use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\ProductPlanCategory;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ProductPlans\AirtelGiftingPlanImportService;

function airtelGiftingBaseEntities(): array
{
    $network = Network::create([
        'network_name' => 'AIRTEL',
        'api_id' => 3,
        'visibility' => '1',
    ]);

    $product = Product::create([
        'slug' => 'data',
        'product_name' => 'Data',
        'visibility' => '1',
        'active_status' => '1',
    ]);

    $automation = Automation::create([
        'automation_name' => 'ORESAMPLUG AUTOMATION',
        'slug' => 'oresamplug',
        'domain_url' => 'https://oresamplug.test',
        'activation_status' => '1',
    ]);

    return compact('network', 'product', 'automation');
}

it('previews the Airtel gifting import without writing rows', function () {
    airtelGiftingBaseEntities();

    $this->artisan('plans:import-airtel-gifting')
        ->assertSuccessful()
        ->expectsOutputToContain('Airtel gifting import preview')
        ->expectsOutputToContain('3.2GB AIRTEL CG (7 DAYS)')
        ->expectsOutputToContain('6GB AIRTEL CG (2 DAYS)')
        ->expectsOutputToContain('Preview only');

    expect(ProductPlanCategory::query()->count())->toBe(0)
        ->and(ProductPlan::query()->count())->toBe(0)
        ->and(AutomationProductPlan::query()->count())->toBe(0);
});

it('imports Airtel gifting plans and provider route rows idempotently', function () {
    airtelGiftingBaseEntities();

    $this->artisan('plans:import-airtel-gifting', ['--execute' => true])
        ->assertSuccessful()
        ->expectsOutputToContain('Created: 38');

    $category = ProductPlanCategory::query()
        ->where('product_plan_category_name', 'Airtel Gifting')
        ->first();

    expect($category)->not->toBeNull()
        ->and(ProductPlan::query()->count())->toBe(38)
        ->and(AutomationProductPlan::query()->count())->toBe(38)
        ->and(ProductPlan::query()->where('data_size_in_mb', '230')->exists())->toBeFalse();

    $daily75 = ProductPlan::query()
        ->where('automation_product_plan_id', '225')
        ->firstOrFail();

    expect($daily75->api_id)->toBeNull()
        ->and((float) $daily75->cost_price)->toBe(70.5)
        ->and((int) $daily75->data_size_in_mb)->toBe(75)
        ->and((int) $daily75->validity_in_days)->toBe(1)
        ->and((float) $daily75->user_level_1_selling_price)->toBe(75.0)
        ->and((float) $daily75->user_level_4_selling_price)->toBe(75.0)
        ->and((float) $daily75->user_level_7_selling_price)->toBe(75.0);

    expect(AutomationProductPlan::query()
        ->where('product_plan_id', $daily75->id)
        ->where('provider_plan_id', '225')
        ->where('cost_price', 70.5)
        ->exists())->toBeTrue();

    $this->artisan('plans:import-airtel-gifting', ['--execute' => true])
        ->assertSuccessful()
        ->expectsOutputToContain('Created: 0');

    expect(ProductPlan::query()->count())->toBe(38)
        ->and(AutomationProductPlan::query()->count())->toBe(38);
});

it('previews and applies pasted Airtel gifting plan text', function () {
    airtelGiftingBaseEntities();

    $text = <<<'TEXT'
Public ID | Plan Name | Airtel API Code | Data Size | Validity | Original Price
225 | Daily Plan 75 | Daily_Plan_75 | 75 MB | 1 day | 75
232 | Binge 500 | Binge_500 | 1 GB | 1 day | 500
TEXT;

    $service = app(AirtelGiftingPlanImportService::class);

    $preview = $service->preview($text);

    expect($preview['summary']['total'])->toBe(2)
        ->and($preview['summary']['new'])->toBe(2)
        ->and($preview['rows'][0]['public_id'])->toBe('225')
        ->and($preview['rows'][0]['code'])->toBe('Daily_Plan_75')
        ->and($preview['rows'][0]['size_mb'])->toBe(75)
        ->and($preview['rows'][1]['size_mb'])->toBe(1000);

    $result = $service->execute($text);

    expect($result['summary']['created'])->toBe(2)
        ->and(ProductPlan::query()->count())->toBe(2)
        ->and(AutomationProductPlan::query()->count())->toBe(2);

    $secondPreview = $service->preview($text);

    expect($secondPreview['summary']['existing'])->toBe(2);
});

it('uses configurable discount percentages per plan level in preview', function () {
    airtelGiftingBaseEntities();

    $service = app(AirtelGiftingPlanImportService::class);

    $preview = $service->preview(
        '225 | Daily Plan 75 | Daily_Plan_75 | 75 MB | 1 day | 100',
        [
            'level_discount_percent_1' => 0.5,
            'level_discount_percent_2' => 1,
            'level_discount_percent_3' => 1.5,
            'level_discount_percent_4' => 2,
            'level_discount_percent_5' => 2.5,
            'level_discount_percent_6' => 3,
            'level_discount_percent_7' => 3.5,
            'airtime_purchase_rate_per_100' => 94,
        ]
    );

    $row = $preview['rows'][0];

    expect($row['level_prices'][1])->toBe(100.0)
        ->and($row['level_prices'][2])->toBe(99.0)
        ->and($row['level_prices'][3])->toBe(99.0)
        ->and($row['level_prices'][4])->toBe(98.0)
        ->and($row['level_prices'][5])->toBe(98.0)
        ->and($row['level_prices'][6])->toBe(97.0)
        ->and($row['level_prices'][7])->toBe(97.0)
        ->and($row['airtime_purchase_rate_per_100'])->toBe(94.0)
        ->and($row['effective_airtime_cost'])->toBe(94.0)
        ->and($row['profit_by_level'][1])->toBe(6.0)
        ->and($row['profit_by_level'][4])->toBe(4.0)
        ->and($row['profit_by_level'][7])->toBe(3.0);
});

it('can target an explicitly selected product network category and automation', function () {
    $base = airtelGiftingBaseEntities();

    $category = ProductPlanCategory::create([
        'product_plan_category_name' => 'Airtel Direct Gift Plans',
        'automation_id' => $base['automation']->id,
        'product_id' => $base['product']->id,
        'network_id' => $base['network']->id,
        'visibility' => '1',
        'is_hot_sales' => '0',
    ]);

    $service = app(AirtelGiftingPlanImportService::class);

    $result = $service->execute(
        '225 | Daily Plan 75 | Daily_Plan_75 | 75 MB | 1 day | 75',
        [
            'network_id' => $base['network']->id,
            'product_id' => $base['product']->id,
            'category_id' => $category->id,
            'automation_id' => $base['automation']->id,
        ]
    );

    $plan = ProductPlan::query()->firstOrFail();

    expect($result['summary']['created'])->toBe(1)
        ->and($plan->product_plan_category_id)->toBe($category->id)
        ->and($plan->automation_id)->toBe($base['automation']->id)
        ->and(AutomationProductPlan::query()->where('product_plan_id', $plan->id)->where('automation_id', $base['automation']->id)->exists())->toBeTrue();
});

it('normalizes Airtel gifting names and MB size, preserves edited level prices, and locks public id 239 in preview', function () {
    airtelGiftingBaseEntities();

    $service = app(AirtelGiftingPlanImportService::class);

    $preview = $service->preview(
        '239 | Any old name | Weekly_Plan_800 | 1 GB | 7 days | 800',
        [
            'airtime_purchase_rate_per_100' => 94,
        ]
    );

    $row = $preview['rows'][0];

    expect($row['name'])->toBe('1GB AIRTEL CG (7 DAYS)')
        ->and($row['size'])->toBe('1000')
        ->and($row['size_mb'])->toBe(1000)
        ->and($row['is_editable'])->toBeFalse();

    $editablePreview = $service->preview('240 | Any old name | Weekly_Plan_1000 | 1500 | 7 days | 1000');
    $editableRow = $editablePreview['rows'][0];

    $editedText = $service->rowsToText([array_merge($editableRow, [
        'level_price_1' => 799,
        'level_price_2' => 798,
        'level_price_3' => 797,
        'level_price_4' => 796,
        'level_price_5' => 795,
        'level_price_6' => 794,
        'level_price_7' => 793,
    ])]);

    $result = $service->execute($editedText);
    $plan = ProductPlan::query()->firstOrFail();

    expect($result['rows'][0]['level_prices'][1])->toBe(799.0)
        ->and($result['rows'][0]['level_prices'][7])->toBe(793.0)
        ->and($plan->product_plan_name)->toBe('1.5GB AIRTEL CG (7 DAYS)')
        ->and((int) $plan->data_size_in_mb)->toBe(1500)
        ->and((float) $plan->cost_price)->toBe(940.0)
        ->and((float) $plan->user_level_1_selling_price)->toBe(799.0)
        ->and((float) $plan->user_level_7_selling_price)->toBe(793.0);
});

it('previews and deletes stale Airtel plans while excluding new imported public ids and recent purchases', function () {
    $base = airtelGiftingBaseEntities();

    $category = ProductPlanCategory::create([
        'product_plan_category_name' => 'Old Airtel Plans',
        'automation_id' => $base['automation']->id,
        'product_id' => $base['product']->id,
        'network_id' => $base['network']->id,
        'visibility' => '1',
        'is_hot_sales' => '0',
    ]);

    $oldStale = ProductPlan::create([
        'product_plan_name' => 'Old Airtel stale plan',
        'product_plan_category_id' => $category->id,
        'automation_id' => $base['automation']->id,
        'automation_product_plan_id' => 'OLD_AIRTEL_1',
        'cost_price' => '100.00',
        'data_size_in_mb' => '1000',
        'validity_in_days' => '30',
        'default_selling_price' => '110.00',
        'visibility' => '1',
        'public_visibility' => '1',
        'active_status' => '1',
    ]);

    $recent = ProductPlan::create([
        'product_plan_name' => 'Old Airtel recent plan',
        'product_plan_category_id' => $category->id,
        'automation_id' => $base['automation']->id,
        'automation_product_plan_id' => 'OLD_AIRTEL_RECENT',
        'cost_price' => '100.00',
        'data_size_in_mb' => '2000',
        'validity_in_days' => '30',
        'default_selling_price' => '110.00',
        'visibility' => '1',
        'public_visibility' => '1',
        'active_status' => '1',
    ]);

    $newPublicIdPlan = ProductPlan::create([
        'product_plan_name' => 'New Airtel import plan',
        'product_plan_category_id' => $category->id,
        'automation_id' => $base['automation']->id,
        'automation_product_plan_id' => '225',
        'cost_price' => '70.50',
        'data_size_in_mb' => '75',
        'validity_in_days' => '1',
        'default_selling_price' => '74.63',
        'visibility' => '1',
        'public_visibility' => '1',
        'active_status' => '1',
    ]);

    $user = User::factory()->create();
    Transaction::create([
        'user_id' => $user->id,
        'product_plan_id' => $recent->id,
        'automation_id' => $base['automation']->id,
        'transaction_category' => 'data',
        'status' => '1',
        'wallet_category' => 'MAIN',
        'phone_number' => '08000000000',
        'amount' => '110',
        'balance_before' => '1000',
        'balance_after' => '890',
        'description' => 'recent purchase',
        'created_at' => now()->subMonth(),
        'updated_at' => now()->subMonth(),
    ]);

    $service = app(AirtelGiftingPlanImportService::class);
    $stale = $service->staleAirtelPlans([
        'network_id' => $base['network']->id,
        'product_id' => $base['product']->id,
    ]);

    expect($stale['rows'])->toHaveCount(1)
        ->and($stale['rows'][0]['id'])->toBe($oldStale->id);

    $deleted = $service->deleteStaleAirtelPlans([$oldStale->id, $recent->id, $newPublicIdPlan->id], [
        'network_id' => $base['network']->id,
        'product_id' => $base['product']->id,
    ]);

    expect($deleted['deleted'])->toBe(1)
        ->and(ProductPlan::query()->whereKey($oldStale->id)->exists())->toBeFalse()
        ->and(ProductPlan::query()->whereKey($recent->id)->exists())->toBeTrue()
        ->and(ProductPlan::query()->whereKey($newPublicIdPlan->id)->exists())->toBeTrue();
});
