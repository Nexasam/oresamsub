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
        'api_id' => '1',
        'visibility' => '1',
    ]);
    $category = ProductPlanCategory::create([
        'product_plan_category_name' => 'MTN SME',
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
        ->assertSee('80.0%')
        ->assertSee('4 / 5 successful');
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
