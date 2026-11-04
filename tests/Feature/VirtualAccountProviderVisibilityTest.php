<?php

use App\Models\FundingOption;
use App\Models\FundingOptionBankCodes;
use App\Models\User;
use App\Models\UserVirtualAccount;
use Inertia\Testing\AssertableInertia as Assert;

it('shows only Xixapay virtual accounts on the customer web page', function () {
    $user = User::factory()->create(['pin' => '4567']);
    $xixapay = FundingOption::create([
        'funding_option_name' => 'Xixapay',
        'slug' => 'xixapay',
        'activation_status' => '1',
    ]);
    $other = FundingOption::create([
        'funding_option_name' => 'Other provider',
        'slug' => 'other-provider',
        'activation_status' => '1',
    ]);
    FundingOptionBankCodes::create([
        'funding_option_id' => $xixapay->id,
        'bank_code' => 'wema',
        'visibility_status' => '1',
    ]);
    UserVirtualAccount::create([
        'user_id' => $user->id,
        'funding_option_id' => $xixapay->id,
        'funding_slug' => 'xixapay',
        'bank_name' => 'Xixapay Bank',
        'bank_code' => 'wema',
        'account_name' => 'OresamSub Customer',
        'account_number' => '1234567890',
    ]);
    UserVirtualAccount::create([
        'user_id' => $user->id,
        'funding_option_id' => $other->id,
        'funding_slug' => 'other-provider',
        'bank_name' => 'Hidden Bank',
        'bank_code' => 'other',
        'account_name' => 'OresamSub Customer',
        'account_number' => '9999999999',
    ]);

    $this->actingAs($user)
        ->get(route('inertia.virtual_accounts.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('VirtualAccounts')
            ->has('virtualccts', 1)
            ->where('virtualccts.0.account_number', '1234567890')
        );
});
