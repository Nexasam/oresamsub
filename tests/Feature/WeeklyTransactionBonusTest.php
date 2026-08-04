<?php

use App\Models\Bonus;
use App\Models\BonusLog;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WeeklyBonusReward;
use App\Services\WeeklyTransactionBonusService;
use Carbon\CarbonImmutable;

function weeklyCampaign(array $attributes = []): Bonus
{
    return Bonus::create(array_merge([
        'title' => fake()->unique()->sentence(3),
        'status' => true,
        'group' => Bonus::GROUP_WEEKLY_TRANSACTION_VOLUME,
        'enjoyment' => [Bonus::ENJOYMENT_WALLET],
        'conditions' => [
            'weekly_minimum_volume' => 20000,
            'weekly_category_scope' => 'all',
            'weekly_categories' => [],
        ],
        'funding_type' => 'flat',
        'funding_value' => 500,
        'funding_cap' => null,
        'bonus_wallet_amount' => 0,
        'frequency_per_user' => 1,
        'reward_valid_days' => null,
        'starts_at' => now()->subMonth(),
        'ends_at' => null,
    ], $attributes));
}

function completedPurchase(User $user, CarbonImmutable $at, float $amount, string $category = 'data', string $status = '1'): Transaction
{
    return Transaction::create([
        'user_id' => $user->id,
        'product_plan_id' => 'weekly-test-plan',
        'transaction_category' => $category,
        'status' => $status,
        'wallet_category' => 'main_wallet',
        'amount' => $amount,
        'balance_before' => 50000,
        'balance_after' => 50000 - $amount,
        'description' => 'Weekly campaign test purchase',
        'created_at' => $at,
        'updated_at' => $at,
    ]);
}

it('awards a flat weekly reward once using only successful eligible transactions', function () {
    $week = CarbonImmutable::parse('2026-07-27', 'Africa/Lagos')->startOfWeek();
    $user = User::factory()->create(['bonus_wallet' => 0]);
    weeklyCampaign([
        'conditions' => [
            'weekly_minimum_volume' => 20000,
            'weekly_category_scope' => 'selected',
            'weekly_categories' => ['data'],
        ],
    ]);
    completedPurchase($user, $week->addDay(), 12000, 'data');
    completedPurchase($user, $week->addDays(2), 9000, 'data');
    completedPurchase($user, $week->addDays(3), 10000, 'airtime');
    completedPurchase($user, $week->addDays(4), 5000, 'data', '2');

    $first = app(WeeklyTransactionBonusService::class)->processWeek($week);
    $second = app(WeeklyTransactionBonusService::class)->processWeek($week);

    expect($first['rewarded'])->toBe(1)
        ->and($second['rewarded'])->toBe(0)
        ->and((float) $user->fresh()->bonus_wallet)->toBe(500.0)
        ->and(WeeklyBonusReward::count())->toBe(1)
        ->and((float) WeeklyBonusReward::first()->qualifying_volume)->toBe(21000.0)
        ->and(BonusLog::where('event_type', 'weekly_volume_reward')->count())->toBe(1);
});

it('calculates percentage rewards from weekly volume and applies the configured cap', function () {
    $week = CarbonImmutable::parse('2026-07-27', 'Africa/Lagos')->startOfWeek();
    $user = User::factory()->create(['bonus_wallet' => 0]);
    weeklyCampaign([
        'funding_type' => 'percent',
        'funding_value' => 10,
        'funding_cap' => 1500,
    ]);
    completedPurchase($user, $week->addDay(), 25000);

    app(WeeklyTransactionBonusService::class)->processWeek($week);

    expect((float) $user->fresh()->bonus_wallet)->toBe(1500.0)
        ->and((float) WeeklyBonusReward::first()->reward_amount)->toBe(1500.0);
});

it('awards every weekly campaign a customer qualifies for without duplicating either campaign', function () {
    $week = CarbonImmutable::parse('2026-07-27', 'Africa/Lagos')->startOfWeek();
    $user = User::factory()->create(['bonus_wallet' => 0]);
    weeklyCampaign([
        'title' => 'High priority general campaign',
        'priority' => 100,
        'funding_value' => 1000,
    ]);
    weeklyCampaign([
        'title' => 'Customer-specific campaign',
        'priority' => 1,
        'funding_value' => 300,
        'conditions' => [
            'weekly_minimum_volume' => 20000,
            'weekly_category_scope' => 'all',
            'weekly_categories' => [],
            'targeted_user_ids' => [$user->id],
            'targeted_customers' => [$user->username],
        ],
    ]);
    completedPurchase($user, $week->addDay(), 25000);

    $result = app(WeeklyTransactionBonusService::class)->processWeek($week);

    $secondRun = app(WeeklyTransactionBonusService::class)->processWeek($week);

    expect($result['rewarded'])->toBe(2)
        ->and($secondRun['rewarded'])->toBe(0)
        ->and((float) $user->fresh()->bonus_wallet)->toBe(1300.0)
        ->and(WeeklyBonusReward::count())->toBe(2)
        ->and(WeeklyBonusReward::with('bonus')->get()->pluck('bonus.title')->sort()->values()->all())
        ->toBe(['Customer-specific campaign', 'High priority general campaign']);
});
