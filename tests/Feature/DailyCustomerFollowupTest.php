<?php

use App\Models\Role;
use App\Models\CustomerFollowupCall;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

function followupAdmin(): User
{
    $role = Role::firstOrCreate(['role_name' => 'Admin']);

    return User::factory()->create(['role_id' => $role->id]);
}

function followupCustomer(string $username, string $category = 'generic'): User
{
    return User::factory()->create([
        'username' => $username,
        'customer_category' => $category,
    ]);
}

function successfulPurchase(User $customer, Carbon|string $when, string|int $status = 1): Transaction
{
    return Transaction::query()->create([
        'user_id' => $customer->id,
        'product_plan_id' => 'retention-test-plan',
        'transaction_category' => 'data',
        'status' => $status,
        'wallet_category' => 'main_wallet',
        'amount' => '100',
        'balance_before' => '1000',
        'balance_after' => '900',
        'description' => 'Retention test purchase',
        'created_at' => Carbon::parse($when),
        'updated_at' => Carbon::parse($when),
    ]);
}

beforeEach(function () {
    Carbon::setTestNow('2026-08-15 12:00:00');
});

afterEach(function () {
    Carbon::setTestNow();
});

it('treats only successful transactions as customer activity', function () {
    $admin = followupAdmin();
    $stale = followupCustomer('stale-success-only');
    $active = followupCustomer('active-success');

    successfulPurchase($stale, now()->subDays(40));
    successfulPurchase($stale, now()->subDay(), -1);
    successfulPurchase($active, now()->subDays(2));

    $this->actingAs($admin)
        ->get(route('admin.daily_customer_followup.index', [
            'segment' => 'stale',
            'inactivity_mode' => 'days',
            'inactive_days' => 30,
        ]))
        ->assertOk()
        ->assertSee('stale-success-only')
        ->assertDontSee('active-success');
});

it('includes never purchased customers in stale results and labels them', function () {
    $admin = followupAdmin();
    followupCustomer('never-bought');

    $this->actingAs($admin)
        ->get(route('admin.daily_customer_followup.index', [
            'segment' => 'stale',
            'inactivity_mode' => 'days',
            'inactive_days' => 10,
        ]))
        ->assertOk()
        ->assertSee('never-bought')
        ->assertSee('Never purchased');
});

it('filters by customer category and last successful purchase period', function () {
    $admin = followupAdmin();
    $pos = followupCustomer('period-pos', 'pos');
    $generic = followupCustomer('period-generic', 'generic');
    $outside = followupCustomer('outside-pos', 'pos');

    successfulPurchase($pos, '2026-07-12');
    successfulPurchase($generic, '2026-07-12');
    successfulPurchase($outside, '2026-06-10');

    $this->actingAs($admin)
        ->get(route('admin.daily_customer_followup.index', [
            'customer_type' => 'pos',
            'inactivity_mode' => 'period',
            'last_purchase_from' => '2026-07-01',
            'last_purchase_to' => '2026-07-31',
        ]))
        ->assertOk()
        ->assertSee('period-pos')
        ->assertDontSee('period-generic')
        ->assertDontSee('outside-pos');
});

it('finds suddenly inactive customers using the x y z rule', function () {
    $admin = followupAdmin();
    $sudden = followupCustomer('suddenly-inactive');
    $notFrequent = followupCustomer('not-frequent-enough');
    $stillActive = followupCustomer('still-active');

    foreach ([10, 15, 20] as $daysAgo) {
        successfulPurchase($sudden, now()->subDays($daysAgo));
    }

    successfulPurchase($notFrequent, now()->subDays(15));

    foreach ([10, 15, 20] as $daysAgo) {
        successfulPurchase($stillActive, now()->subDays($daysAgo));
    }
    successfulPurchase($stillActive, now()->subDays(2));

    $this->actingAs($admin)
        ->get(route('admin.daily_customer_followup.index', [
            'segment' => 'suddenly_inactive',
            'purchase_count' => 3,
            'activity_days' => 30,
            'inactive_days' => 7,
        ]))
        ->assertOk()
        ->assertSee('suddenly-inactive')
        ->assertDontSee('not-frequent-enough')
        ->assertDontSee('still-active');
});

it('records an admin attributed customer call with feedback and next action', function () {
    $admin = followupAdmin();
    $customer = followupCustomer('called-customer');

    $this->actingAs($admin)
        ->post(route('admin.daily_customer_followup.calls.store', $customer), [
            'outcome' => 'answered',
            'feedback' => 'Customer had trouble finding the preferred data plan.',
            'followup_status' => 'follow_up_again',
            'next_followup_at' => '2026-08-17 10:30:00',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('customer_followup_calls', [
        'customer_id' => $customer->id,
        'called_by' => $admin->id,
        'outcome' => 'answered',
        'feedback' => 'Customer had trouble finding the preferred data plan.',
        'followup_status' => 'follow_up_again',
        'next_followup_at' => '2026-08-17 10:30:00',
    ]);
});

it('requires useful call feedback and a date when another followup is planned', function () {
    $admin = followupAdmin();
    $customer = followupCustomer('validation-customer');

    $this->actingAs($admin)
        ->from(route('admin.daily_customer_followup.index'))
        ->post(route('admin.daily_customer_followup.calls.store', $customer), [
            'outcome' => 'answered',
            'feedback' => '',
            'followup_status' => 'follow_up_again',
        ])
        ->assertRedirect(route('admin.daily_customer_followup.index'))
        ->assertSessionHasErrors(['feedback', 'next_followup_at']);
});

it('blocks ordinary customers from creating followup call logs', function () {
    $ordinary = followupCustomer('ordinary-user');
    $customer = followupCustomer('protected-customer');

    $this->actingAs($ordinary)
        ->post(route('admin.daily_customer_followup.calls.store', $customer), [
            'outcome' => 'no_answer',
            'feedback' => 'No response after two attempts.',
            'followup_status' => 'follow_up_again',
            'next_followup_at' => '2026-08-18 09:00:00',
        ])
        ->assertRedirect();

    $this->assertDatabaseCount('customer_followup_calls', 0);
});

it('renders the retention controls customer signals and call history', function () {
    $admin = followupAdmin();
    $customer = followupCustomer('retention-dashboard-customer', 'pos');
    successfulPurchase($customer, now()->subDays(35));

    CustomerFollowupCall::query()->create([
        'customer_id' => $customer->id,
        'called_by' => $admin->id,
        'outcome' => 'answered',
        'feedback' => 'Customer requested a reminder after salary day.',
        'followup_status' => 'follow_up_again',
        'next_followup_at' => now()->subDay(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.daily_customer_followup.index', [
            'segment' => 'stale',
            'inactive_days' => 30,
        ]))
        ->assertOk()
        ->assertSee('Customer Retention Follow-up')
        ->assertSee('Suddenly inactive')
        ->assertSee('Last-purchase period')
        ->assertSee('retention-dashboard-customer')
        ->assertSee('35 days inactive')
        ->assertSee('Overdue follow-up')
        ->assertSee('Customer requested a reminder after salary day.')
        ->assertSee($admin->first_name)
        ->assertSee('Log a call')
        ->assertSee('Next follow-up');
});

it('prioritizes overdue followups before other stale customers', function () {
    $admin = followupAdmin();
    $regular = followupCustomer('regular-stale');
    $overdue = followupCustomer('overdue-stale');
    successfulPurchase($regular, now()->subDays(80));
    successfulPurchase($overdue, now()->subDays(40));

    CustomerFollowupCall::query()->create([
        'customer_id' => $overdue->id,
        'called_by' => $admin->id,
        'outcome' => 'answered',
        'feedback' => 'Asked us to call back.',
        'followup_status' => 'follow_up_again',
        'next_followup_at' => now()->subHour(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.daily_customer_followup.index', [
            'segment' => 'stale',
            'inactive_days' => 30,
        ]))
        ->assertOk()
        ->assertSeeInOrder(['overdue-stale', 'regular-stale']);
});
