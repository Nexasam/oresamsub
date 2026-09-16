<?php

use App\Models\Automation;
use App\Models\AutomationWalletFunding;
use App\Models\FundingOption;
use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\ProductPlanCategory;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Automation\AutomationBalanceResolver;
use App\Services\Automation\WalletAutoFundingService;
use App\Services\Securewave\SecurewaveClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

function fundingAutomation(array $overrides = []): Automation
{
    return Automation::create(array_merge([
        'automation_name' => 'Affatech',
        'slug' => 'affatech-'.Str::lower(Str::random(6)),
        'automation_group' => 'nil',
        'domain_url' => 'https://provider.example',
    ], $overrides));
}

function fundingConfig(Automation $automation, array $overrides = []): AutomationWalletFunding
{
    return AutomationWalletFunding::create(array_merge([
        'automation_id' => $automation->id,
        'threshold' => 1000,
        'amount_to_fund' => 3000,
        'default_balance' => 5000,
        'last_balance' => 5000,
        'balance_response_path' => 'data.balance_after',
        'customer_first_name' => 'Affa',
        'customer_last_name' => 'Tech',
        'customer_phone_number' => '08012345678',
        'bank_code' => '1',
        'provider_bank_name' => 'Provider Bank',
        'provider_bank_code' => '058',
        'provider_account_name' => 'Affatech Limited',
        'provider_account_number' => '0123456789',
        'automatic_funding' => true,
        'active' => 'yes',
    ], $overrides));
}

function securewaveOption(): FundingOption
{
    return FundingOption::create([
        'funding_option_name' => 'Securewave',
        'slug' => 'securewaveng',
        'api_public_key' => 'public-key',
        'api_secret_key' => 'secret-key',
        'contract_code' => 'BUSINESS-123',
        'virtual_account_id_number' => '12345678901',
        'activation_status' => '1',
    ]);
}

function walletFundingAdmin(): User
{
    $role = Role::firstOrCreate(['role_name' => 'Admin']);

    return User::factory()->create(['role_id' => $role->id]);
}

function providerTransaction(Automation $automation, string $status, string $response, string $createdAt): string
{
    $id = (string) Str::uuid();
    $user = User::factory()->create();
    $product = Product::create(['slug' => 'data', 'product_name' => 'Data']);
    $category = ProductPlanCategory::create([
        'product_plan_category_name' => 'Balance test '.Str::uuid(),
        'automation_id' => $automation->id,
        'product_id' => $product->id,
    ]);
    $plan = ProductPlan::create([
        'product_plan_name' => 'Balance test plan',
        'product_plan_category_id' => $category->id,
        'automation_product_plan_id' => (string) Str::uuid(),
        'automation_id' => $automation->id,
        'default_selling_price' => '100',
    ]);

    DB::table('transactions')->insert([
        'id' => $id,
        'user_id' => $user->id,
        'product_plan_id' => $plan->id,
        'automation_id' => $automation->id,
        'status' => $status,
        'wallet_category' => 'main_wallet',
        'amount' => '100',
        'balance_before' => '1000',
        'balance_after' => '900',
        'description' => 'Test purchase',
        'admin_screen_message' => $response,
        'created_at' => $createdAt,
        'updated_at' => $createdAt,
    ]);

    return $id;
}

it('stores one funding configuration and exposes it from the automation', function () {
    $automation = fundingAutomation();
    $funding = fundingConfig($automation, [
        'balance_source' => 'default',
        'last_balance_synced_at' => now(),
    ]);

    expect($automation->fresh()->walletFunding->is($funding))->toBeTrue()
        ->and($funding->default_balance)->toBe('5000.00')
        ->and($funding->last_balance_synced_at)->not->toBeNull();
});

it('uses the newest successful transaction containing a numeric configured balance', function () {
    $automation = fundingAutomation();
    $funding = fundingConfig($automation);

    providerTransaction($automation, '1', json_encode(['data' => ['balance_after' => 1800]]), '2026-09-15 10:00:00');
    providerTransaction($automation, '-1', json_encode(['data' => ['balance_after' => 50]]), '2026-09-15 11:00:00');
    providerTransaction($automation, '1', json_encode(['data' => ['other' => 700]]), '2026-09-15 12:00:00');
    $sourceId = providerTransaction($automation, '1', json_encode(['data' => ['balance_after' => '1250.75']]), '2026-09-15 13:00:00');

    $synced = app(AutomationBalanceResolver::class)->sync($funding);

    expect($synced)->toBeTrue()
        ->and($funding->fresh()->last_balance)->toBe('1250.75')
        ->and($funding->fresh()->balance_source)->toBe('transaction_response')
        ->and($funding->fresh()->balance_source_transaction_id)->toBe($sourceId);
});

it('keeps the confirmed balance when no successful response has a numeric configured balance', function () {
    $automation = fundingAutomation();
    $funding = fundingConfig($automation, ['last_balance' => 4200]);

    providerTransaction($automation, '1', 'not-json', '2026-09-15 10:00:00');
    providerTransaction($automation, '1', json_encode(['data' => ['balance_after' => 'unknown']]), '2026-09-15 11:00:00');

    $synced = app(AutomationBalanceResolver::class)->sync($funding);

    expect($synced)->toBeFalse()
        ->and($funding->fresh()->last_balance)->toBe('4200.00')
        ->and($funding->fresh()->last_error)->toContain('No numeric balance');
});

it('normalizes Securewave merchant balance and sends stored credentials', function () {
    securewaveOption();
    Http::fake([
        'securewaveng.com/api/balance' => Http::response([
            'status' => true,
            'message' => 'Successful',
            'data' => ['balance' => '9500.50'],
        ]),
    ]);

    $result = app(SecurewaveClient::class)->merchantBalance();

    expect($result['ok'])->toBeTrue()
        ->and($result['balance'])->toBe(9500.50);

    Http::assertSent(fn ($request) => $request->url() === 'https://securewaveng.com/api/balance'
        && $request->hasHeader('Authorization', 'Bearer secret-key')
        && $request->hasHeader('x-api-key', 'public-key'));
});

it('creates and funds a Securewave customer with normalized balances', function () {
    securewaveOption();
    Http::fake([
        'securewaveng.com/api/virtual_accounts/generate' => Http::response([
            'status' => true,
            'data' => [[
                'account_reference' => 'CUS-123',
                'account_number' => '0123456789',
                'account_name' => 'Affa Tech',
                'bank_name' => 'Test Bank',
                'bank_code' => 1,
                'status' => 1,
            ]],
        ]),
        'securewaveng.com/api/customer_withdrawals/withdraw' => Http::response([
            'status' => true,
            'message' => 'Funded',
            'data' => ['balance_after' => '4200'],
        ]),
    ]);

    $customer = app(SecurewaveClient::class)->createCustomer(
        'Affa', 'Tech', 'affatech@example.com', '08012345678', '1', 'AUTOMATION-123'
    );
    $funding = app(SecurewaveClient::class)->fundCustomer('affatech@example.com', 3000);

    expect($customer['ok'])->toBeTrue()
        ->and($customer['customer_reference'])->toBe('CUS-123')
        ->and($customer['account']['account_number'])->toBe('0123456789')
        ->and($funding['ok'])->toBeTrue()
        ->and($funding['customer_balance'])->toBe(4200.0);

    Http::assertSent(fn ($request) => $request->url() === 'https://securewaveng.com/api/customer_withdrawals/withdraw'
        && $request['customer_email'] === 'affatech@example.com'
        && (float) $request['amount'] === 3000.0);

    Http::assertSent(fn ($request) => $request->url() === 'https://securewaveng.com/api/virtual_accounts/generate'
        && $request['email'] === 'affatech@example.com'
        && $request['first_name'] === 'Affa'
        && $request['last_name'] === 'Tech'
        && $request['phone_number'] === '08012345678'
        && $request['bank_code'] === [1]
        && $request['business_id'] === 'BUSINESS-123'
        && $request['account_type'] === 'static'
        && $request['id_type'] === 'bvn'
        && $request['id_number'] === '12345678901'
        && $request['metadata']['external_customer_id'] === 'AUTOMATION-123');
});

it('encrypts the shared Securewave BVN at rest', function () {
    $option = securewaveOption();

    expect($option->virtual_account_id_number)->toBe('12345678901')
        ->and(DB::table('funding_options')->where('id', $option->id)->value('virtual_account_id_number'))
        ->not->toBe('12345678901');
});

it('does not normalize malformed or failed Securewave responses as success', function () {
    securewaveOption();
    Http::fake([
        'securewaveng.com/api/balance' => Http::response('provider unavailable', 503),
        'securewaveng.com/api/customer_withdrawals/withdraw' => Http::response([
            'status' => false,
            'message' => 'Insufficient funds',
        ], 200),
    ]);

    expect(app(SecurewaveClient::class)->merchantBalance()['ok'])->toBeFalse()
        ->and(app(SecurewaveClient::class)->fundCustomer('affatech@example.com', 3000)['ok'])->toBeFalse();
});

it('does not fund when the Securewave merchant balance is insufficient', function () {
    securewaveOption();
    $funding = fundingConfig(fundingAutomation(), [
        'linked_customer_email' => 'affatech@example.com',
        'securewave_customer_created_at' => now(),
        'securewave_bank_info_saved_at' => now(),
        'last_balance' => 500,
    ]);
    Http::fake([
        'securewaveng.com/api/balance' => Http::response(['status' => true, 'data' => ['balance' => 1000]]),
        'securewaveng.com/api/customer_withdrawals/withdraw' => Http::response(['status' => true]),
    ]);

    $result = app(WalletAutoFundingService::class)->fund($funding, 3000, 'manual');

    expect($result['ok'])->toBeFalse()
        ->and($funding->fresh()->last_balance)->toBe('500.00')
        ->and($funding->fresh()->last_error)->toContain('Insufficient');
    Http::assertNotSent(fn ($request) => str_contains($request->url(), 'customer_withdrawals'));
});

it('does not fund an email that has not been provisioned as a Securewave customer', function () {
    securewaveOption();
    $funding = fundingConfig(fundingAutomation(), [
        'linked_customer_email' => 'new-customer@example.com',
        'securewave_customer_created_at' => null,
    ]);
    Http::fake();

    $result = app(WalletAutoFundingService::class)->fund($funding, 3000, 'manual');

    expect($result['ok'])->toBeFalse()
        ->and($result['message'])->toContain('Create the Securewave customer');
    Http::assertNothingSent();
});

it('prefers the confirmed Securewave customer balance after funding', function () {
    securewaveOption();
    $funding = fundingConfig(fundingAutomation(), [
        'linked_customer_email' => 'affatech@example.com',
        'securewave_customer_created_at' => now(),
        'securewave_bank_info_saved_at' => now(),
        'last_balance' => 500,
    ]);
    Http::fake([
        'securewaveng.com/api/balance' => Http::response(['status' => true, 'data' => ['balance' => 10000]]),
        'securewaveng.com/api/customer_withdrawals/withdraw' => Http::response([
            'status' => true,
            'data' => ['balance_after' => 3750],
        ]),
    ]);

    $result = app(WalletAutoFundingService::class)->fund($funding, 3000, 'manual');

    expect($result['ok'])->toBeTrue()
        ->and($funding->fresh()->last_balance)->toBe('3750.00')
        ->and($funding->fresh()->balance_source)->toBe('securewave_funding')
        ->and($funding->fresh()->last_funded_at)->not->toBeNull();
});

it('adds a confirmed funded amount when Securewave omits customer balance', function () {
    securewaveOption();
    $funding = fundingConfig(fundingAutomation(), [
        'linked_customer_email' => 'affatech@example.com',
        'securewave_customer_created_at' => now(),
        'securewave_bank_info_saved_at' => now(),
        'last_balance' => 500,
    ]);
    Http::fake([
        'securewaveng.com/api/balance' => Http::response(['status' => true, 'data' => ['balance' => 10000]]),
        'securewaveng.com/api/customer_withdrawals/withdraw' => Http::response(['status' => true, 'message' => 'Funded']),
    ]);

    app(WalletAutoFundingService::class)->fund($funding, 3000, 'automatic');

    expect($funding->fresh()->last_balance)->toBe('3500.00')
        ->and($funding->fresh()->balance_source)->toBe('securewave_funding');
});

it('skips automatic funding when disabled or above threshold', function () {
    securewaveOption();
    $disabled = fundingConfig(fundingAutomation(), [
        'linked_customer_email' => 'disabled@example.com',
        'automatic_funding' => false,
        'last_balance' => 100,
    ]);
    $healthy = fundingConfig(fundingAutomation(), [
        'linked_customer_email' => 'healthy@example.com',
        'automatic_funding' => true,
        'last_balance' => 5000,
    ]);
    Http::fake();

    $disabledResult = app(WalletAutoFundingService::class)->process($disabled);
    $healthyResult = app(WalletAutoFundingService::class)->process($healthy);

    expect($disabledResult['ok'])->toBeFalse()
        ->and($disabledResult['skipped'])->toBeTrue()
        ->and($healthyResult['ok'])->toBeFalse()
        ->and($healthyResult['skipped'])->toBeTrue();
    Http::assertNothingSent();
});

it('shows every automation on the admin funding page', function () {
    $admin = walletFundingAdmin();
    fundingAutomation(['automation_name' => 'Affatech']);
    fundingAutomation(['automation_name' => 'Paultechs']);

    $this->actingAs($admin)
        ->get(route('admin.automation-funding.index'))
        ->assertOk()
        ->assertSee('Automation Wallet Funding')
        ->assertSee('Affatech')
        ->assertSee('Paultechs');
});

it('shows the last confirmed Securewave master wallet balance', function () {
    $admin = walletFundingAdmin();
    securewaveOption()->update([
        'merchant_wallet_balance' => 125000.75,
        'merchant_balance_synced_at' => '2026-09-16 10:30:00',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.automation-funding.index'))
        ->assertOk()
        ->assertSee('Securewave Master Wallet')
        ->assertSee('₦125,000.75')
        ->assertSee('Refresh Wallet Balance');
});

it('refreshes and stores the Securewave master wallet balance', function () {
    $admin = walletFundingAdmin();
    $option = securewaveOption();
    Http::fake([
        'securewaveng.com/api/balance' => Http::response([
            'status' => true,
            'message' => 'Successful',
            'data' => ['balance' => '98000.50'],
        ]),
    ]);

    $this->actingAs($admin)
        ->post(route('admin.automation-funding.refresh-merchant-balance'))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($option->fresh()->merchant_wallet_balance)->toBe('98000.50')
        ->and($option->fresh()->merchant_balance_synced_at)->not->toBeNull()
        ->and($option->fresh()->merchant_balance_error)->toBeNull();
});

it('lists the most recently updated automation funding first', function () {
    $admin = walletFundingAdmin();
    $older = fundingConfig(fundingAutomation(['automation_name' => 'Older Automation']));
    $newer = fundingConfig(fundingAutomation(['automation_name' => 'Newer Automation']));
    $older->forceFill(['updated_at' => '2026-09-14 10:00:00'])->saveQuietly();
    $newer->forceFill(['updated_at' => '2026-09-15 10:00:00'])->saveQuietly();

    $html = $this->actingAs($admin)
        ->get(route('admin.automation-funding.index'))
        ->assertOk()
        ->getContent();

    expect(strpos($html, 'Newer Automation'))->toBeLessThan(strpos($html, 'Older Automation'));
});

it('renders the funding page as a compact table with one lazy management drawer', function () {
    $admin = walletFundingAdmin();
    fundingConfig(fundingAutomation(['automation_name' => 'Affatech']));
    fundingConfig(fundingAutomation(['automation_name' => 'Paultechs']));

    $response = $this->actingAs($admin)->get(route('admin.automation-funding.index'));
    $html = $response->getContent();

    $response->assertOk()
        ->assertSee('Current Balance')
        ->assertSee('Default Funding')
        ->assertSee('Securewave Customer')
        ->assertSee('data-manage-funding', false)
        ->assertSee('automation-funding-drawer', false);

    expect(substr_count($html, 'id="automation-funding-drawer"'))->toBe(1)
        ->and($html)->not->toContain('name="balance_response_path"');
});

it('loads all controls for the selected automation in the management drawer', function () {
    $admin = walletFundingAdmin();
    $automation = fundingAutomation(['automation_name' => 'Affatech']);
    fundingConfig($automation, ['linked_customer_email' => 'affatech@example.com']);

    $this->actingAs($admin)
        ->get(route('admin.automation-funding.manage', $automation))
        ->assertOk()
        ->assertSee('Manage Affatech')
        ->assertSee('name="balance_response_path"', false)
        ->assertSee('name="customer_first_name"', false)
        ->assertSee('name="provider_account_number"', false)
        ->assertSee('Create Securewave Customer')
        ->assertSee('Sync Balance')
        ->assertSee('Correct Balance')
        ->assertSee('Turn Auto')
        ->assertSee('Fund Automation');
});

it('orders funding actions before configuration and balance correction', function () {
    $admin = walletFundingAdmin();
    $automation = fundingAutomation(['automation_name' => 'Affatech']);
    fundingConfig($automation, ['linked_customer_email' => 'affatech@example.com']);

    $html = $this->actingAs($admin)
        ->get(route('admin.automation-funding.manage', $automation))
        ->assertOk()
        ->getContent();

    expect(strpos($html, 'Funding readiness'))->toBeLessThan(strpos($html, 'Automation settings'))
        ->and(strpos($html, 'Fund Automation'))->toBeLessThan(strpos($html, 'Correct Balance'));
});

it('configures an automation with default balance threshold and response path', function () {
    $admin = walletFundingAdmin();
    $automation = fundingAutomation();

    $this->actingAs($admin)
        ->post(route('admin.automation-funding.configure', $automation), [
            'linked_customer_email' => 'affatech@example.com',
            'customer_first_name' => 'Affa',
            'customer_last_name' => 'Tech',
            'customer_phone_number' => '08012345678',
            'bank_code' => '1',
            'provider_bank_name' => 'Provider Bank',
            'provider_bank_code' => '058',
            'provider_account_name' => 'Affatech Limited',
            'provider_account_number' => '0123456789',
            'balance_response_path' => 'data.balance_after',
            'default_balance' => 8000,
            'threshold' => 1500,
            'amount_to_fund' => 5000,
            'automatic_funding' => '1',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $funding = $automation->fresh()->walletFunding;
    expect($funding->last_balance)->toBe('8000.00')
        ->and($funding->customer_first_name)->toBe('Affa')
        ->and($funding->provider_bank_name)->toBe('Provider Bank')
        ->and($funding->provider_account_number)->toBe('0123456789')
        ->and(DB::table('automation_wallet_fundings')->where('id', $funding->id)->value('provider_account_number'))
        ->not->toBe('0123456789')
        ->and($funding->threshold)->toBe('1500.00')
        ->and($funding->amount_to_fund)->toBe('5000.00')
        ->and($funding->automatic_funding)->toBeTrue();
});

it('explicitly creates the configured automation customer on Securewave', function () {
    securewaveOption();
    $admin = walletFundingAdmin();
    $funding = fundingConfig(fundingAutomation(), [
        'linked_customer_email' => 'affatech@example.com',
    ]);
    Http::fake([
        'securewaveng.com/api/virtual_accounts/generate' => Http::response([
            'status' => true,
            'data' => [[
                'account_reference' => 'CUS-AFFATECH',
                'account_number' => '0123456789',
                'account_name' => 'Affa Tech',
                'bank_name' => 'Test Bank',
                'bank_code' => 1,
                'status' => 1,
            ]],
        ]),
        'securewaveng.com/api/customer_withdrawals/bank-info' => Http::response([
            'status' => true,
            'message' => 'Customer bank info saved successfully',
            'data' => ['id' => 77],
        ]),
    ]);

    $this->actingAs($admin)
        ->post(route('admin.automation-funding.create-customer', $funding))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($funding->fresh()->securewave_customer_reference)->toBe('CUS-AFFATECH')
        ->and($funding->fresh()->securewave_account_number)->toBe('0123456789')
        ->and($funding->fresh()->securewave_bank_name)->toBe('Test Bank')
        ->and($funding->fresh()->securewave_bank_info_id)->toBe('77')
        ->and($funding->fresh()->securewave_bank_info_saved_at)->not->toBeNull()
        ->and($funding->fresh()->securewave_customer_created_at)->not->toBeNull();

    Http::assertSent(fn ($request) => $request->url() === 'https://securewaveng.com/api/customer_withdrawals/bank-info'
        && $request->hasHeader('Content-Type', 'application/json')
        && $request['customer_email'] === 'affatech@example.com'
        && $request['bank_name'] === 'Provider Bank'
        && $request['account_name'] === 'Affatech Limited'
        && $request['bank_code'] === '058'
        && $request['account_number'] === '0123456789');
});

it('blocks funding until the provider bank information is registered', function () {
    securewaveOption();
    $funding = fundingConfig(fundingAutomation(), [
        'linked_customer_email' => 'affatech@example.com',
        'securewave_customer_created_at' => now(),
        'securewave_bank_info_saved_at' => null,
    ]);
    Http::fake();

    $result = app(WalletAutoFundingService::class)->fund($funding, 3000, 'manual');

    expect($result['ok'])->toBeFalse()
        ->and($result['message'])->toContain('bank information');
    Http::assertNothingSent();
});

it('lets an admin correct balance and toggle automatic funding', function () {
    $admin = walletFundingAdmin();
    $funding = fundingConfig(fundingAutomation(), ['automatic_funding' => true]);

    $this->actingAs($admin)
        ->post(route('admin.automation-funding.correct-balance', $funding), ['last_balance' => 7777.25])
        ->assertSessionHas('success');
    $this->actingAs($admin)
        ->post(route('admin.automation-funding.toggle', $funding))
        ->assertSessionHas('success');

    expect($funding->fresh()->last_balance)->toBe('7777.25')
        ->and($funding->fresh()->balance_source)->toBe('manual')
        ->and($funding->fresh()->automatic_funding)->toBeFalse();
});

it('updates balance immediately when a successful automation transaction is saved', function () {
    $automation = fundingAutomation();
    $funding = fundingConfig($automation, ['last_balance' => 5000]);
    $seedId = providerTransaction($automation, '0', '{}', '2026-09-15 08:00:00');
    $seed = Transaction::findOrFail($seedId);

    Transaction::create([
        'user_id' => $seed->user_id,
        'product_plan_id' => $seed->product_plan_id,
        'automation_id' => $automation->id,
        'status' => '1',
        'wallet_category' => 'main_wallet',
        'amount' => '100',
        'balance_before' => '1000',
        'balance_after' => '900',
        'description' => 'Successful provider transaction',
        'admin_screen_message' => json_encode(['data' => ['balance_after' => 2222.40]]),
    ]);

    expect($funding->fresh()->last_balance)->toBe('2222.40')
        ->and($funding->fresh()->balance_source)->toBe('transaction_response');
});

it('does not let an older transaction overwrite a newer manual balance', function () {
    $automation = fundingAutomation();
    $funding = fundingConfig($automation, [
        'last_balance' => 7777,
        'balance_source' => 'manual',
        'last_balance_synced_at' => '2026-09-15 12:00:00',
    ]);
    providerTransaction($automation, '1', json_encode(['data' => ['balance_after' => 100]]), '2026-09-15 10:00:00');

    $synced = app(AutomationBalanceResolver::class)->sync($funding);

    expect($synced)->toBeFalse()
        ->and($funding->fresh()->last_balance)->toBe('7777.00');
});
