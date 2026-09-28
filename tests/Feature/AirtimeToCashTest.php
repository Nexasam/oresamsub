<?php

use App\Mail\AirtimeToCashSubmittedMail;
use App\Models\AirtimeToCashRequest;
use App\Models\ConfigSetting;
use App\Models\Network;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

function airtimeToCashUser(array $overrides = []): User
{
    $role = Role::firstOrCreate(['role_name' => 'User']);

    return User::factory()->create(array_merge(['role_id' => $role->id], $overrides));
}

function airtimeToCashAdmin(array $overrides = []): User
{
    $role = Role::firstOrCreate(['role_name' => 'Admin']);

    return User::factory()->create(array_merge(['role_id' => $role->id], $overrides));
}

it('stores db-backed default settings and renders the customer airtime-to-cash page', function () {
    $user = airtimeToCashUser();
    Network::create(['network_name' => 'MTN', 'api_id' => 1]);

    $this->actingAs($user)
        ->get(route('airtime-to-cash.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('AirtimeToCash')
            ->where('settings.enabled', true)
            ->where('settings.rate_per_100', 90)
            ->whereType('settings.fraud_disclaimer_text', 'string')
            ->has('networks', 1)
        );

    expect(ConfigSetting::where('key', 'airtime_to_cash_enabled')->value('current_value'))->toBe('1')
        ->and(ConfigSetting::where('key', 'airtime_to_cash_support_email')->value('current_value'))->toBe('admin@example.com')
        ->and(ConfigSetting::where('key', 'airtime_to_cash_support_whatsapp')->value('current_value'))->toBe('234xxxxxxxxxx')
        ->and(ConfigSetting::where('key', 'airtime_to_cash_rate_per_100')->value('current_value'))->toBe('90');
});

it('lets a customer submit airtime-to-cash request and notifies support', function () {
    Mail::fake();

    $user = airtimeToCashUser();
    $network = Network::create(['network_name' => 'AIRTEL', 'api_id' => 2]);

    ConfigSetting::create([
        'key' => 'airtime_to_cash_support_email',
        'value' => 'support@example.com',
        'current_value' => 'support@example.com',
    ]);
    ConfigSetting::create([
        'key' => 'airtime_to_cash_rate_per_100',
        'value' => '90',
        'current_value' => '90',
    ]);

    $this->actingAs($user)
        ->from(route('airtime-to-cash.index'))
        ->post(route('airtime-to-cash.store'), [
            'network_id' => $network->id,
            'airtime_amount' => 1000,
            'sender_phone' => '08012345678',
            'payout_bank_name' => 'OPay',
            'payout_account_name' => 'Customer Name',
            'payout_account_number' => '1234567890',
            'customer_transfer_reference' => 'TX-123',
            'customer_note' => 'I will transfer shortly.',
            'fraud_disclaimer_accepted' => '1',
        ])
        ->assertRedirect(route('airtime-to-cash.index'))
        ->assertSessionHas('success');

    $requestRecord = AirtimeToCashRequest::first();

    expect($requestRecord)->not->toBeNull()
        ->and($requestRecord->user_id)->toBe($user->id)
        ->and($requestRecord->network_name)->toBe('AIRTEL')
        ->and((float) $requestRecord->airtime_amount)->toBe(1000.0)
        ->and((float) $requestRecord->cash_amount)->toBe(900.0)
        ->and($requestRecord->status)->toBe('pending')
        ->and($requestRecord->payout_account_number)->toBe('1234567890')
        ->and($requestRecord->fraud_disclaimer_accepted_at)->not->toBeNull()
        ->and($requestRecord->fraud_disclaimer_text)->toContain('fraudulent')
        ->and($requestRecord->fraud_disclaimer_ip)->not->toBeNull();

    Mail::assertSent(AirtimeToCashSubmittedMail::class, fn ($mail) =>
        $mail->hasTo('support@example.com')
        && $mail->requestRecord->is($requestRecord)
    );
});

it('requires the customer to accept the airtime fraud disclaimer before submission', function () {
    Mail::fake();

    $user = airtimeToCashUser();
    $network = Network::create(['network_name' => 'MTN', 'api_id' => 22]);

    $this->actingAs($user)
        ->from(route('airtime-to-cash.index'))
        ->post(route('airtime-to-cash.store'), [
            'network_id' => $network->id,
            'airtime_amount' => 1000,
            'sender_phone' => '08012345678',
            'payout_bank_name' => 'OPay',
            'payout_account_name' => 'Customer Name',
            'payout_account_number' => '1234567890',
        ])
        ->assertRedirect(route('airtime-to-cash.index'))
        ->assertSessionHasErrors('fraud_disclaimer_accepted');

    expect(AirtimeToCashRequest::count())->toBe(0);
    Mail::assertNothingSent();
});

it('allows admin to update manual processing status and db settings', function () {
    $admin = airtimeToCashAdmin();
    $user = airtimeToCashUser();
    $network = Network::create(['network_name' => 'GLO', 'api_id' => 3]);

    $requestRecord = AirtimeToCashRequest::create([
        'reference' => 'ATC-TEST',
        'user_id' => $user->id,
        'network_id' => $network->id,
        'network_name' => 'GLO',
        'airtime_amount' => 2000,
        'cash_amount' => 1800,
        'rate_per_100' => 90,
        'sender_phone' => '08012345678',
        'payout_bank_name' => 'Kuda',
        'payout_account_name' => 'Customer Name',
        'payout_account_number' => '1234567890',
        'status' => 'pending',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.airtime-to-cash.settings'), [
            'enabled' => '0',
            'support_email' => 'cashdesk@example.com',
            'support_whatsapp' => '2348012345678',
            'rate_per_100' => 88,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(ConfigSetting::where('key', 'airtime_to_cash_enabled')->value('current_value'))->toBe('0')
        ->and(ConfigSetting::where('key', 'airtime_to_cash_support_email')->value('current_value'))->toBe('cashdesk@example.com')
        ->and(ConfigSetting::where('key', 'airtime_to_cash_support_whatsapp')->value('current_value'))->toBe('2348012345678')
        ->and(ConfigSetting::where('key', 'airtime_to_cash_rate_per_100')->value('current_value'))->toBe('88');

    $this->actingAs($admin)
        ->put(route('admin.airtime-to-cash.update', $requestRecord), [
            'status' => 'paid',
            'admin_note' => 'Verified and paid manually.',
            'payout_reference' => 'PAY-123',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $requestRecord->refresh();

    expect($requestRecord->status)->toBe('paid')
        ->and($requestRecord->admin_note)->toBe('Verified and paid manually.')
        ->and($requestRecord->payout_reference)->toBe('PAY-123')
        ->and($requestRecord->processed_by)->toBe($admin->id)
        ->and($requestRecord->processed_at)->not->toBeNull();
});

it('blocks new customer submissions when admin disables airtime-to-cash', function () {
    Mail::fake();

    $user = airtimeToCashUser();
    $network = Network::create(['network_name' => 'MTN', 'api_id' => 4]);

    ConfigSetting::create([
        'key' => 'airtime_to_cash_enabled',
        'value' => '0',
        'current_value' => '0',
    ]);

    $this->actingAs($user)
        ->from(route('airtime-to-cash.index'))
        ->post(route('airtime-to-cash.store'), [
            'network_id' => $network->id,
            'airtime_amount' => 1000,
            'sender_phone' => '08012345678',
            'payout_bank_name' => 'OPay',
            'payout_account_name' => 'Customer Name',
            'payout_account_number' => '1234567890',
        ])
        ->assertRedirect(route('airtime-to-cash.index'))
        ->assertSessionHas('failure');

    expect(AirtimeToCashRequest::count())->toBe(0);
    Mail::assertNothingSent();
});
