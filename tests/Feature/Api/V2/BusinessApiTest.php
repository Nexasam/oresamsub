<?php

use App\Http\Services\Api\v1\VendorUsersApi\Products\ProductsService;
use App\Models\Automation;
use App\Models\Network;
use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\ProductPlanCategory;
use App\Models\Transaction;
use App\Models\User;

use function Pest\Laravel\get;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

function businessHeaders(User $user): array
{
    return ['Authorization' => 'Bearer '.$user->api_token];
}

function businessPlan(string $slug = 'data', array $overrides = []): ProductPlan
{
    $automation = Automation::create(['automation_name' => fake()->unique()->word(), 'slug' => fake()->unique()->slug(), 'domain_url' => 'https://provider.test']);
    $product = Product::create(['slug' => $slug, 'product_name' => ucfirst($slug), 'visibility' => '1', 'active_status' => '1']);
    $network = Network::create(['network_name' => fake()->unique()->word(), 'api_id' => fake()->unique()->numerify('##'), 'visibility' => '1']);
    $category = ProductPlanCategory::create([
        'product_plan_category_name' => fake()->unique()->words(2, true), 'automation_id' => $automation->id,
        'product_id' => $product->id, 'network_id' => $network->id, 'visibility' => '1',
    ]);

    return ProductPlan::create(array_merge([
        'product_plan_name' => '1GB Monthly', 'product_plan_category_id' => $category->id,
        'automation_product_plan_id' => 'vendor-plan', 'automation_id' => $automation->id,
        'api_id' => fake()->unique()->numberBetween(1000, 9999), 'default_selling_price' => '500',
        'user_level_1_selling_price' => '480', 'user_level_2_selling_price' => '450',
        'data_size_in_mb' => '1024', 'validity_in_days' => '30', 'visibility' => '1',
        'public_visibility' => '1', 'active_status' => '1',
    ], $overrides));
}

it('requires a valid users api token', function () {
    getJson('/api/v2/wallet')->assertUnauthorized()->assertJsonPath('success', false);
});

it('returns a safe catalogue at the authenticated business price', function () {
    $user = User::factory()->create(['api_token' => 'valid-business-token']);
    $user->user_plan->update(['plan_level' => 2]);
    businessPlan();
    businessPlan('data', ['visibility' => '0', 'product_plan_name' => 'Hidden plan']);

    getJson('/api/v2/catalogue', businessHeaders($user))
        ->assertOk()->assertJsonPath('success', true)->assertJsonPath('data.0.price', 450)
        ->assertJsonMissing(['name' => 'Hidden plan'])->assertJsonMissingPath('data.0.automation_id')
        ->assertJsonMissingPath('data.0.cost_price');
});

it('returns the authenticated business wallet only', function () {
    $user = User::factory()->create(['api_token' => 'wallet-token', 'main_wallet' => '1234.56']);

    getJson('/api/v2/wallet', businessHeaders($user))
        ->assertOk()->assertJsonPath('data.currency', 'NGN')->assertJsonPath('data.available_balance', 1234.56);
});

it('processes a data purchase without accepting a transaction pin', function () {
    $user = User::factory()->create(['api_token' => 'purchase-token', 'pin' => '1234']);
    $plan = businessPlan();
    $products = Mockery::mock(ProductsService::class);
    $products->shouldReceive('buy_data_service_one_api')->once()->with(Mockery::on(
        fn (array $payload) => $payload['user_id'] === $user->id
            && $payload['product_plan_id'] === $plan->id
            && $payload['phone_number'] === '08030000000'
            && $payload['reference'] === 'BIZ-DATA-001'
    ))->andReturn(['status' => 1, 'status_code' => 200, 'Status' => 'successful', 'message' => 'Delivered']);
    app()->instance(ProductsService::class, $products);

    postJson('/api/v2/buy-service', [
        'service' => 'data', 'plan_id' => $plan->api_id, 'customer' => '08030000000', 'reference' => 'BIZ-DATA-001',
    ], businessHeaders($user))->assertOk()->assertJsonPath('data.status', 'successful')->assertJsonMissingPath('data.admin_message');
});

it('processes airtime through the same purchase endpoint', function () {
    $user = User::factory()->create(['api_token' => 'airtime-token', 'pin' => '1234']);
    $plan = businessPlan('airtime', ['product_plan_name' => 'Airtime']);
    $products = Mockery::mock(ProductsService::class);
    $products->shouldReceive('buy_airtime_service')->once()->with(Mockery::on(
        fn (array $payload) => $payload['product_plan_id'] === $plan->id
            && $payload['amount'] === 1000.0
            && $payload['actual_amount'] === 1000.0
    ))->andReturn(['status' => 1, 'status_code' => 200, 'Status' => 'successful', 'message' => 'Airtime delivered']);
    app()->instance(ProductsService::class, $products);

    postJson('/api/v2/buy-service', [
        'service' => 'airtime', 'plan_id' => $plan->api_id, 'customer' => '08030000000',
        'amount' => 1000, 'reference' => 'BIZ-AIRTIME-001',
    ], businessHeaders($user))->assertOk()->assertJsonPath('data.service', 'airtime')->assertJsonPath('data.amount', 1000);
});

it('scopes transaction reconciliation to the authenticated business', function () {
    $owner = User::factory()->create(['api_token' => 'owner-token']);
    $other = User::factory()->create(['api_token' => 'other-token']);
    $plan = businessPlan();
    Transaction::create([
        'user_id' => $owner->id, 'product_plan_id' => $plan->id, 'transaction_category' => 'data',
        'status' => '1', 'wallet_category' => 'main_wallet', 'phone_number' => '08030000000',
        'amount' => '450', 'balance_before' => '1000', 'balance_after' => '550',
        'description' => 'Data purchase', 'txn_reference' => 'BIZ-STATUS-001',
    ]);

    getJson('/api/v2/transactions/BIZ-STATUS-001', businessHeaders($other))->assertNotFound();
    getJson('/api/v2/transactions/BIZ-STATUS-001', businessHeaders($owner))
        ->assertOk()->assertJsonPath('data.reference', 'BIZ-STATUS-001')->assertJsonPath('data.status', 'successful');
});

it('returns an existing matching purchase instead of charging twice', function () {
    $user = User::factory()->create(['api_token' => 'replay-token']);
    $plan = businessPlan();
    Transaction::create([
        'user_id' => $user->id, 'product_plan_id' => $plan->id, 'transaction_category' => 'data',
        'status' => '1', 'wallet_category' => 'main_wallet', 'phone_number' => '08030000000',
        'amount' => '450', 'balance_before' => '1000', 'balance_after' => '550',
        'description' => 'Data purchase', 'txn_reference' => 'BIZ-REPLAY-001',
    ]);
    $products = Mockery::mock(ProductsService::class);
    $products->shouldNotReceive('buy_data_service_one_api');
    app()->instance(ProductsService::class, $products);

    postJson('/api/v2/buy-service', [
        'service' => 'data', 'plan_id' => $plan->api_id, 'customer' => '08030000000', 'reference' => 'BIZ-REPLAY-001',
    ], businessHeaders($user))->assertOk()->assertJsonPath('meta.idempotent_replay', true);
});

it('rejects reuse of a reference for different purchase details', function () {
    $user = User::factory()->create(['api_token' => 'conflict-token']);
    $plan = businessPlan();
    Transaction::create([
        'user_id' => $user->id, 'product_plan_id' => $plan->id, 'transaction_category' => 'data',
        'status' => '1', 'wallet_category' => 'main_wallet', 'phone_number' => '08030000000',
        'amount' => '450', 'balance_before' => '1000', 'balance_after' => '550',
        'description' => 'Data purchase', 'txn_reference' => 'BIZ-CONFLICT-001',
    ]);

    postJson('/api/v2/buy-service', [
        'service' => 'data', 'plan_id' => $plan->api_id, 'customer' => '08140000000', 'reference' => 'BIZ-CONFLICT-001',
    ], businessHeaders($user))->assertConflict()->assertJsonPath('success', false);
});

it('publishes branded documentation and an OpenAPI contract', function () {
    get('/developers')->assertOk()->assertSee('OresamSub API')->assertSee('/api/v2/buy-service');
    getJson('/api/v2/openapi.json')->assertOk()->assertJsonPath('openapi', '3.1.0');
});
