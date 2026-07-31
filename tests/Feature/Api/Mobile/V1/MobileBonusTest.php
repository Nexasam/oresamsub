<?php

use App\Models\Bonus;
use App\Models\BonusEntitlement;
use App\Models\BonusLog;
use App\Models\FundingOption;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\UplineFundingBonusLog;
use App\Models\UplineFundingBonusSetting;
use App\Models\User;
use App\Models\UserPlan;
use App\Services\BonusService;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\postJson;

function bonusCampaign(array $attributes = []): Bonus
{
    return Bonus::create(array_merge([
        'title' => 'Welcome to OresamSub '.fake()->unique()->numerify('####'),
        'status' => true,
        'group' => Bonus::GROUP_NEW_REGISTRATION,
        'enjoyment' => [Bonus::ENJOYMENT_WALLET],
        'conditions' => null,
        'funding_type' => null,
        'funding_value' => 0,
        'funding_cap' => null,
        'bonus_wallet_amount' => 500,
        'funding_whitelist' => null,
        'frequency_per_user' => 1,
        'max_rewards_per_ip' => 1,
        'max_rewards_per_device' => 1,
        'reward_valid_days' => 14,
        'priority' => 0,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addMonth(),
    ], $attributes));
}

it('awards a new registration only after email verification and remains idempotent on login', function () {
    Notification::fake();
    Role::create(['role_name' => 'User']);
    UserPlan::create([
        'user_plan_name' => 'Default',
        'plan_level' => 1,
        'is_default' => 1,
        'visibility' => 1,
    ]);
    $bonus = bonusCampaign();
    $headers = ['X-Device-ID' => 'team-phone-001'];

    postJson('/api/mobile/v1/auth/register', [
        'first_name' => 'Bonus',
        'last_name' => 'Tester',
        'username' => 'bonus_tester',
        'email' => 'bonus-tester@example.com',
        'password' => 'SecurePass123!',
        'password_confirmation' => 'SecurePass123!',
        'device_name' => 'Test Android',
        'terms_accepted' => true,
    ], $headers)->assertCreated();

    $user = User::where('email', 'bonus-tester@example.com')->firstOrFail();
    expect($user->bonus_wallet)->toBe('0.00')
        ->and($user->registration_ip)->not->toBeNull()
        ->and($user->registration_device_hash)->toBe(hash('sha256', 'team-phone-001'))
        ->and(BonusEntitlement::count())->toBe(0);

    $user->forceFill(['email_verified_at' => now()])->save();
    $payload = [
        'login' => $user->email,
        'password' => 'SecurePass123!',
        'device_name' => 'Test Android',
    ];

    postJson('/api/mobile/v1/auth/login', $payload, $headers)->assertOk();
    postJson('/api/mobile/v1/auth/login', $payload, $headers)->assertOk();

    expect((float) $user->fresh()->bonus_wallet)->toBe(500.0)
        ->and(BonusEntitlement::where('bonus_id', $bonus->id)->where('user_id', $user->id)->count())->toBe(1)
        ->and(BonusLog::where('event_type', 'entitlement_granted')->count())->toBe(1);
});

it('denies duplicate registration rewards by IP or device without blocking the accounts', function () {
    $bonus = bonusCampaign();
    $service = app(BonusService::class);
    $first = User::factory()->create([
        'registration_ip' => '102.89.1.10',
        'registration_device_hash' => hash('sha256', 'phone-a'),
    ]);
    $sameIp = User::factory()->create([
        'registration_ip' => '102.89.1.10',
        'registration_device_hash' => hash('sha256', 'phone-b'),
    ]);
    $sameDevice = User::factory()->create([
        'registration_ip' => '102.89.1.11',
        'registration_device_hash' => hash('sha256', 'phone-a'),
    ]);

    expect($service->evaluate($first))->toHaveCount(1)
        ->and($service->evaluate($sameIp))->toHaveCount(0)
        ->and($service->evaluate($sameDevice))->toHaveCount(0)
        ->and(User::whereKey($sameIp->id)->exists())->toBeTrue()
        ->and(User::whereKey($sameDevice->id)->exists())->toBeTrue()
        ->and(BonusEntitlement::where('bonus_id', $bonus->id)->count())->toBe(1)
        ->and(BonusLog::where('event_type', 'eligibility_rejected')->count())->toBe(2);

    expect(BonusLog::where('user_id', $sameIp->id)->firstOrFail()->metadata['reason'])
        ->toBe('ip_reward_limit_reached')
        ->and(BonusLog::where('user_id', $sameDevice->id)->firstOrFail()->metadata['reason'])
        ->toBe('device_reward_limit_reached');
});

it('awards dormant customers based on their last successful transaction only', function () {
    $bonus = bonusCampaign([
        'group' => Bonus::GROUP_DORMANT_CUSTOMER,
        'conditions' => ['dormant_days' => 15],
        'max_rewards_per_ip' => null,
        'max_rewards_per_device' => null,
    ]);
    $dormant = User::factory()->create();
    $active = User::factory()->create();
    $neverTransacted = User::factory()->create();

    foreach ([[$dormant, now()->subDays(20)], [$active, now()->subDays(3)]] as [$user, $createdAt]) {
        Transaction::create([
            'user_id' => $user->id,
            'product_plan_id' => 'test-plan',
            'transaction_category' => 'data',
            'status' => '1',
            'wallet_category' => 'main_wallet',
            'amount' => '500',
            'balance_before' => '1000',
            'balance_after' => '500',
            'description' => 'Data purchase',
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }

    $service = app(BonusService::class);
    expect($service->evaluate($dormant))->toHaveCount(1)
        ->and($service->evaluate($active))->toHaveCount(0)
        ->and($service->evaluate($neverTransacted))->toHaveCount(0)
        ->and(BonusEntitlement::where('bonus_id', $bonus->id)->count())->toBe(1);
});

it('calculates capped funding rewards and fee waivers once per verified webhook reference', function () {
    $bonus = bonusCampaign([
        'enjoyment' => [Bonus::ENJOYMENT_FUNDING, Bonus::ENJOYMENT_FEE_WAIVER],
        'funding_type' => 'percent',
        'funding_value' => 5,
        'funding_cap' => 300,
        'bonus_wallet_amount' => 0,
        'funding_whitelist' => ['xixapay'],
        'frequency_per_user' => 2,
    ]);
    $user = User::factory()->create();
    $service = app(BonusService::class);
    $service->manuallyGrant($bonus, $user);

    $reward = $service->applyFundingReward($user, 'xixapay', 10000, 'fund-ref-001', 100, 9900);
    $duplicate = $service->applyFundingReward($user, 'xixapay', 10000, 'fund-ref-001', 100, 9900);
    $wrongProvider = $service->applyFundingReward($user, 'securewaveng', 10000, 'fund-ref-002', 100, 9900);
    $secondReward = $service->applyFundingReward($user, 'xixapay', 10000, 'fund-ref-003', 100, 10000);
    $frequencyExhausted = $service->applyFundingReward($user, 'xixapay', 10000, 'fund-ref-004', 100, 9900);

    expect($reward['funding_bonus'])->toBe(300.0)
        ->and($reward['fee_waiver'])->toBe(100.0)
        ->and($reward['reward'])->toBe(400.0)
        ->and($duplicate['duplicate'])->toBeTrue()
        ->and($duplicate['reward'])->toBe(0.0)
        ->and($wrongProvider['reward'])->toBe(0.0)
        ->and($secondReward['reward'])->toBe(300.0)
        ->and($secondReward['fee_waiver'])->toBe(0.0)
        ->and($frequencyExhausted['reward'])->toBe(0.0)
        ->and(BonusLog::where('event_type', 'funding_reward')->count())->toBe(2);
});

it('moves available bonus to the main wallet once and expires stale credit', function () {
    $user = User::factory()->create(['main_wallet' => 1000]);
    $service = app(BonusService::class);
    $activeBonus = bonusCampaign(['bonus_wallet_amount' => 250]);
    $service->manuallyGrant($activeBonus, $user);
    $headers = [
        'Authorization' => 'Bearer '.$user->createToken('mobile:test', ['mobile'])->plainTextToken,
        'X-Device-ID' => 'conversion-device',
    ];

    postJson('/api/mobile/v1/bonuses/convert', [], $headers)
        ->assertOk()
        ->assertJsonPath('data.converted_amount', 250)
        ->assertJsonPath('data.main_wallet_balance', 1250)
        ->assertJsonPath('data.bonus_wallet_balance', 0);
    postJson('/api/mobile/v1/bonuses/convert', [], $headers)
        ->assertUnprocessable()
        ->assertJsonPath('message', 'There is no available bonus balance to move.');

    $expiredBonus = bonusCampaign([
        'title' => 'Expired credit',
        'bonus_wallet_amount' => 100,
    ]);
    $service->manuallyGrant($expiredBonus, $user);
    BonusEntitlement::where('bonus_id', $expiredBonus->id)->where('user_id', $user->id)
        ->update(['expires_at' => now()->subMinute()]);

    expect($service->expireCredits($user))->toBe(100.0)
        ->and((float) $user->fresh()->bonus_wallet)->toBe(0.0)
        ->and(BonusLog::where('event_type', 'expired')->where('bonus_id', $expiredBonus->id)->count())->toBe(1);
});

it('does not grant paused campaigns or unverified accounts', function () {
    bonusCampaign(['status' => false]);
    $unverified = User::factory()->unverified()->create();
    $verified = User::factory()->create();
    $service = app(BonusService::class);

    expect($service->evaluate($unverified))->toHaveCount(0)
        ->and($service->evaluate($verified))->toHaveCount(0)
        ->and(BonusEntitlement::count())->toBe(0);
});

it('uses only the highest-priority eligible funding campaign', function () {
    $low = bonusCampaign([
        'title' => 'Low priority funding',
        'enjoyment' => [Bonus::ENJOYMENT_FUNDING],
        'funding_type' => 'flat',
        'funding_value' => 100,
        'bonus_wallet_amount' => 0,
        'priority' => 1,
    ]);
    $high = bonusCampaign([
        'title' => 'High priority funding',
        'enjoyment' => [Bonus::ENJOYMENT_FUNDING],
        'funding_type' => 'flat',
        'funding_value' => 500,
        'bonus_wallet_amount' => 0,
        'priority' => 50,
    ]);
    $user = User::factory()->create();
    $service = app(BonusService::class);
    $service->manuallyGrant($low, $user);
    $service->manuallyGrant($high, $user);

    $reward = $service->applyFundingReward($user, 'xixapay', 10000, 'priority-ref', 100, 9900);

    expect($reward['reward'])->toBe(500.0)
        ->and($reward['bonus_id'])->toBe($high->id)
        ->and(BonusLog::where('event_type', 'funding_reward')->count())->toBe(1);
});

it('keeps an awarded entitlement valid for its full reward period after the campaign closes', function () {
    $bonus = bonusCampaign([
        'enjoyment' => [Bonus::ENJOYMENT_WALLET, Bonus::ENJOYMENT_FUNDING],
        'funding_type' => 'flat',
        'funding_value' => 150,
        'funding_whitelist' => ['xixapay'],
        'reward_valid_days' => 7,
        'ends_at' => now()->addHour(),
    ]);
    $user = User::factory()->create();
    $service = app(BonusService::class);
    $entitlement = $service->manuallyGrant($bonus, $user);

    expect($entitlement)->not->toBeNull()
        ->and($entitlement->expires_at->greaterThan(now()->addDays(6)))->toBeTrue()
        ->and($entitlement->expires_at->greaterThan($bonus->ends_at))->toBeTrue();

    $bonus->update(['ends_at' => now()->subMinute()]);
    $reward = $service->applyFundingReward($user, 'xixapay', 5000, 'after-campaign-end', 50, 4950);

    expect($reward['reward'])->toBe(150.0);
});

it('credits a Securewave funding campaign through its signed webhook exactly once', function () {
    $secret = 'securewave-test-secret';
    FundingOption::create([
        'is_current_option' => 1,
        'funding_option_name' => 'Securewave',
        'slug' => 'securewaveng',
        'api_secret_key' => $secret,
        'activation_status' => 1,
    ]);
    $bonus = bonusCampaign([
        'enjoyment' => [Bonus::ENJOYMENT_FUNDING],
        'funding_type' => 'flat',
        'funding_value' => 200,
        'bonus_wallet_amount' => 0,
        'funding_whitelist' => ['securewaveng'],
    ]);
    $upline = User::factory()->create(['bonus_wallet' => 0]);
    $user = User::factory()->create(['main_wallet' => 1000, 'upline_id' => $upline->id]);
    UplineFundingBonusSetting::create([
        'user_id' => $upline->id,
        'enabled' => true,
        'reward_type' => 'flat',
        'reward_value' => 75,
        'frequency_per_downline' => 1,
        'funding_whitelist' => ['securewaveng'],
    ]);
    app(BonusService::class)->manuallyGrant($bonus, $user);
    $payload = json_encode([
        'provider_reference' => 'securewave-funding-001',
        'transaction_status' => 'success',
        'customer' => ['email' => $user->email],
        'settlement_amount' => 9900,
        'amount' => 10000,
        'fees' => 100,
        'receiver' => ['bank' => 'Test Bank', 'name' => 'Bonus Tester', 'account_number' => '0123456789'],
        'description' => 'Wallet funding',
    ], JSON_THROW_ON_ERROR);
    $server = [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_X_SIGNATURE' => hash_hmac('sha256', $payload, $secret),
    ];

    $this->call('POST', '/api/admin/wallets/securewaveng_hook/test', [], [], [], $server, $payload)
        ->assertOk()
        ->assertJsonPath('status', 'successfully processed');
    $this->call('POST', '/api/admin/wallets/securewaveng_hook/test', [], [], [], $server, $payload)
        ->assertOk()
        ->assertJsonPath('status', 'already  likely received');

    expect((float) $user->fresh()->main_wallet)->toBe(11100.0)
        ->and(BonusLog::where('event_type', 'funding_reward')->where('funding_reference', 'securewave-funding-001')->count())->toBe(1)
        ->and((float) $upline->fresh()->bonus_wallet)->toBe(75.0)
        ->and(UplineFundingBonusLog::where('funding_reference', 'securewave-funding-001')->count())->toBe(1);
});

it('credits a Xixapay funding campaign through its signed webhook exactly once', function () {
    $secret = 'xixapay-test-secret';
    FundingOption::create([
        'is_current_option' => 1,
        'funding_option_name' => 'Xixapay',
        'slug' => 'xixapay',
        'api_secret_key' => $secret,
        'activation_status' => 1,
    ]);
    $bonus = bonusCampaign([
        'enjoyment' => [Bonus::ENJOYMENT_FUNDING],
        'funding_type' => 'flat',
        'funding_value' => 200,
        'bonus_wallet_amount' => 0,
        'funding_whitelist' => ['xixapay'],
    ]);
    $user = User::factory()->create(['main_wallet' => 1000]);
    app(BonusService::class)->manuallyGrant($bonus, $user);
    $payload = json_encode([
        'transaction_id' => 'xixapay-funding-001',
        'transaction_status' => 'success',
        'notification_status' => 'payment_successful',
        'customer' => ['email' => $user->email],
        'settlement_amount' => 9900,
        'amount_paid' => 10000,
        'receiver' => ['bank' => 'Test Bank', 'name' => 'Bonus Tester', 'account_number' => '0123456789'],
        'event_data' => ['data' => ['charged' => 100, 'settled' => 9900]],
        'description' => 'Wallet funding',
    ], JSON_THROW_ON_ERROR);
    $server = [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_XIXAPAY' => hash_hmac('sha256', $payload, $secret),
    ];

    $this->call('POST', '/api/admin/wallets/xixapayhook/test', [], [], [], $server, $payload)
        ->assertOk()
        ->assertJsonPath('status', 'successfully processed');
    $this->call('POST', '/api/admin/wallets/xixapayhook/test', [], [], [], $server, $payload)
        ->assertOk()
        ->assertJsonPath('status', 'already likely received');

    expect((float) $user->fresh()->main_wallet)->toBe(11100.0)
        ->and(BonusLog::where('event_type', 'funding_reward')->where('funding_reference', 'xixapay-funding-001')->count())->toBe(1);
});

it('allows an admin to create, view and pause a validated campaign', function () {
    $role = Role::create(['role_name' => 'Admin']);
    $admin = User::factory()->create(['role_id' => $role->id]);
    $payload = [
        'title' => 'Admin welcome campaign',
        'description' => 'A controlled launch reward.',
        'status' => '1',
        'group' => Bonus::GROUP_NEW_REGISTRATION,
        'enjoyment' => [Bonus::ENJOYMENT_WALLET],
        'bonus_wallet_amount' => '750',
        'frequency_per_user' => '1',
        'max_rewards_per_ip' => '1',
        'max_rewards_per_device' => '1',
        'reward_valid_days' => '7',
        'priority' => '10',
        'starts_at' => '',
        'ends_at' => now()->addMonth()->format('Y-m-d H:i:s'),
    ];

    $this->actingAs($admin)
        ->post('/admin/bonuses', $payload)
        ->assertRedirect()
        ->assertSessionHas('success');

    $campaign = Bonus::where('title', 'Admin welcome campaign')->firstOrFail();
    expect((float) $campaign->bonus_wallet_amount)->toBe(750.0)
        ->and($campaign->created_by)->toBe($admin->id);

    $this->actingAs($admin)
        ->get('/admin/bonuses')
        ->assertOk()
        ->assertSee('Admin welcome campaign')
        ->assertSee('Bonus campaigns');

    $this->actingAs($admin)
        ->patch("/admin/bonuses/{$campaign->id}/toggle")
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($campaign->fresh()->status)->toBeFalse();
});

it('rejects unsafe percentage campaigns without a cap', function () {
    $role = Role::create(['role_name' => 'Admin']);
    $admin = User::factory()->create(['role_id' => $role->id]);

    $this->actingAs($admin)->post('/admin/bonuses', [
        'title' => 'Uncapped percentage',
        'status' => '1',
        'group' => Bonus::GROUP_NEW_REGISTRATION,
        'enjoyment' => [Bonus::ENJOYMENT_FUNDING],
        'funding_type' => 'percent',
        'funding_value' => '5',
        'frequency_per_user' => '1',
        'ends_at' => now()->addMonth()->format('Y-m-d H:i:s'),
    ])
        ->assertSessionHasErrors('funding_cap');

    expect(Bonus::where('title', 'Uncapped percentage')->exists())->toBeFalse();
});
