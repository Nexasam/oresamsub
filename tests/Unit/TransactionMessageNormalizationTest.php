<?php

use App\Models\Bonus;
use App\Models\Transaction;
use App\Models\User;
use App\Services\BonusService;

it('normalizes structured transaction messages before database persistence', function () {
    $transaction = new Transaction();
    $transaction->user_screen_message = [
        'success' => true,
        'message' => 'You have successfully shared 1GB data.',
        'data' => ['reference' => 'provider-reference'],
    ];
    $transaction->admin_screen_message = [
        'success' => true,
        'data' => ['reference' => 'provider-reference'],
    ];

    expect($transaction->getAttributes()['user_screen_message'])
        ->toBe('You have successfully shared 1GB data.')
        ->and(json_decode($transaction->getAttributes()['admin_screen_message'], true))
        ->toMatchArray([
            'success' => true,
            'data' => ['reference' => 'provider-reference'],
        ]);
});

it('does not evaluate a registration campaign against a missing user creation date', function () {
    $bonus = (new Bonus())->setDateFormat('Y-m-d H:i:s');
    $bonus->forceFill([
        'group' => Bonus::GROUP_NEW_REGISTRATION,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'conditions' => [],
    ]);
    $user = (new User())->setDateFormat('Y-m-d H:i:s');
    $user->created_at = null;

    $method = new ReflectionMethod(BonusService::class, 'isEligible');

    expect($method->invoke(new BonusService(), $bonus, $user))->toBeFalse();
});

it('supports registration campaigns without an end date', function () {
    $bonus = (new Bonus())->setDateFormat('Y-m-d H:i:s');
    $bonus->forceFill([
        'group' => Bonus::GROUP_NEW_REGISTRATION,
        'starts_at' => now()->subDay(),
        'ends_at' => null,
        'conditions' => [],
    ]);
    $user = (new User())->setDateFormat('Y-m-d H:i:s');
    $user->created_at = now();

    $method = new ReflectionMethod(BonusService::class, 'isEligible');

    expect($method->invoke(new BonusService(), $bonus, $user))->toBeTrue();
});
