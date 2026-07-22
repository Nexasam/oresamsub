<?php

use App\Models\Transaction;
use App\Models\User;

use function Pest\Laravel\getJson;
use function Pest\Laravel\putJson;

function mobileHeadersFor(User $user): array
{
    return ['Authorization' => 'Bearer '.$user->createToken('mobile:test', ['mobile'])->plainTextToken];
}

it('returns wallet summary and only safe recent transaction fields', function () {
    $user = User::factory()->create(['main_wallet' => '1250.50']);
    Transaction::create([
        'user_id' => $user->id,
        'product_plan_id' => 'test-plan',
        'transaction_category' => 'data',
        'status' => '1',
        'wallet_category' => 'main_wallet',
        'phone_number' => '08030000000',
        'amount' => '500',
        'balance_before' => '1750.50',
        'balance_after' => '1250.50',
        'description' => 'Data purchase',
        'user_screen_message' => 'Purchase successful',
        'admin_screen_message' => 'private provider diagnostic',
    ]);

    getJson('/api/mobile/v1/dashboard', mobileHeadersFor($user))
        ->assertOk()
        ->assertJsonPath('data.wallet.balance', 1250.5)
        ->assertJsonPath('data.summary.total_transactions', 1)
        ->assertJsonPath('data.recent_transactions.0.status', 'successful')
        ->assertJsonMissing(['admin_screen_message' => 'private provider diagnostic'])
        ->assertJsonStructure(['data' => ['wallet', 'summary', 'recent_transactions']]);
});

it('shows and updates only the authenticated user profile', function () {
    $user = User::factory()->create(['username' => 'before_name']);

    getJson('/api/mobile/v1/profile', mobileHeadersFor($user))
        ->assertOk()
        ->assertJsonPath('data.user.id', $user->id)
        ->assertJsonMissingPath('data.user.pin')
        ->assertJsonMissingPath('data.user.main_wallet');

    putJson('/api/mobile/v1/profile', [
        'first_name' => 'Updated',
        'last_name' => 'Customer',
        'other_names' => 'Mobile',
        'username' => 'updated_customer',
        'customer_landmark' => 'Near Central Market',
    ], mobileHeadersFor($user))
        ->assertOk()
        ->assertJsonPath('data.user.first_name', 'Updated')
        ->assertJsonPath('data.user.username', 'updated_customer');

    expect($user->fresh()->customer_landmark)->toBe('Near Central Market');
});

it('requires authentication for private mobile bootstrap endpoints', function () {
    getJson('/api/mobile/v1/dashboard')->assertUnauthorized();
    getJson('/api/mobile/v1/profile')->assertUnauthorized();
});
