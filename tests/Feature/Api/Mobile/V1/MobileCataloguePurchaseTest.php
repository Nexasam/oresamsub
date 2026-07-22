<?php

use App\Models\Automation;
use App\Models\Network;
use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\ProductPlanCategory;
use App\Models\Transaction;
use App\Models\User;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

function catalogueHeaders(User $user): array
{
    return ['Authorization' => 'Bearer '.$user->createToken('mobile:test', ['mobile'])->plainTextToken];
}

function cataloguePlan(string $slug = 'data', array $planOverrides = []): ProductPlan
{
    $automation = Automation::create(['automation_name' => fake()->unique()->word(), 'slug' => fake()->unique()->slug(), 'domain_url' => 'https://provider.test']);
    $product = Product::create(['slug' => $slug, 'product_name' => ucfirst($slug), 'visibility' => '1', 'active_status' => '1']);
    $network = Network::create(['network_name' => fake()->unique()->word(), 'api_id' => fake()->unique()->numerify('##'), 'visibility' => '1']);
    $category = ProductPlanCategory::create([
        'product_plan_category_name' => fake()->unique()->words(3, true), 'automation_id' => $automation->id,
        'product_id' => $product->id, 'network_id' => $network->id, 'visibility' => '1',
    ]);

    return ProductPlan::create(array_merge([
        'product_plan_name' => '1GB Monthly', 'product_plan_category_id' => $category->id, 'automation_product_plan_id' => 'vendor-plan',
        'automation_id' => $automation->id, 'default_selling_price' => '500', 'user_level_1_selling_price' => '480',
        'user_level_2_selling_price' => '450', 'visibility' => '1', 'public_visibility' => '1', 'active_status' => '1',
    ], $planOverrides));
}

it('returns only visible products and resolves the authenticated users price server-side', function () {
    $user = User::factory()->create();
    $user->user_plan->update(['plan_level' => 2]);
    $plan = cataloguePlan();
    Product::create(['slug' => 'hidden', 'product_name' => 'Hidden Product', 'visibility' => '0', 'active_status' => '1']);
    Product::create(['slug' => 'e_pins', 'product_name' => 'E-PINS', 'visibility' => '1', 'active_status' => '1']);
    Product::create(['slug' => 'result_checker', 'product_name' => 'Result Checker', 'visibility' => '1', 'active_status' => '1']);

    getJson('/api/mobile/v1/catalogue/products', catalogueHeaders($user))
        ->assertOk()->assertJsonFragment(['slug' => 'data'])->assertJsonMissing(['slug' => 'hidden'])
        ->assertJsonMissing(['slug' => 'e_pins'])->assertJsonMissing(['slug' => 'result_checker']);
    getJson('/api/mobile/v1/catalogue/plans?category_id='.$plan->product_plan_category_id, catalogueHeaders($user))
        ->assertOk()->assertJsonPath('data.0.price', 450)->assertJsonMissingPath('data.0.cost_price')->assertJsonMissingPath('data.0.automation_id');
});

it('rejects a purchase before provider processing when the transaction pin is wrong', function () {
    $user = User::factory()->create(['pin' => '1234']);
    $plan = cataloguePlan();

    postJson('/api/mobile/v1/purchases/data', [
        'product_plan_id' => $plan->id, 'phone_number' => '08030000000', 'pin' => '9999', 'reference' => 'MOB-WRONG-PIN-1',
    ], catalogueHeaders($user))->assertUnprocessable()->assertJsonPath('message', 'Incorrect transaction PIN.');
    expect(Transaction::where('txn_reference', 'MOB-WRONG-PIN-1')->exists())->toBeFalse();
});

it('rejects duplicate idempotency references and scopes reconciliation by user', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $plan = cataloguePlan();
    $transaction = Transaction::create([
        'user_id' => $owner->id, 'product_plan_id' => $plan->id, 'transaction_category' => 'data', 'status' => '3',
        'wallet_category' => 'main_wallet', 'phone_number' => '08030000000', 'amount' => '450', 'balance_before' => '1000',
        'balance_after' => '550', 'description' => 'Data purchase', 'txn_reference' => 'MOB-IDEMPOTENT-1',
    ]);

    postJson('/api/mobile/v1/purchases/data', [
        'product_plan_id' => $plan->id, 'phone_number' => '08030000000', 'pin' => (string) $owner->pin, 'reference' => 'MOB-IDEMPOTENT-1',
    ], catalogueHeaders($owner))->assertUnprocessable();
    app('auth')->forgetGuards();
    getJson('/api/mobile/v1/purchases/status/MOB-IDEMPOTENT-1', catalogueHeaders($other))->assertNotFound();
    app('auth')->forgetGuards();
    getJson('/api/mobile/v1/purchases/status/MOB-IDEMPOTENT-1', catalogueHeaders($owner))
        ->assertOk()->assertJsonPath('data.transaction.id', $transaction->id)->assertJsonPath('data.transaction.status', 'processing');
});
