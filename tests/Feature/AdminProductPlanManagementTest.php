<?php

use App\Models\Automation;
use App\Models\AutomationProductPlan;
use App\Models\Network;
use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\ProductPlanCategory;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

function productPlanAdmin(): User
{
    $role = Role::firstOrCreate(['role_name' => 'Admin']);

    return User::factory()->create(['role_id' => $role->id]);
}

function adminProductPlanFixture(Automation $automation, array $overrides = []): ProductPlan
{
    $product = Product::create([
        'slug' => 'data',
        'product_name' => 'Data',
        'visibility' => '1',
        'active_status' => '1',
    ]);
    $network = Network::create([
        'network_name' => 'MTN',
        'api_id' => (string) Illuminate\Support\Str::uuid(),
        'visibility' => '1',
    ]);
    $category = ProductPlanCategory::create([
        'product_plan_category_name' => 'MTN SME ' . Illuminate\Support\Str::uuid(),
        'automation_id' => $automation->id,
        'product_id' => $product->id,
        'network_id' => $network->id,
        'visibility' => '1',
    ]);

    return ProductPlan::create(array_merge([
        'product_plan_name' => '1GB Monthly',
        'product_plan_category_id' => $category->id,
        'automation_id' => $automation->id,
        'automation_product_plan_id' => 'DEFAULT-PLAN-101',
        'api_id' => 'API-PLAN-9001',
        'cost_price' => 400,
        'default_selling_price' => 450,
        'user_level_1_selling_price' => 450,
        'validity_in_days' => 30,
        'visibility' => '1',
    ], $overrides));
}

function providerPerformanceTransaction(ProductPlan $plan, Automation $automation, string $status, DateTimeInterface $createdAt): Transaction
{
    $user = User::factory()->create();

    $transaction = Transaction::create([
        'user_id' => $user->id,
        'product_plan_id' => $plan->id,
        'automation_id' => $automation->id,
        'transaction_category' => 'data',
        'status' => $status,
        'wallet_category' => 'main_wallet',
        'phone_number' => '08030000000',
        'amount' => '450',
        'balance_before' => '1000',
        'balance_after' => $status === '1' ? '550' : '1000',
        'description' => 'Provider performance fixture',
    ]);
    $transaction->timestamps = false;
    $transaction->created_at = $createdAt;
    $transaction->updated_at = $createdAt;
    $transaction->save();

    return $transaction;
}

it('shows API IDs, providers from both sources, and the best provider over the last 30 days', function () {
    $admin = productPlanAdmin();
    $defaultProvider = Automation::create([
        'automation_name' => 'Legacy Direct',
        'slug' => 'legacy-direct',
        'domain_url' => 'https://legacy.test',
    ]);
    $configuredProvider = Automation::create([
        'automation_name' => 'Fast Provider',
        'slug' => 'fast-provider',
        'domain_url' => 'https://fast.test',
    ]);
    $plan = adminProductPlanFixture($defaultProvider);

    AutomationProductPlan::create([
        'product_plan_id' => $plan->id,
        'automation_id' => $configuredProvider->id,
        'provider_plan_id' => 'CONFIGURED-PLAN-202',
        'priority' => 1,
        'cost_price' => 390,
        'is_active' => true,
    ]);

    foreach (['1', '1', '1', '-1'] as $status) {
        providerPerformanceTransaction($plan, $defaultProvider, $status, now()->subDays(5));
    }
    foreach (['1', '1', '1', '1', '-1'] as $status) {
        providerPerformanceTransaction($plan, $configuredProvider, $status, now()->subDays(3));
    }
    providerPerformanceTransaction($plan, $defaultProvider, '1', now()->subDays(31));

    actingAs($admin);

    get(route('admin.product_plans.index2'))
        ->assertOk()
        ->assertSee('API-PLAN-9001')
        ->assertSee('Legacy Direct')
        ->assertSee('DEFAULT-PLAN-101')
        ->assertSee('Fast Provider')
        ->assertSee('CONFIGURED-PLAN-202')
        ->assertSee('Best provider · 30 days')
        ->assertSee('80.0% · 4/5');
});

it('uses 500 as the default product plan page size', function () {
    actingAs(productPlanAdmin());

    get(route('admin.product_plans.index2'))
        ->assertOk()
        ->assertViewHas('data', fn ($plans): bool => $plans->perPage() === 500)
        ->assertSee('value="500" selected', false);
});

it('falls back to 500 when the requested product plan page size is unsupported', function () {
    actingAs(productPlanAdmin());

    get(route('admin.product_plans.index2', ['per_page' => 5000]))
        ->assertOk()
        ->assertViewHas('data', fn ($plans): bool => $plans->perPage() === 500);
});

it('filters product plans by ON and OFF visibility', function () {
    $admin = productPlanAdmin();
    $automation = Automation::create([
        'automation_name' => 'Visibility Provider',
        'slug' => 'visibility-provider',
        'domain_url' => 'https://visibility.test',
    ]);
    adminProductPlanFixture($automation, [
        'product_plan_name' => 'Visible Plan',
        'api_id' => 'VISIBLE-API-ID',
        'visibility' => '1',
    ]);
    adminProductPlanFixture($automation, [
        'product_plan_name' => 'Hidden Plan',
        'api_id' => 'HIDDEN-API-ID',
        'visibility' => '0',
    ]);
    actingAs($admin);

    get(route('admin.product_plans.index2', ['visibility' => '1']))
        ->assertOk()
        ->assertSee('VISIBLE-API-ID')
        ->assertDontSee('HIDDEN-API-ID');

    get(route('admin.product_plans.index2', ['visibility' => '0']))
        ->assertOk()
        ->assertSee('HIDDEN-API-ID')
        ->assertDontSee('VISIBLE-API-ID');
});

it('filters plans by whether provider transactions were tracked in the last 30 days', function () {
    $admin = productPlanAdmin();
    $automation = Automation::create([
        'automation_name' => 'Tracking Provider',
        'slug' => 'tracking-provider',
        'domain_url' => 'https://tracking.test',
    ]);
    $tracked = adminProductPlanFixture($automation, [
        'product_plan_name' => 'Tracked Plan',
        'api_id' => 'HAS-TRACKING-API-ID',
    ]);
    $untracked = adminProductPlanFixture($automation, [
        'product_plan_name' => 'Untracked Plan',
        'api_id' => 'NO-TRACKING-API-ID',
    ]);
    providerPerformanceTransaction($tracked, $automation, '1', now()->subDays(2));
    providerPerformanceTransaction($untracked, $automation, '1', now()->subDays(31));
    actingAs($admin);

    get(route('admin.product_plans.index2', ['tracking' => 'tracked']))
        ->assertOk()
        ->assertSee('HAS-TRACKING-API-ID')
        ->assertDontSee('NO-TRACKING-API-ID');

    get(route('admin.product_plans.index2', ['tracking' => 'untracked']))
        ->assertOk()
        ->assertSee('NO-TRACKING-API-ID')
        ->assertDontSee('HAS-TRACKING-API-ID');
});

it('orders plans by network then numeric data size validity and plan name by default', function () {
    $admin = productPlanAdmin();
    $automation = Automation::create([
        'automation_name' => 'Ordering Provider',
        'slug' => 'ordering-provider',
        'domain_url' => 'https://ordering.test',
    ]);

    $largeAlpha = adminProductPlanFixture($automation, [
        'product_plan_name' => 'Alpha 10GB',
        'api_id' => 'ORDER-ALPHA-10',
        'data_size_in_mb' => '10000',
        'validity_in_days' => '30',
    ]);
    $largeAlpha->product_plan_category->network->update(['network_name' => 'Airtel']);

    $smallAlpha = adminProductPlanFixture($automation, [
        'product_plan_name' => 'Alpha 2GB',
        'api_id' => 'ORDER-ALPHA-2',
        'data_size_in_mb' => '2000',
        'validity_in_days' => '30',
    ]);
    $smallAlpha->product_plan_category->network->update(['network_name' => 'Airtel']);

    $zulu = adminProductPlanFixture($automation, [
        'product_plan_name' => 'Zulu 1GB',
        'api_id' => 'ORDER-ZULU-1',
        'data_size_in_mb' => '1000',
        'validity_in_days' => '7',
    ]);
    $zulu->product_plan_category->network->update(['network_name' => 'MTN']);
    actingAs($admin);

    get(route('admin.product_plans.index2'))
        ->assertOk()
        ->assertSeeInOrder(['ORDER-ALPHA-2', 'ORDER-ALPHA-10', 'ORDER-ZULU-1']);
});

it('places compact provider columns immediately after API ID', function () {
    actingAs(productPlanAdmin());

    $content = get(route('admin.product_plans.index2'))->assertOk()->getContent();

    expect($content)->toMatch('/<th>API ID<\/th>\s*<th>Providers<\/th>\s*<th>Best provider · 30 days<\/th>/')
        ->and($content)->not->toContain('min-w-[260px]')
        ->and($content)->not->toContain('text-lg font-bold text-green-600');
});

it('renders plan management as modal-only content while preserving the standalone page', function () {
    $admin = productPlanAdmin();
    $automation = Automation::create([
        'automation_name' => 'Manage Provider',
        'slug' => 'manage-provider',
        'domain_url' => 'https://manage.test',
    ]);
    $plan = adminProductPlanFixture($automation);
    actingAs($admin);

    get(route('admin.product_plans.manage', ['id' => $plan->id, 'modal' => 1]))
        ->assertOk()
        ->assertSee('Edit Product Plan')
        ->assertDontSee('<!DOCTYPE html>', false)
        ->assertDontSee('Back to Product Plans');

    get(route('admin.product_plans.manage', $plan->id))
        ->assertOk()
        ->assertSee('<!DOCTYPE html>', false)
        ->assertSee('Back to Product Plans');
});

it('renders one lazy management modal shell for the product plan list', function () {
    $admin = productPlanAdmin();
    $automation = Automation::create([
        'automation_name' => 'List Provider',
        'slug' => 'list-provider',
        'domain_url' => 'https://list.test',
    ]);
    $plan = adminProductPlanFixture($automation);
    actingAs($admin);

    $response = get(route('admin.product_plans.index2'))->assertOk();
    $content = $response->getContent();

    expect(substr_count($content, 'id="manage-plan-modal"'))->toBe(1)
        ->and($content)->toContain('data-manage-plan')
        ->and($content)->toContain(route('admin.product_plans.manage', ['id' => $plan->id, 'modal' => 1]));
});

it('supports ajax updates for every product plan management action', function () {
    $admin = productPlanAdmin();
    $automation = Automation::create([
        'automation_name' => 'AJAX Default Provider',
        'slug' => 'ajax-default-provider',
        'domain_url' => 'https://ajax-default.test',
    ]);
    $secondAutomation = Automation::create([
        'automation_name' => 'AJAX Secondary Provider',
        'slug' => 'ajax-secondary-provider',
        'domain_url' => 'https://ajax-secondary.test',
    ]);
    $plan = adminProductPlanFixture($automation);
    actingAs($admin);

    get(route('admin.product_plans.manage', ['id' => $plan->id, 'modal' => 1]))
        ->assertOk()
        ->assertSee('data-ajax-plan-form', false)
        ->assertSee('data-ajax-submit', false)
        ->assertSee('data-ajax-feedback', false);

    putJson(route('admin.product_plans.update_product_plan_new', $plan->id), [
        'automation_id' => $automation->id,
        'automation_product_plan_id' => 'AJAX-PLAN-101',
        'product_plan_name' => 'AJAX Updated Plan',
        'data_size_in_mb' => 1000,
        'validity_in_days' => 7,
        'cost_price' => 350,
        'is_visible' => 1,
        'product_plan_category_id' => $plan->product_plan_category_id,
        ...collect(range(1, 7))->mapWithKeys(fn (int $level) => [
            "user_level_{$level}_selling_price" => 440 - ($level * 10),
        ])->all(),
    ])->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Product plan updated successfully.');

    putJson(route('admin.product_plans.update_selling_prices', $plan->id),
        collect(range(1, 7))->mapWithKeys(fn (int $level) => [
            "user_level_{$level}_selling_price" => 450 - ($level * 10),
        ])->all()
    )->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Selling prices updated successfully.');

    $createResponse = postJson(route('admin.automation-product-plans.store'), [
        'product_plan_id' => $plan->id,
        'automation_id' => $secondAutomation->id,
        'priority' => 2,
        'cost_price' => 340,
        'provider_plan_id' => 'AJAX-PROVIDER-202',
        'is_active' => true,
    ])->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Provider added successfully.');

    putJson(route('admin.automation-product-plans.update', $createResponse->json('provider_id')), [
        'priority' => 1,
        'cost_price' => 345,
        'provider_plan_id' => 'AJAX-PROVIDER-UPDATED',
        'is_active' => false,
    ])->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Provider updated successfully.');
});
