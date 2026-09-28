<?php

use App\Http\Services\DataPlansService;
use App\Models\Automation;
use App\Models\AutomationProductPlan;
use App\Models\Network;
use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\ProductPlanCategory;
use App\Models\ProductPlanCustomPricing;
use App\Models\User;
use App\Models\UserPlan;

function dataPricingFixture(float $customPrice, int $seed): array
{
    $userPlan = UserPlan::create([
        'user_plan_name' => 'Elite '.$seed,
        'updated_user_plan_name' => 'Elite '.$seed,
        'plan_level' => 7,
        'is_default' => 0,
        'visibility' => 1,
    ]);

    $user = User::factory()->create(['user_plan_id' => $userPlan->id]);
    $network = Network::create(['network_name' => 'GLO '.$seed, 'api_id' => 10_000 + $seed]);
    $product = Product::create(['slug' => 'data', 'product_name' => 'DATA '.$seed]);
    $automation = Automation::create([
        'automation_name' => 'GongozConcept '.$seed,
        'slug' => 'gongozconcept-'.$seed,
        'automation_group' => 'test',
        'domain_url' => 'https://example.com',
        'data_url' => 'https://example.com/data',
        'airtime_url' => 'https://example.com/airtime',
        'cable_url' => 'https://example.com/cable',
        'electricity_url' => 'https://example.com/electricity',
    ]);
    $category = ProductPlanCategory::create([
        'product_plan_category_name' => 'GLO CG DATA '.$seed,
        'automation_id' => $automation->id,
        'product_id' => $product->id,
        'network_id' => $network->id,
    ]);

    $plan = ProductPlan::create([
        'product_plan_name' => '1GB GLO CG (7 DAYS)',
        'product_plan_category_id' => $category->id,
        'automation_product_plan_id' => (string) (538 + $seed),
        'automation_id' => $automation->id,
        'cost_price' => 352,
        'data_size_in_mb' => 1000,
        'validity_in_days' => 7,
        'default_selling_price' => 390,
        'user_level_1_selling_price' => 420,
        'user_level_2_selling_price' => 410,
        'user_level_3_selling_price' => 400,
        'user_level_4_selling_price' => 390,
        'user_level_5_selling_price' => 380,
        'user_level_6_selling_price' => 370,
        'user_level_7_selling_price' => 360,
    ]);

    AutomationProductPlan::create([
        'product_plan_id' => $plan->id,
        'automation_id' => $automation->id,
        'provider_plan_id' => (string) (538 + $seed),
        'cost_price' => 352,
        'is_active' => true,
    ]);

    ProductPlanCustomPricing::create([
        'product_plan_id' => $plan->id,
        'user_id' => $user->id,
        'price' => $customPrice,
        'status' => 1,
        'added_by' => $user->id,
    ]);

    return compact('user', 'network', 'product', 'plan');
}

it('does not allow data custom pricing below cost plus minimum profit', function () {
    ['user' => $user, 'network' => $network, 'product' => $product, 'plan' => $plan] = dataPricingFixture(350, 1);

    $price = (new DataPlansService())->get_customer_price_per_plan([
        'product_id' => $product->id,
        'network_id' => $network->id,
        'user' => $user,
        'plan_details' => $plan,
    ])['message'];

    expect((float) $price)->toBe(355.0);
});

it('keeps a safe custom data price when it is above the minimum floor', function () {
    ['user' => $user, 'network' => $network, 'product' => $product, 'plan' => $plan] = dataPricingFixture(365, 2);

    $price = (new DataPlansService())->get_customer_price_per_plan([
        'product_id' => $product->id,
        'network_id' => $network->id,
        'user' => $user,
        'plan_details' => $plan,
    ])['message'];

    expect((float) $price)->toBe(365.0);
});

it('never allows a custom data price below cost even when the normal selling price is not profitable', function () {
    ['user' => $user, 'network' => $network, 'product' => $product, 'plan' => $plan] = dataPricingFixture(350, 3);

    $plan->update(['user_level_7_selling_price' => 352]);
    $plan->refresh();

    $price = (new DataPlansService())->get_customer_price_per_plan([
        'product_id' => $product->id,
        'network_id' => $network->id,
        'user' => $user,
        'plan_details' => $plan,
    ])['message'];

    expect((float) $price)->toBe(352.0);
});
