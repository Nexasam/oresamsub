<?php

use App\Http\Services\DataPlansService;
use App\Models\Automation;
use App\Models\Network;
use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\ProductPlanCategory;
use App\Models\Transaction;
use App\Models\User;
use Mockery\MockInterface;

function favoritePlanFixture(
    Automation $automation,
    Product $product,
    Network $network,
    string $name,
    string $visibility = '1',
): ProductPlan {
    $category = ProductPlanCategory::create([
        'product_plan_category_name' => fake()->unique()->word(),
        'automation_id' => $automation->id,
        'product_id' => $product->id,
        'network_id' => $network->id,
    ]);

    return ProductPlan::create([
        'product_plan_name' => $name,
        'product_plan_category_id' => $category->id,
        'automation_product_plan_id' => fake()->unique()->numerify('plan-####'),
        'automation_id' => $automation->id,
        'cost_price' => 100,
        'data_size_in_mb' => 1000,
        'validity_in_days' => 30,
        'default_selling_price' => 150,
        'user_level_1_selling_price' => 150,
        'visibility' => $visibility,
    ]);
}

function favoriteTransaction(
    User $user,
    ProductPlan $plan,
    string $status,
    string $category,
    string $phone,
    int $minutesAgo,
): Transaction {
    return Transaction::create([
        'user_id' => $user->id,
        'product_plan_id' => $plan->id,
        'transaction_category' => $category,
        'status' => $status,
        'wallet_category' => 'main_wallet',
        'phone_number' => $phone,
        'amount' => 150,
        'balance_before' => 1000,
        'balance_after' => 850,
        'description' => 'Favourite plan test',
        'created_at' => now()->subMinutes($minutesAgo),
        'updated_at' => now()->subMinutes($minutesAgo),
    ]);
}

it('returns visible plans ranked by successful data usage with customer pricing', function () {
    $user = User::factory()->create();
    $automation = Automation::create([
        'automation_name' => 'Test automation',
        'slug' => fake()->unique()->slug(),
        'domain_url' => 'https://provider.test',
    ]);
    $product = Product::create([
        'slug' => 'data',
        'product_name' => 'Data',
    ]);
    $network = Network::create([
        'api_id' => 'mtn',
        'network_name' => 'MTN',
    ]);

    $mostUsed = favoritePlanFixture($automation, $product, $network, '1GB Monthly');
    $second = favoritePlanFixture($automation, $product, $network, '2GB Monthly');
    $hidden = favoritePlanFixture($automation, $product, $network, 'Hidden Plan', '0');

    favoriteTransaction($user, $mostUsed, '1', 'data', '08030000001', 30);
    favoriteTransaction($user, $mostUsed, '1', 'data', '08030000002', 10);
    favoriteTransaction($user, $mostUsed, '-1', 'data', '08030000003', 5);
    favoriteTransaction($user, $second, '1', 'data', '08040000001', 20);
    favoriteTransaction($user, $second, '1', 'airtime', '08040000002', 1);
    favoriteTransaction($user, $hidden, '1', 'data', '08050000001', 2);

    $service = mock(DataPlansService::class, function (MockInterface $mock): void {
        $mock->makePartial();
        $mock->shouldReceive('get_customer_price_per_plan')
            ->andReturnUsing(fn (array $data) => [
                'status' => 1,
                'message' => $data['plan_details']->product_plan_name === '1GB Monthly' ? 175 : 275,
                'upline_commission' => 0,
            ]);
    });

    $plans = $service->favoriteDataPlans($user, 10);

    expect($plans)->toHaveCount(2)
        ->and($plans->pluck('product_plan_id')->all())->toBe([$mostUsed->id, $second->id])
        ->and($plans->pluck('usage_count')->all())->toBe([2, 1])
        ->and($plans->pluck('selling_price')->all())->toBe([175, 275])
        ->and($plans->first()['phone_number'])->toBe('08030000002')
        ->and($plans->pluck('product_plan_id'))->not->toContain($hidden->id);
});
