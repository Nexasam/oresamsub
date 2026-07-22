<?php

use App\Models\FundingOption;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserVirtualAccount;

use function Pest\Laravel\getJson;

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
    $fundingOption = FundingOption::create(['funding_option_name' => 'Test Funding', 'slug' => 'test-funding', 'activation_status' => '1']);
    UserVirtualAccount::create([
        'user_id' => $user->id, 'funding_option_id' => $fundingOption->id, 'bank_name' => 'Test Bank',
        'account_name' => 'Mobile Customer', 'account_number' => '1234567890', 'bvn' => 'sensitive',
    ]);
    UserVirtualAccount::create([
        'user_id' => $other->id, 'funding_option_id' => $fundingOption->id, 'bank_name' => 'Other Bank',
        'account_name' => 'Other Customer', 'account_number' => '9999999999',
    ]);

    getJson('/api/mobile/v1/wallet', authenticatedMobileHeaders($user))
        ->assertOk()->assertJsonPath('data.balance', 1234.56)->assertJsonPath('data.accounts_count', 1);

    getJson('/api/mobile/v1/wallet/accounts', authenticatedMobileHeaders($user))
        ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.account_number', '1234567890')
        ->assertJsonMissingPath('data.0.bvn')->assertJsonMissing(['account_number' => '9999999999']);
});
