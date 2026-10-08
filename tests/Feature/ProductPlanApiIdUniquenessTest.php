<?php

use App\Models\Automation;
use App\Models\Network;
use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\ProductPlanCategory;
use App\Models\Role;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\putJson;

function productPlanApiIdFixture(string|null $apiId = null): ProductPlan
{
    $automation = Automation::create([
        'automation_name' => 'API ID Guard Provider '.Str::uuid(),
        'slug' => 'api-id-guard-provider-'.Str::uuid(),
        'domain_url' => 'https://example.test',
        'activation_status' => '1',
    ]);

    $network = Network::create([
        'network_name' => 'API ID Guard Network '.Str::uuid(),
        'api_id' => (string) random_int(100_000, 999_999),
        'visibility' => '1',
    ]);

    $product = Product::create([
        'slug' => 'data-'.Str::uuid(),
        'product_name' => 'Data '.Str::uuid(),
        'visibility' => '1',
        'active_status' => '1',
    ]);

    $category = ProductPlanCategory::create([
        'product_plan_category_name' => 'API ID Guard Category '.Str::uuid(),
        'automation_id' => $automation->id,
        'product_id' => $product->id,
        'network_id' => $network->id,
        'visibility' => '1',
    ]);

    return ProductPlan::create([
        'product_plan_name' => 'API ID Guard Plan '.Str::uuid(),
        'product_plan_category_id' => $category->id,
        'automation_id' => $automation->id,
        'automation_product_plan_id' => (string) random_int(100_000, 999_999),
        'api_id' => $apiId,
        'cost_price' => 100,
        'default_selling_price' => 110,
        'user_level_1_selling_price' => 110,
        'visibility' => '1',
    ]);
}

it('rejects duplicate non-null product plan API IDs at model level', function () {
    productPlanApiIdFixture('PLAN-API-123');

    expect(fn () => productPlanApiIdFixture('PLAN-API-123'))
        ->toThrow(ValidationException::class);
});

it('still allows multiple product plans without API IDs', function () {
    productPlanApiIdFixture();
    productPlanApiIdFixture();

    expect(ProductPlan::query()->whereNull('api_id')->count())->toBe(2);
});

it('allows pricing-only updates on legacy product plans that already have duplicate API IDs', function () {
    $role = Role::firstOrCreate(['role_name' => 'Admin']);
    actingAs(User::factory()->create(['role_id' => $role->id]));

    $first = productPlanApiIdFixture('1');
    $second = productPlanApiIdFixture();

    ProductPlan::withoutEvents(fn () => $second->forceFill(['api_id' => '1'])->save());

    putJson(route('admin.product_plans.update_selling_prices', $first->id), [
        'user_level_1_selling_price' => 101,
        'user_level_2_selling_price' => 102,
        'user_level_3_selling_price' => 103,
        'user_level_4_selling_price' => 104,
        'user_level_5_selling_price' => 105,
        'user_level_6_selling_price' => 106,
        'user_level_7_selling_price' => 107,
    ])->assertOk()
        ->assertJsonPath('success', true);

    expect($first->fresh()->user_level_1_selling_price)->toBe('101');
});
