<?php

use App\Mail\AffiliateLowBalanceMail;
use App\Models\AffiliateFundingAttempt;
use App\Models\AffiliateWalletSetting;
use App\Models\BonusLog;
use App\Models\Commissions;
use App\Models\FundingWebhookPayload;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\UplineFundingBonusLog;
use App\Models\UplineFundingBonusSetting;
use App\Models\User;
use App\Services\AffiliateLowBalanceService;
use App\Services\BusinessProfitReportService;
use App\Services\UplineFundingBonusService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use function Pest\Laravel\postJson;

function affiliateSetting(User $user, array $attributes = []): AffiliateWalletSetting
{
    return AffiliateWalletSetting::create(array_merge([
        'user_id' => $user->id,
        'enabled' => true,
        'funding_threshold' => 5000,
        'notification_email' => $user->email,
        'admin_copy_email' => 'finance-admin@example.com',
        'automatic_transfer_enabled' => false,
    ], $attributes));
}

function uplineBonusSetting(User $upline, array $attributes = []): UplineFundingBonusSetting
{
    return UplineFundingBonusSetting::create(array_merge([
        'user_id' => $upline->id,
        'enabled' => true,
        'reward_type' => 'flat',
        'reward_value' => 100,
        'reward_cap' => null,
        'frequency_per_downline' => 1,
        'funding_whitelist' => ['xixapay', 'securewaveng'],
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addMonth(),
    ], $attributes));
}

it('stores affiliate funding account details encrypted and sends only one low-balance email per day', function () {
    Mail::fake();
    $user = User::factory()->create(['main_wallet' => 1000]);
    $setting = affiliateSetting($user, [
        'funding_bank_name' => 'Test Bank',
        'funding_account_name' => 'Affiliate Customer',
        'funding_account_number' => '0123456789',
    ]);

    $service = app(AffiliateLowBalanceService::class);
    expect($service->process($setting))->toBe('notified')
        ->and($service->process($setting->fresh('user')))->toBe('skipped')
        ->and(AffiliateFundingAttempt::count())->toBe(1)
        ->and($setting->fresh()->last_notified_on->isToday())->toBeTrue()
        ->and(DB::table('affiliate_wallet_settings')->where('id', $setting->id)->value('funding_account_number'))
        ->not->toBe('0123456789');

    Mail::assertSent(AffiliateLowBalanceMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email) && $mail->hasCc('finance-admin@example.com');
    });
});

it('does not notify disabled, deactivated or sufficiently funded affiliates', function () {
    Mail::fake();
    $service = app(AffiliateLowBalanceService::class);
    $funded = User::factory()->create(['main_wallet' => 7000]);
    $deactivated = User::factory()->create(['main_wallet' => 0, 'is_deactivated' => 1]);
    $disabled = User::factory()->create(['main_wallet' => 0]);

    expect($service->process(affiliateSetting($funded)))->toBe('skipped')
        ->and($service->process(affiliateSetting($deactivated)))->toBe('skipped')
        ->and($service->process(affiliateSetting($disabled, ['enabled' => false])))->toBe('skipped')
        ->and(AffiliateFundingAttempt::count())->toBe(0);
    Mail::assertNothingSent();
});

it('prepares but never executes an automatic affiliate transfer without an integration', function () {
    Mail::fake();
    $user = User::factory()->create(['main_wallet' => 100]);
    $setting = affiliateSetting($user, [
        'automatic_transfer_enabled' => true,
        'transfer_provider' => 'future-provider',
        'funding_amount' => 10000,
        'funding_account_number' => '0123456789',
    ]);

    expect(app(AffiliateLowBalanceService::class)->process($setting))->toBe('notified');
    $attempt = AffiliateFundingAttempt::firstOrFail();

    expect($attempt->status)->toBe('awaiting_transfer_integration')
        ->and((float) $attempt->requested_amount)->toBe(10000.0)
        ->and($attempt->provider_reference)->toBeNull()
        ->and($attempt->failure_reason)->toBe('Automatic transfer provider is not configured.')
        ->and((float) $user->fresh()->main_wallet)->toBe(100.0);
});

it('credits an upline bonus once per downline and ignores webhook replay', function () {
    $upline = User::factory()->create(['bonus_wallet' => 50]);
    $downline = User::factory()->create(['upline_id' => $upline->id]);
    uplineBonusSetting($upline);
    $service = app(UplineFundingBonusService::class);

    $first = $service->apply($downline, 'xixapay', 10000, 'funding-one');
    $replay = $service->apply($downline, 'xixapay', 10000, 'funding-one');
    $frequencyExceeded = $service->apply($downline, 'xixapay', 10000, 'funding-two');

    expect($first['reward'])->toBe(100.0)
        ->and($replay['duplicate'])->toBeTrue()
        ->and($frequencyExceeded['reward'])->toBe(0.0)
        ->and((float) $upline->fresh()->bonus_wallet)->toBe(150.0)
        ->and(UplineFundingBonusLog::count())->toBe(1);
});

it('caps percentage upline bonuses and honours configurable frequency and providers', function () {
    $upline = User::factory()->create(['bonus_wallet' => 0]);
    $downline = User::factory()->create(['upline_id' => $upline->id]);
    uplineBonusSetting($upline, [
        'reward_type' => 'percent',
        'reward_value' => 5,
        'reward_cap' => 300,
        'frequency_per_downline' => 2,
        'funding_whitelist' => ['securewaveng'],
    ]);
    $service = app(UplineFundingBonusService::class);

    $wrongProvider = $service->apply($downline, 'xixapay', 10000, 'wrong-provider');
    $first = $service->apply($downline, 'securewaveng', 10000, 'secure-one');
    $second = $service->apply($downline, 'securewaveng', 2000, 'secure-two');
    $third = $service->apply($downline, 'securewaveng', 2000, 'secure-three');

    expect($wrongProvider['reward'])->toBe(0.0)
        ->and($first['reward'])->toBe(300.0)
        ->and($second['reward'])->toBe(100.0)
        ->and($third['reward'])->toBe(0.0)
        ->and((float) $upline->fresh()->bonus_wallet)->toBe(400.0)
        ->and(UplineFundingBonusLog::count())->toBe(2);
});

it('allows an upline to move direct referral bonuses from bonus wallet to main wallet', function () {
    $upline = User::factory()->create(['main_wallet' => 1000, 'bonus_wallet' => 0]);
    $downline = User::factory()->create(['upline_id' => $upline->id]);
    uplineBonusSetting($upline, ['reward_value' => 250]);
    app(UplineFundingBonusService::class)->apply($downline, 'xixapay', 10000, 'convert-referral');
    $headers = ['Authorization' => 'Bearer '.$upline->createToken('mobile:test', ['mobile'])->plainTextToken];

    postJson('/api/mobile/v1/bonuses/convert', [], $headers)
        ->assertOk()
        ->assertJsonPath('data.converted_amount', 250)
        ->assertJsonPath('data.main_wallet_balance', 1250)
        ->assertJsonPath('data.bonus_wallet_balance', 0);

    expect(BonusLog::where('event_type', 'external_bonus_converted')->sum('amount'))->toEqual(250);
});

it('produces a filtered profitability report without double-counting funding incentives', function () {
    $user = User::factory()->create();
    $upline = User::factory()->create();
    $transaction = Transaction::create([
        'user_id' => $user->id,
        'product_plan_id' => 'test-plan',
        'automation_id' => null,
        'transaction_category' => 'data',
        'status' => '1',
        'wallet_category' => 'main_wallet',
        'amount' => 1000,
        'discounted_amount' => 1000,
        'service_charge' => 20,
        'automation_plan_amount' => 700,
        'balance_before' => 2000,
        'balance_after' => 980,
        'description' => 'Data purchase',
    ]);
    Commissions::create([
        'transaction_id' => $transaction->id,
        'commission' => 50,
        'beneficiary' => $upline->id,
        'transaction_by' => $user->id,
        'status' => 1,
        'payout_status' => 0,
    ]);
    FundingWebhookPayload::create([
        'funding_slug' => 'xixapay',
        'user_id' => $user->id,
        'user_email' => $user->email,
        'status' => 'success',
        'funding_status' => 'success',
        'message' => 'funded',
        'package_id' => 'test',
        'bank_name' => 'Test Bank',
        'account_name' => 'Test User',
        'account_number' => '0123456789',
        'account_reference' => 'funding-profit',
        'amount_paid' => 1000,
        'amount_charged' => 0,
        'amount_settled' => 980,
        'currency' => 'NGN',
        'transaction_reference' => 'funding-profit',
        'collection_reference' => 'funding-profit',
        'payload_content' => json_encode(['settlement_amount' => 1000]),
    ]);
    BonusLog::create([
        'user_id' => $user->id,
        'event_type' => 'entitlement_granted',
        'amount' => 100,
        'event_key' => 'report-grant',
    ]);
    BonusLog::create([
        'user_id' => $user->id,
        'event_type' => 'wallet_converted',
        'amount' => 25,
        'event_key' => 'report-conversion',
    ]);
    BonusLog::create([
        'user_id' => $user->id,
        'event_type' => 'funding_reward',
        'amount' => 40,
        'event_key' => 'report-funding-reward',
    ]);
    $setting = uplineBonusSetting($upline);
    UplineFundingBonusLog::create([
        'upline_funding_bonus_setting_id' => $setting->id,
        'upline_id' => $upline->id,
        'downline_id' => $user->id,
        'funding_provider' => 'xixapay',
        'funding_reference' => 'report-upline',
        'funded_amount' => 1000,
        'bonus_amount' => 30,
        'bonus_balance_before' => 0,
        'bonus_balance_after' => 30,
        'sequence' => 1,
        'event_key' => 'report-upline',
    ]);

    $report = app(BusinessProfitReportService::class)->generate([
        'from' => now()->subDay()->toDateString(),
        'to' => now()->addDay()->toDateString(),
        'category' => 'data',
    ]);

    expect($report['summary']['transaction_gross_profit'])->toBe(320.0)
        ->and($report['summary']['funding_net_margin'])->toBe(20.0)
        ->and($report['summary']['commission_accrued'])->toBe(50.0)
        ->and($report['summary']['campaign_wallet_expense'])->toBe(25.0)
        ->and($report['summary']['campaign_funding_rewards_in_funding_margin'])->toBe(40.0)
        ->and($report['summary']['upline_funding_bonus_expense'])->toBe(30.0)
        ->and($report['summary']['net_profit'])->toBe(235.0)
        ->and($report['summary']['campaign_liability_movement'])->toBe(75.0)
        ->and($report['summary']['current_bonus_wallet_liability'])->toBe(0.0);
});

it('allows admins to configure affiliate finance and blocks ordinary users', function () {
    $adminRole = Role::create(['role_name' => 'Admin']);
    $admin = User::factory()->create(['role_id' => $adminRole->id]);
    $customer = User::factory()->create();
    $ordinary = User::factory()->create();

    $this->actingAs($ordinary)->get('/admin/affiliate-finance')->assertRedirect();
    $this->actingAs($admin)->post('/admin/affiliate-finance/wallet-setting', [
        'user_id' => $customer->id,
        'enabled' => 1,
        'funding_threshold' => 5000,
        'notification_email' => $customer->email,
        'admin_copy_email' => 'admin@example.com',
        'automatic_transfer_enabled' => 0,
    ])->assertRedirect()->assertSessionHas('success');

    $this->actingAs($admin)->post('/admin/affiliate-finance/upline-bonus', [
        'user_id' => $customer->id,
        'enabled' => 1,
        'reward_type' => 'percent',
        'reward_value' => 2,
        'reward_cap' => 200,
        'frequency_per_downline' => 1,
        'funding_whitelist' => ['xixapay'],
    ])->assertRedirect()->assertSessionHas('success');

    $this->actingAs($admin)->from('/admin/affiliate-finance')->post('/admin/affiliate-finance/upline-bonus', [
        'user_id' => $customer->id,
        'enabled' => 1,
        'reward_type' => 'percent',
        'reward_value' => 101,
        'frequency_per_downline' => 1,
    ])->assertRedirect('/admin/affiliate-finance')
        ->assertSessionHasErrors(['reward_value', 'reward_cap']);

    $this->actingAs($admin)->get('/admin/affiliate-finance')
        ->assertOk()
        ->assertSee('Affiliate finance')
        ->assertSee($customer->email);

    expect(AffiliateWalletSetting::where('user_id', $customer->id)->exists())->toBeTrue()
        ->and(UplineFundingBonusSetting::where('user_id', $customer->id)->exists())->toBeTrue();
});
