<?php

use App\Models\AffiliateDataPurchaseRequest;
use App\Models\Automation;
use App\Models\Network;
use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\ProductPlanCategory;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Api\Affiliate\MsorgDataProviderExecutor;

use function Pest\Laravel\postJson;

function msorgDataPlan(array $overrides = []): ProductPlan
{
    $automation = Automation::create([
        'automation_name' => fake()->unique()->word(),
        'slug' => 'msorg-test-'.fake()->unique()->slug(),
        'automation_group' => 'msorg',
        'domain_url' => 'https://provider.test',
        'data_url' => 'https://provider.test/data',
        'api_public_key' => 'provider-key',
    ]);
    $product = Product::create([
        'slug' => 'data', 'product_name' => 'Data', 'visibility' => '1', 'active_status' => '1',
    ]);
    $network = Network::create([
        'network_name' => fake()->unique()->word(), 'api_id' => fake()->unique()->numerify('##'), 'visibility' => '1',
    ]);
    $category = ProductPlanCategory::create([
        'product_plan_category_name' => fake()->unique()->words(2, true),
        'automation_id' => $automation->id, 'product_id' => $product->id,
        'network_id' => $network->id, 'visibility' => '1',
    ]);

    return ProductPlan::create(array_merge([
        'product_plan_name' => '1GB Monthly', 'product_plan_category_id' => $category->id,
        'automation_product_plan_id' => 'provider-plan', 'automation_id' => $automation->id,
        'api_id' => fake()->unique()->numberBetween(1000, 9999), 'default_selling_price' => '500',
        'user_level_1_selling_price' => '480', 'data_size_in_mb' => '1024',
        'validity_in_days' => '30', 'visibility' => '1', 'public_visibility' => '1', 'active_status' => '1',
    ], $overrides));
}

it('returns the affiliate-compatible unauthorized response', function () {
    postJson('/api/data', [])->assertUnauthorized()->assertExactJson([
        'Status' => 'failed',
        'apiresponse' => 'Unauthorized. Invalid API token.',
        'api_response' => 'Unauthorized. Invalid API token.',
    ]);
});

it('rejects a token belonging to a deactivated account', function () {
    $user = User::factory()->create(['api_token' => 'msorg-deactivated-token', 'is_deactivated' => true]);

    postJson('/api/data', [], ['Authorization' => 'Token '.$user->api_token])
        ->assertUnauthorized()->assertJsonPath('Status', 'failed');
});

it('requires a valid client reference and returns validation errors as 422', function () {
    $user = User::factory()->create(['api_token' => 'msorg-validation-token']);

    postJson('/api/data', [
        'network' => '1', 'mobile_number' => '08030000000', 'plan' => '10', 'Ported_number' => true,
    ], ['Authorization' => 'Token '.$user->api_token])
        ->assertUnprocessable()
        ->assertJsonPath('Status', 'failed')
        ->assertJsonPath('errors.reference.0', 'The reference field is required.');
});

it('rejects a plan that does not belong to the supplied network', function () {
    $user = User::factory()->create(['api_token' => 'msorg-network-token']);
    $plan = msorgDataPlan();

    postJson('/api/data', [
        'network' => '999999', 'mobile_number' => '08030000000', 'plan' => $plan->api_id,
        'Ported_number' => true, 'reference' => 'MSORG-WRONG-NETWORK-1',
    ], ['Authorization' => 'Token '.$user->api_token])
        ->assertUnprocessable()
        ->assertJsonPath('Status', 'failed');
});

it('reserves the wallet, delivers data, and replays the saved response without calling the provider twice', function () {
    $user = User::factory()->create(['api_token' => 'msorg-success-token', 'main_wallet' => '1000']);
    $plan = msorgDataPlan();
    $actualProvider = Automation::create([
        'automation_name' => 'Rossytechs',
        'slug' => 'rossytechs',
        'automation_group' => 'v2',
        'domain_url' => 'https://rossytechs.test',
        'data_url' => 'https://rossytechs.test/api/data/',
        'api_public_key' => 'provider-key',
    ]);
    $provider = Mockery::mock(MsorgDataProviderExecutor::class);
    $provider->shouldReceive('execute')->once()->andReturn([
        'status' => 1, 'user_message' => 'Data delivered.', 'admin_message' => 'sensitive provider detail',
        'provider_id' => $actualProvider->id, 'provider_name' => 'Rossytechs',
        'provider_slug' => 'rossytechs', 'provider_plan_id' => 'rossy-plan-289',
    ]);
    app()->instance(MsorgDataProviderExecutor::class, $provider);
    $payload = [
        'network' => $plan->product_plan_category->network->api_id, 'mobile_number' => '08030000000',
        'plan' => $plan->api_id, 'Ported_number' => true, 'reference' => 'MSORG-SUCCESS-1',
    ];
    $headers = ['Authorization' => 'Token '.$user->api_token];

    $first = postJson('/api/data', $payload, $headers)->assertOk()
        ->assertJsonPath('Status', 'successful')->assertJsonPath('ident', 'MSORG-SUCCESS-1')
        ->assertJsonPath('balance_before', '1000.00')
        ->assertJsonPath('idempotent_replay', false)
        ->assertJsonPath('provider_called', true)
        ->assertJsonMissing(['api_response' => 'sensitive provider detail']);
    postJson('/api/data/', $payload, $headers)->assertOk()
        ->assertJsonPath('Status', 'successful')
        ->assertJsonPath('idempotent_replay', true)
        ->assertJsonPath('provider_called', false)
        ->assertJsonPath('apiresponse', 'Existing successful transaction returned. No new purchase was made.');

    $expectedBalance = 1000 - (float) $first->json('plan_amount');
    $transaction = Transaction::where('user_id', $user->id)->sole();
    expect((float) $first->json('balance_after'))->toBe($expectedBalance)
        ->and((float) $user->fresh()->main_wallet)->toBe($expectedBalance)
        ->and(Transaction::where('user_id', $user->id)->count())->toBe(1)
        ->and(AffiliateDataPurchaseRequest::where('user_id', $user->id)->count())->toBe(1)
        ->and($transaction->automation_id)->toBe($actualProvider->id)
        ->and($transaction->admin_screen_message)->toBe(
            'Provider: Rossytechs | Provider plan: rossy-plan-289 | Status: successful | Response: Data delivered.'
        );
});

it('returns 409 when the same reference is reused with different purchase details', function () {
    $user = User::factory()->create(['api_token' => 'msorg-conflict-token', 'main_wallet' => '1000']);
    $plan = msorgDataPlan();
    $provider = Mockery::mock(MsorgDataProviderExecutor::class);
    $provider->shouldReceive('execute')->once()->andReturn(['status' => 1, 'user_message' => 'Delivered.']);
    app()->instance(MsorgDataProviderExecutor::class, $provider);
    $payload = [
        'network' => $plan->product_plan_category->network->api_id, 'mobile_number' => '08030000000',
        'plan' => $plan->api_id, 'Ported_number' => true, 'reference' => 'MSORG-CONFLICT-1',
    ];
    $headers = ['Authorization' => 'Token '.$user->api_token];
    postJson('/api/data', $payload, $headers)->assertOk();

    postJson('/api/data', array_merge($payload, ['mobile_number' => '08031111111']), $headers)
        ->assertConflict()->assertJsonPath('Status', 'failed');
});

it('refunds the reservation when the provider fails', function () {
    $user = User::factory()->create(['api_token' => 'msorg-failure-token', 'main_wallet' => '1000']);
    $plan = msorgDataPlan();
    $provider = Mockery::mock(MsorgDataProviderExecutor::class);
    $provider->shouldReceive('execute')->once()->andReturn(['status' => -1, 'user_message' => 'Provider rejected purchase.']);
    app()->instance(MsorgDataProviderExecutor::class, $provider);

    postJson('/api/data', [
        'network' => $plan->product_plan_category->network->api_id, 'mobile_number' => '08030000000',
        'plan' => $plan->api_id, 'Ported_number' => true, 'reference' => 'MSORG-FAILURE-1',
    ], ['Authorization' => 'Token '.$user->api_token])
        ->assertStatus(502)->assertJsonPath('Status', 'failed')
        ->assertJsonPath('balance_before', '1000.00')->assertJsonPath('balance_after', '1000.00');

    expect((float) $user->fresh()->main_wallet)->toBe(1000.0)
        ->and((int) Transaction::where('user_id', $user->id)->sole()->status)->toBe(-1);
});

it('does not call the provider or debit the wallet when the balance is insufficient', function () {
    $user = User::factory()->create(['api_token' => 'msorg-low-balance-token', 'main_wallet' => '0']);
    $plan = msorgDataPlan();
    $provider = Mockery::mock(MsorgDataProviderExecutor::class);
    $provider->shouldNotReceive('execute');
    app()->instance(MsorgDataProviderExecutor::class, $provider);

    postJson('/api/data', [
        'network' => $plan->product_plan_category->network->api_id, 'mobile_number' => '08030000000',
        'plan' => $plan->api_id, 'Ported_number' => true, 'reference' => 'MSORG-LOW-BALANCE-1',
    ], ['Authorization' => 'Token '.$user->api_token])
        ->assertUnprocessable()->assertJsonPath('Status', 'failed')
        ->assertJsonPath('apiresponse', 'Insufficient wallet balance.');

    expect((float) $user->fresh()->main_wallet)->toBe(0.0);
});
