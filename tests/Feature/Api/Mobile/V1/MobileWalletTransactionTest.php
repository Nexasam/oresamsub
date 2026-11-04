<?php

use App\Models\FundingOption;
use App\Models\FundingOptionBankCodes;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserVirtualAccount;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

function authenticatedMobileHeaders(User $user): array
{
    return ['Authorization' => 'Bearer '.$user->createToken('mobile:test', ['mobile'])->plainTextToken];
}

function transactionFor(User $user, array $attributes = []): Transaction
{
    return Transaction::create(array_merge([
        'user_id' => $user->id,
        'product_plan_id' => 'test-plan',
        'transaction_category' => 'data',
        'status' => '1',
        'wallet_category' => 'main_wallet',
        'phone_number' => '08030000000',
        'amount' => '500',
        'balance_before' => '2000',
        'balance_after' => '1500',
        'description' => 'Data purchase',
        'txn_reference' => 'TEST-'.fake()->unique()->uuid(),
    ], $attributes));
}

it('paginates and filters only the authenticated users transactions', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    transactionFor($user, ['status' => '1']);
    transactionFor($user, ['status' => '0']);
    transactionFor($other, ['status' => '1']);

    getJson('/api/mobile/v1/transactions?status=successful&per_page=5', authenticatedMobileHeaders($user))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.status', 'successful');
});

it('prevents cross-user transaction detail and receipt access', function () {
    $owner = User::factory()->create();
    $attacker = User::factory()->create();
    $transaction = transactionFor($owner);

    getJson("/api/mobile/v1/transactions/{$transaction->id}", authenticatedMobileHeaders($attacker))->assertNotFound();
    getJson("/api/mobile/v1/transactions/{$transaction->id}/receipt", authenticatedMobileHeaders($attacker))->assertNotFound();

    app('auth')->forgetGuards();
    getJson("/api/mobile/v1/transactions/{$transaction->id}/receipt", authenticatedMobileHeaders($owner))
        ->assertOk()->assertJsonPath('data.receipt.reference', $transaction->txn_reference);
});

it('returns wallet accounts without sensitive gateway fields and scopes them to the user', function () {
    $user = User::factory()->create(['main_wallet' => '1234.56']);
    $other = User::factory()->create();
    $fundingOption = FundingOption::create(['funding_option_name' => 'Xixapay', 'slug' => 'xixapay', 'activation_status' => '1']);
    $hiddenOption = FundingOption::create(['funding_option_name' => 'Another provider', 'slug' => 'another-provider', 'activation_status' => '1']);
    UserVirtualAccount::create([
        'user_id' => $user->id, 'funding_option_id' => $fundingOption->id, 'funding_slug' => 'xixapay', 'bank_name' => 'Test Bank',
        'account_name' => 'Mobile Customer', 'account_number' => '1234567890', 'bvn' => 'sensitive',
    ]);
    UserVirtualAccount::create([
        'user_id' => $user->id, 'funding_option_id' => $hiddenOption->id, 'funding_slug' => 'another-provider', 'bank_name' => 'Hidden Bank',
        'account_name' => 'Mobile Customer', 'account_number' => '1111111111',
    ]);
    UserVirtualAccount::create([
        'user_id' => $other->id, 'funding_option_id' => $fundingOption->id, 'funding_slug' => 'xixapay', 'bank_name' => 'Other Bank',
        'account_name' => 'Other Customer', 'account_number' => '9999999999',
    ]);

    getJson('/api/mobile/v1/wallet', authenticatedMobileHeaders($user))
        ->assertOk()->assertJsonPath('data.balance', 1234.56)->assertJsonPath('data.accounts_count', 1);

    $accountsResponse = getJson('/api/mobile/v1/wallet/accounts', authenticatedMobileHeaders($user))
        ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.account_number', '1234567890')
        ->assertJsonMissingPath('data.0.bvn')->assertJsonMissingPath('data.0.provider')
        ->assertJsonMissing(['account_number' => '1111111111'])
        ->assertJsonMissing(['account_number' => '9999999999']);

    expect(strtolower($accountsResponse->getContent()))->not->toContain('crystal');
});

it('returns only Xixapay as a mobile virtual account funding option', function () {
    $user = User::factory()->create();
    $option = FundingOption::create([
        'funding_option_name' => 'Xixapay',
        'slug' => 'xixapay',
        'activation_status' => '1',
    ]);
    $hiddenOption = FundingOption::create([
        'funding_option_name' => 'Crystal Pay',
        'slug' => 'crystal_pay',
        'activation_status' => '1',
    ]);
    FundingOptionBankCodes::create([
        'funding_option_id' => $option->id,
        'bank_code' => 'wema',
        'visibility_status' => '1',
        'short_description' => 'Xixapay Wema account',
    ]);
    FundingOptionBankCodes::create([
        'funding_option_id' => $hiddenOption->id,
        'bank_code' => 'hidden-bank',
        'visibility_status' => '1',
        'short_description' => 'CrystalPay hidden account',
    ]);

    $response = getJson('/api/mobile/v1/wallet/funding-options', authenticatedMobileHeaders($user))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $option->id)
        ->assertJsonPath('data.0.name', 'Bank transfer')
        ->assertJsonPath('data.0.banks.0.description', 'bank transfer Wema account')
        ->assertJsonMissingPath('data.0.slug');

    expect(strtolower($response->getContent()))
        ->not->toContain('crystal')
        ->not->toContain('xixapay')
        ->not->toContain('hidden-bank');
});

it('creates mobile virtual accounts through Xixapay and rejects another provider', function () {
    Http::fake([
        'api.xixapay.com/api/v1/createVirtualAccount' => Http::response([
            'status' => 'success',
            'bankAccounts' => [[
                'bankName' => 'Wema Bank',
                'bankCode' => 'wema',
                'accountName' => 'OresamSub - Mobile Customer',
                'accountNumber' => '1234567890',
                'Reserved_Account_Id' => 'xixa-reserved-1',
            ]],
        ]),
    ]);
    $user = User::factory()->create(['pin' => '1234', 'phone_number' => '08031234567']);
    $xixapay = FundingOption::create([
        'funding_option_name' => 'Xixapay',
        'slug' => 'xixapay',
        'activation_status' => '1',
        'api_secret_key' => 'xixa-secret',
        'api_public_key' => 'xixa-public',
        'contract_code' => 'xixa-business',
        'biz_bvn' => '12345678901',
    ]);
    $other = FundingOption::create([
        'funding_option_name' => 'Other provider',
        'slug' => 'other-provider',
        'activation_status' => '1',
    ]);
    foreach ([$xixapay, $other] as $option) {
        FundingOptionBankCodes::create([
            'funding_option_id' => $option->id,
            'bank_code' => 'wema',
            'visibility_status' => '1',
        ]);
    }

    postJson('/api/mobile/v1/wallet/accounts', [
        'funding_option_id' => $other->id,
        'bank_code' => 'wema',
        'pin' => '1234',
    ], authenticatedMobileHeaders($user))->assertUnprocessable();

    postJson('/api/mobile/v1/wallet/accounts', [
        'funding_option_id' => $xixapay->id,
        'bank_code' => 'wema',
        'pin' => '1234',
    ], authenticatedMobileHeaders($user))
        ->assertCreated()
        ->assertJsonPath('data.account.account_number', '1234567890');

    Http::assertSentCount(1);
    expect(UserVirtualAccount::where('user_id', $user->id)->get())
        ->toHaveCount(1)
        ->first()->funding_slug->toBe('xixapay');
});

it('never exposes provider html pages in mobile transaction messages', function () {
    $user = User::factory()->create();
    $transaction = transactionFor($user, [
        'status' => '-1',
        'user_screen_message' => '<!doctype html><html><head><style>body{color:red}</style></head><body>Gateway failure</body></html>',
    ]);

    $response = getJson("/api/mobile/v1/transactions/{$transaction->id}", authenticatedMobileHeaders($user))
        ->assertOk()
        ->assertJsonPath('data.transaction.message', 'This transaction could not be completed.');

    expect(strtolower($response->getContent()))->not->toContain('<html')
        ->not->toContain('<style');
});
