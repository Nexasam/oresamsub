<?php

use App\Http\Services\Api\v1\VendorUsersApi\Products\ProductsService;
use App\Models\Automation;
use App\Models\AutomationProductPlan;
use App\Models\Network;
use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\ProductPlanCategory;
use App\Models\ProductPlanCustomPricing;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAutomation;
use App\Models\UserProductPlanAutomation;
use App\Services\BusinessApi\BillerValidationService;
use Illuminate\Support\Facades\Log;

use function Pest\Laravel\actingAs;
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
    $plan = businessPlan();
    AutomationProductPlan::create([
        'product_plan_id' => $plan->id,
        'automation_id' => $plan->automation_id,
        'provider_plan_id' => 'provider-plan',
        'is_active' => true,
    ]);
    businessPlan('data', ['visibility' => '0', 'product_plan_name' => 'Hidden plan']);

    getJson('/api/v2/catalogue', businessHeaders($user))
        ->assertOk()->assertJsonPath('success', true)->assertJsonPath('data.0.price', 450)
        ->assertJsonMissing(['name' => 'Hidden plan'])->assertJsonMissingPath('data.0.automation_id')
        ->assertJsonMissingPath('data.0.cost_price');
});

it('returns the exact customer price shown by the PWA in the business catalogue', function () {
    $user = User::factory()->create(['api_token' => 'pwa-parity-token']);
    $user->user_plan->update(['plan_level' => 2]);
    $plan = businessPlan('data', [
        'cost_price' => '390',
        'user_level_2_selling_price' => '450',
    ]);
    ProductPlanCustomPricing::create([
        'product_plan_id' => $plan->id,
        'user_id' => $user->id,
        'price' => '417.50',
        'added_by' => $user->id,
    ]);

    actingAs($user);

    $pwaPrice = getJson('/user/data/fetch_product_plans?network_id='.
        $plan->product_plan_category->network_id.'&product_slug=data')
        ->assertOk()
        ->json('data.0.selling_price');

    getJson('/api/v2/catalogue', businessHeaders($user))
        ->assertOk()
        ->assertJsonPath('data.0.id', (string) $plan->api_id)
        ->assertJsonPath('data.0.price', (float) $pwaPrice)
        ->assertJsonPath('data.0.pricing_type', 'fixed');
});

it('uses the PWA pricing flow for every non data catalogue product', function (string $slug, string $publicService, string $pricingType) {
    $user = User::factory()->create(['api_token' => $slug.'-parity-token']);
    $user->user_plan->update(['plan_level' => 2]);
    $plan = businessPlan($slug, [
        'user_level_2_selling_price' => $pricingType === 'percentage_discount' ? '3.5' : '725',
    ]);
    ProductPlanCustomPricing::create([
        'product_plan_id' => $plan->id,
        'user_id' => $user->id,
        'price' => $pricingType === 'percentage_discount' ? '4.25' : '699',
        'added_by' => $user->id,
    ]);

    actingAs($user);

    $pwaResponse = getJson('/user/data/fetch_product_plans?network_id='.
        $plan->product_plan_category->network_id.'&plan_category_id='.
        $plan->product_plan_category_id.'&product_slug='.$slug)
        ->assertOk();
    $pwaPlan = collect($pwaResponse->json('data'))->firstWhere('product_plan_id', $plan->id);

    $cataloguePlan = collect(getJson('/api/v2/catalogue', businessHeaders($user))
        ->assertOk()
        ->json('data'))->firstWhere('id', (string) $plan->api_id);

    expect($cataloguePlan)
        ->not->toBeNull()
        ->and($cataloguePlan['service'])->toBe($publicService)
        ->and($cataloguePlan['price'])->toEqual((float) $pwaPlan['selling_price'])
        ->and($cataloguePlan['pricing_type'])->toBe($pricingType);
})->with([
    'airtime' => ['airtime', 'airtime', 'percentage_discount'],
    'cable' => ['cable_subscription', 'cable', 'fixed'],
    'electricity' => ['utility_bills', 'electricity', 'percentage_discount'],
]);

it('includes cable and electricity plans in the business catalogue', function () {
    $user = User::factory()->create(['api_token' => 'bills-catalogue-token']);
    businessPlan('cable_subscription', ['product_plan_name' => 'DStv Compact']);
    businessPlan('utility_bills', ['product_plan_name' => 'IBEDC Prepaid']);

    getJson('/api/v2/catalogue', businessHeaders($user))
        ->assertOk()
        ->assertJsonFragment(['service' => 'cable', 'name' => 'DStv Compact'])
        ->assertJsonFragment(['service' => 'electricity', 'name' => 'IBEDC Prepaid']);
});

it('validates cable customers and returns a short lived validation reference', function () {
    $user = User::factory()->create(['api_token' => 'validate-cable-token']);
    $plan = businessPlan('cable_subscription', ['product_plan_name' => 'DStv Compact']);
    $validator = Mockery::mock(BillerValidationService::class);
    $validator->shouldReceive('validate')->once()->andReturn([
        'validation_reference' => 'VAL-CABLE-001', 'customer_name' => 'Test Customer',
        'address' => null, 'expires_at' => now()->addMinutes(10)->toIso8601String(),
    ]);
    app()->instance(BillerValidationService::class, $validator);

    postJson('/api/v2/validate-customer', [
        'service' => 'cable', 'plan_id' => $plan->api_id, 'customer_number' => '1234567890',
    ], businessHeaders($user))->assertOk()->assertJsonPath('data.validation_reference', 'VAL-CABLE-001');
});

it('returns the authenticated business wallet only', function () {
    $user = User::factory()->create(['api_token' => 'wallet-token', 'main_wallet' => '1234.56']);

    getJson('/api/v2/wallet', businessHeaders($user))
        ->assertOk()->assertJsonPath('data.currency', 'NGN')->assertJsonPath('data.available_balance', 1234.56);
});

it('uses the first validation error as the API response message', function () {
    $user = User::factory()->create(['api_token' => 'validation-message-token']);
    $plan = businessPlan('airtime', ['product_plan_name' => 'Airtime']);

    postJson('/api/v2/buy-service', [
        'service' => 'airtime',
        'plan_id' => $plan->api_id,
        'customer_number' => '08030000000',
        'amount' => 25,
        'reference' => 'BIZ-INVALID-AMOUNT-001',
    ], businessHeaders($user))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'The amount must be at least 50.')
        ->assertJsonPath('errors.amount.0', 'The amount must be at least 50.');
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
        'service' => 'data', 'plan_id' => $plan->api_id, 'customer_number' => '08030000000', 'reference' => 'BIZ-DATA-001',
    ], businessHeaders($user))->assertOk()->assertJsonPath('data.status', 'successful')->assertJsonMissingPath('data.admin_message');
});

it('logs the duration of a business api purchase', function () {
    Log::spy();

    $user = User::factory()->create(['api_token' => 'timed-purchase-token', 'pin' => '1234']);
    $plan = businessPlan();
    $products = Mockery::mock(ProductsService::class);
    $products->shouldReceive('buy_data_service_one_api')->once()->andReturn([
        'status' => 1,
        'Status' => 'successful',
        'message' => 'Delivered',
    ]);
    app()->instance(ProductsService::class, $products);

    postJson('/api/v2/buy-service', [
        'service' => 'data',
        'plan_id' => $plan->api_id,
        'customer_number' => '08030000000',
        'reference' => 'BIZ-TIMING-001',
    ], businessHeaders($user))->assertOk();

    Log::shouldHaveReceived('info')->with('oresamsub.purchase.started', [
        'reference' => 'BIZ-TIMING-001',
        'service' => 'data',
    ])->once();
    Log::shouldHaveReceived('info')->with(
        'oresamsub.purchase.finished',
        Mockery::on(fn (array $context): bool => $context['reference'] === 'BIZ-TIMING-001'
            && $context['service'] === 'data'
            && is_float($context['duration_seconds'])
            && $context['duration_seconds'] >= 0)
    )->once();
});

it('logs unexpected errors from the complete business purchase flow', function () {
    Log::spy();

    $user = User::factory()->create(['api_token' => 'failed-purchase-token', 'pin' => '1234']);
    $plan = businessPlan('cable_subscription');
    $validator = Mockery::mock(BillerValidationService::class);
    $validator->shouldReceive('resolve')->once()->andThrow(new RuntimeException('Validation storage unavailable'));
    app()->instance(BillerValidationService::class, $validator);

    postJson('/api/v2/buy-service', [
        'service' => 'cable',
        'plan_id' => $plan->api_id,
        'customer_number' => '1234567890',
        'reference' => 'BIZ-FAILED-001',
        'validation_reference' => 'VAL-FAILED-001',
    ], businessHeaders($user))
        ->assertStatus(500)
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'The transaction could not be processed. Please contact support with your reference.');

    Log::shouldHaveReceived('error')->with(
        'oresamsub.purchase.unhandled_error',
        Mockery::on(fn (array $context): bool => $context['reference'] === 'BIZ-FAILED-001'
            && $context['service'] === 'cable'
            && $context['exception'] === RuntimeException::class
            && $context['message'] === 'Validation storage unavailable'
            && is_string($context['file'])
            && is_int($context['line'])
            && is_string($context['trace']))
    )->once();
});

it('falls back to the active plan provider when a customer has no provider override', function () {
    $user = User::factory()->create([
        'api_token' => 'missing-data-automation-token',
        'pin' => '1234',
        'main_wallet' => 1000,
    ]);
    $plan = businessPlan();
    AutomationProductPlan::create([
        'product_plan_id' => $plan->id,
        'automation_id' => $plan->automation_id,
        'provider_plan_id' => 'new-flow-provider-plan',
        'is_active' => true,
    ]);

    postJson('/api/v2/buy-service', [
        'service' => 'data',
        'plan_id' => $plan->api_id,
        'customer_number' => '08030000000',
        'reference' => 'BIZ-DATA-NO-AUTOMATION-001',
    ], businessHeaders($user))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Data processing failed.')
        ->assertJsonPath('data.status', 'failed');

    $transaction = Transaction::where('txn_reference', 'BIZ-DATA-NO-AUTOMATION-001')->sole();

    expect($transaction->user_product_plan_automation_id)->toBeNull()
        ->and((float) $transaction->balance_before)->toBe(1000.0)
        ->and((float) $transaction->balance_after)->toBe(1000.0);
});

it('uses the product plan automation for a legacy data plan', function () {
    $user = User::factory()->create([
        'api_token' => 'legacy-data-routing-token',
        'pin' => '1234',
        'main_wallet' => 1000,
    ]);
    $plan = businessPlan('data', [
        'automation_product_plan_id' => 'legacy-provider-plan-292',
        'cost_price' => 400,
    ]);
    $plan->automation->update([
        'api_public_key' => 'legacy-public-key',
        'api_secret_key' => 'legacy-secret-key',
        'data_url' => 'https://provider.test/data',
        'automation_group' => 'legacy',
    ]);

    postJson('/api/v2/buy-service', [
        'service' => 'data',
        'plan_id' => $plan->api_id,
        'customer_number' => '08030000000',
        'reference' => 'BIZ-DATA-LEGACY-ROUTING-001',
    ], businessHeaders($user))
        ->assertUnprocessable()
        ->assertJsonPath('data.status', 'failed')
        ->assertJsonPath('message', 'Data processing failed.');

    $transaction = Transaction::where('txn_reference', 'BIZ-DATA-LEGACY-ROUTING-001')->sole();

    expect($transaction->user_product_plan_automation_id)->toBeNull()
        ->and((float) $transaction->amount)->toBeGreaterThan(0);
});

it('uses the catalogue price for a data purchase amount and wallet check', function () {
    $user = User::factory()->create([
        'api_token' => 'data-catalogue-price-token',
        'pin' => '1234',
        'main_wallet' => 100,
    ]);
    $plan = businessPlan();
    AutomationProductPlan::create([
        'product_plan_id' => $plan->id,
        'automation_id' => $plan->automation_id,
        'provider_plan_id' => 'provider-plan',
        'is_active' => true,
    ]);
    $userAutomation = UserAutomation::create([
        'user_id' => $user->id,
        'automation_id' => $plan->automation_id,
        'product' => 'data',
        'pricing_amount' => 0,
    ]);
    UserProductPlanAutomation::create([
        'user_id' => $user->id,
        'product_plan_id' => $plan->id,
        'user_automation_id' => $userAutomation->id,
        'automation_product_plan_id' => 'provider-plan',
        'priority' => 1,
        'status' => 1,
    ]);

    postJson('/api/v2/buy-service', [
        'service' => 'data',
        'plan_id' => $plan->api_id,
        'customer_number' => '08030000000',
        'reference' => 'BIZ-DATA-CATALOGUE-PRICE-001',
    ], businessHeaders($user))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Insufficient wallet balance');

    $transaction = Transaction::where('txn_reference', 'BIZ-DATA-CATALOGUE-PRICE-001')->sole();

    expect((float) $transaction->amount)->toBe(480.0)
        ->and((float) $transaction->discounted_amount)->toBe(480.0)
        ->and((float) $transaction->balance_before)->toBe(100.0)
        ->and((float) $transaction->balance_after)->toBe(100.0);
});

it('keeps failed provider purchase balances unchanged in the transaction and response', function () {
    $user = User::factory()->create([
        'api_token' => 'failed-provider-balance-token',
        'pin' => '1234',
        'main_wallet' => 2560.99,
    ]);
    $plan = businessPlan('data', ['user_level_1_selling_price' => 1700]);
    AutomationProductPlan::create([
        'product_plan_id' => $plan->id,
        'automation_id' => $plan->automation_id,
        'provider_plan_id' => 'provider-plan',
        'is_active' => true,
    ]);
    $userAutomation = UserAutomation::create([
        'user_id' => $user->id,
        'automation_id' => $plan->automation_id,
        'product' => 'data',
        'pricing_amount' => 0,
    ]);
    UserProductPlanAutomation::create([
        'user_id' => $user->id,
        'product_plan_id' => $plan->id,
        'user_automation_id' => $userAutomation->id,
        'automation_product_plan_id' => 'provider-plan',
        'priority' => 1,
        'status' => 1,
    ]);

    postJson('/api/v2/buy-service', [
        'service' => 'data',
        'plan_id' => $plan->api_id,
        'customer_number' => '08168509044',
        'reference' => 'BIZ-DATA-PROVIDER-FAILED-001',
    ], businessHeaders($user))
        ->assertUnprocessable()
        ->assertJsonPath('data.status', 'failed')
        ->assertJsonPath('data.balance_before', 2560.99)
        ->assertJsonPath('data.balance_after', 2560.99);

    $transaction = Transaction::where('txn_reference', 'BIZ-DATA-PROVIDER-FAILED-001')->sole();

    expect((float) $transaction->balance_before)->toBe(2560.99)
        ->and((float) $transaction->balance_after)->toBe(2560.99)
        ->and((float) $user->fresh()->main_wallet)->toBe(2560.99);
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
        'service' => 'airtime', 'plan_id' => $plan->api_id, 'customer_number' => '08030000000',
        'amount' => 1000, 'reference' => 'BIZ-AIRTIME-001',
    ], businessHeaders($user))->assertOk()->assertJsonPath('data.service', 'airtime')->assertJsonPath('data.amount', 1000);
});

it('uses processing language instead of legacy pending wording for airtime', function () {
    $user = User::factory()->create(['api_token' => 'airtime-processing-message-token', 'pin' => '1234']);
    $plan = businessPlan('airtime', ['product_plan_name' => 'Airtime']);
    $products = Mockery::mock(ProductsService::class);
    $products->shouldReceive('buy_airtime_service')->once()->andReturn([
        'status' => 1,
        'status_code' => 200,
        'Status' => 'successful',
        'user_message' => 'Airtime transaction pending.',
    ]);
    app()->instance(ProductsService::class, $products);

    postJson('/api/v2/buy-service', [
        'service' => 'airtime',
        'plan_id' => $plan->api_id,
        'customer_number' => '08030000000',
        'amount' => 1000,
        'reference' => 'BIZ-AIRTIME-PROCESSING-001',
    ], businessHeaders($user))
        ->assertOk()
        ->assertJsonPath('message', 'Transaction is being processed.')
        ->assertJsonPath('data.status', 'successful');
});

it('processes a validated cable purchase through the one fit all endpoint', function () {
    $user = User::factory()->create(['api_token' => 'cable-purchase-token', 'pin' => '1234']);
    $plan = businessPlan('cable_subscription', ['product_plan_name' => 'DStv Compact']);
    $validator = Mockery::mock(BillerValidationService::class);
    $validator->shouldReceive('resolve')->once()->andReturn([
        'customer_name' => 'Test Customer', 'address' => null, 'extra_info' => '',
    ]);
    app()->instance(BillerValidationService::class, $validator);
    $products = Mockery::mock(ProductsService::class);
    $products->shouldReceive('buy_cable_service')->once()->with(Mockery::on(
        fn (array $payload) => $payload['smart_card_number'] === '1234567890'
            && $payload['validation_customer_name'] === 'Test Customer'
            && $payload['cable_product_plan_id'] === $plan->id
    ))->andReturn(['status' => 1, 'Status' => 'successful', 'message' => 'Cable delivered']);
    app()->instance(ProductsService::class, $products);

    postJson('/api/v2/buy-service', [
        'service' => 'cable', 'plan_id' => $plan->api_id, 'customer_number' => '1234567890',
        'validation_reference' => 'VAL-CABLE-001', 'reference' => 'BIZ-CABLE-001',
    ], businessHeaders($user))->assertOk()->assertJsonPath('data.service', 'cable');
});

it('processes validated electricity and returns the meter token', function () {
    $user = User::factory()->create(['api_token' => 'power-purchase-token', 'pin' => '1234']);
    $plan = businessPlan('utility_bills', ['product_plan_name' => 'IBEDC Prepaid']);
    $validator = Mockery::mock(BillerValidationService::class);
    $validator->shouldReceive('resolve')->once()->andReturn([
        'customer_name' => 'Power Customer', 'address' => 'Ibadan', 'extra_info' => 'Power Customer',
    ]);
    app()->instance(BillerValidationService::class, $validator);
    $products = Mockery::mock(ProductsService::class);
    $products->shouldReceive('buy_electricity_service')->once()->with(Mockery::on(
        fn (array $payload) => $payload['metre_number'] === '01234567890'
            && $payload['amount'] === 5000.0
            && $payload['electricity_product_plan_id'] === $plan->id
    ))->andReturn(['status' => 1, 'Status' => 'successful', 'message' => 'Power delivered', 'token' => '1234-5678-9012']);
    app()->instance(ProductsService::class, $products);

    postJson('/api/v2/buy-service', [
        'service' => 'electricity', 'plan_id' => $plan->api_id, 'customer_number' => '01234567890',
        'amount' => 5000, 'validation_reference' => 'VAL-POWER-001', 'reference' => 'BIZ-POWER-001',
    ], businessHeaders($user))->assertOk()->assertJsonPath('data.token', '1234-5678-9012');
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
        'service' => 'data', 'plan_id' => $plan->api_id, 'customer_number' => '08030000000', 'reference' => 'BIZ-REPLAY-001',
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
        'service' => 'data', 'plan_id' => $plan->api_id, 'customer_number' => '08140000000', 'reference' => 'BIZ-CONFLICT-001',
    ], businessHeaders($user))->assertConflict()->assertJsonPath('success', false);
});

it('publishes branded documentation and an OpenAPI contract', function () {
    get('/developers')->assertOk()->assertSee('OresamSub API')->assertSee('/api/v2/buy-service')
        ->assertSee('/api/v2/validate-customer')->assertSee('cable')->assertSee('electricity')
        ->assertSee('Validate cable customer')->assertSee('Buy electricity')->assertSee('Transaction lookup')
        ->assertSee('Cable TV')->assertSee('Step 1 · Validate customer')->assertSee('Step 2 · Buy electricity')
        ->assertSee('Response examples')->assertSee('Success responses')->assertSee('Failure responses')->assertSee('customer_number')
        ->assertSee('curlPreview')->assertSee('updateCurlPreview')
        ->assertSee('responseModal')->assertSee('showResponseModal')->assertSee('Copy response');
    getJson('/api/v2/openapi.json')->assertOk()->assertJsonPath('openapi', '3.1.0')
        ->assertJsonPath('paths./validate-customer.post.operationId', 'validateCustomer')
        ->assertJsonPath('paths./catalogue.get.responses.200.content.application/json.example.success', true)
        ->assertJsonPath('paths./transactions/{reference}.get.responses.404.content.application/json.example.message', 'Transaction not found.')
        ->assertJsonPath('components.schemas.PurchaseRequest.required.2', 'customer_number')
        ->assertJsonFragment(['message' => 'This reference has already been used for a different transaction.']);
});
