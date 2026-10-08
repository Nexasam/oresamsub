<?php

use App\Models\Automation;
use App\Models\Network;
use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\ProductPlanCategory;
use Illuminate\Validation\ValidationException;

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
