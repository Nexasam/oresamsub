<?php

use App\Models\AccountOfficerProfile;
use App\Models\CustomerOfficerAssignment;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Notifications\CustomersAssignedNotification;
use App\Services\AccountOfficers\WeightedCustomerAllocator;
use Illuminate\Support\Facades\Notification;

function officerUser(string $email, int $weight): User
{
    $user = User::factory()->create(['email' => $email]);
    $role = Role::firstOrCreate(['role_name' => 'Account Officer']);
    $user->roles()->syncWithoutDetaching([$role->id]);
    AccountOfficerProfile::create(['user_id' => $user->id, 'is_active' => true, 'allocation_weight' => $weight]);

    return $user;
}

function officerPermissionRole(string $name, array $keys): Role
{
    $role = Role::create(['role_name' => $name]);
    foreach ($keys as $key) {
        $permission = Permission::firstOrCreate(['key' => $key], [
            'name' => str($key)->after('.')->replace('_', ' ')->title(),
            'group' => str($key)->before('.'),
        ]);
        $role->accessPermissions()->attach($permission);
    }
    return $role;
}

it('allocates only unassigned customers according to officer weights', function () {
    $admin = User::factory()->create(['email' => 'adebsholey4real@gmail.com']);
    $officer1 = officerUser('officer1@example.com', 25);
    $officer2 = officerUser('officer2@example.com', 30);
    $officer3 = officerUser('officer3@example.com', 45);
    $customerRole = Role::firstOrCreate(['role_name' => 'User']);
    $customers = User::factory()->count(21)->create(['role_id' => $customerRole->id]);

    CustomerOfficerAssignment::create([
        'customer_id' => $customers->first()->id,
        'officer_id' => $officer1->id,
        'assigned_by' => $admin->id,
        'started_at' => now(),
    ]);

    $result = app(WeightedCustomerAllocator::class)->allocate($admin);

    expect($result->total_customers)->toBe(20)
        ->and(CustomerOfficerAssignment::whereNull('ended_at')->where('officer_id', $officer1->id)->count())->toBe(6)
        ->and(CustomerOfficerAssignment::whereNull('ended_at')->where('officer_id', $officer2->id)->count())->toBe(6)
        ->and(CustomerOfficerAssignment::whereNull('ended_at')->where('officer_id', $officer3->id)->count())->toBe(9);

    expect(app(WeightedCustomerAllocator::class)->allocate($admin)->total_customers)->toBe(0);
});

it('rejects allocation when active officer weights do not total one hundred', function () {
    $admin = User::factory()->create(['email' => 'adebsholey4real@gmail.com']);
    officerUser('officer-a@example.com', 40);
    officerUser('officer-b@example.com', 40);

    expect(fn () => app(WeightedCustomerAllocator::class)->allocate($admin))
        ->toThrow(InvalidArgumentException::class, '100');
});

it('preserves historical ownership when an assignment ends', function () {
    $admin = User::factory()->create(['email' => 'adebsholey4real@gmail.com']);
    $officer = officerUser('history-officer@example.com', 100);
    $customer = User::factory()->create();
    $assignment = CustomerOfficerAssignment::create([
        'customer_id' => $customer->id,
        'officer_id' => $officer->id,
        'assigned_by' => $admin->id,
        'started_at' => now()->subMonth(),
    ]);

    $assignment->update(['ended_at' => now()]);

    expect($customer->fresh()->currentOfficerAssignment)->toBeNull()
        ->and($customer->officerAssignments()->count())->toBe(1);
});

it('allows only the protected super admin to manage and execute officer allocation', function () {
    Notification::fake();
    $super = User::factory()->create(['email' => 'adebsholey4real@gmail.com']);
    $ordinary = User::factory()->create();
    $staffRole = Role::create(['role_name' => 'Staff']);
    $staff = User::factory()->create(['email' => 'new-officer@example.com', 'role_id' => $staffRole->id]);

    $this->actingAs($ordinary)->post(route('admin.account_officers.store'), [
        'user_id' => $staff->id, 'allocation_weight' => 100,
    ])->assertForbidden();

    $this->actingAs($super)->post(route('admin.account_officers.store'), [
        'user_id' => $staff->id, 'allocation_weight' => 100,
    ])->assertRedirect();

    expect($staff->fresh()->hasPermission('followups.view_assigned'))->toBeTrue()
        ->and($staff->fresh()->hasPermission('followups.log_call'))->toBeTrue();

    $this->actingAs($super)->post(route('admin.account_officers.allocate'))->assertRedirect();
    expect($staff->fresh()->accountOfficerProfile)->not->toBeNull();
    Notification::assertSentTo($staff, CustomersAssignedNotification::class);
});

it('restricts an officer to their assigned customer portfolio and calls', function () {
    $officer = officerUser('scoped-officer@example.com', 100);
    $permissionRole = officerPermissionRole('Officer Access', ['followups.view_assigned', 'followups.log_call']);
    $officer->roles()->attach($permissionRole);
    $ownCustomer = User::factory()->create(['username' => 'my-assigned-customer']);
    $otherCustomer = User::factory()->create(['username' => 'someone-elses-customer']);
    CustomerOfficerAssignment::create([
        'customer_id' => $ownCustomer->id, 'officer_id' => $officer->id,
        'started_at' => now(), 'assigned_by' => $officer->id,
    ]);

    $this->actingAs($officer)->get(route('admin.daily_customer_followup.index'))
        ->assertOk()->assertSee('my-assigned-customer')->assertDontSee('someone-elses-customer');

    $payload = ['outcome' => 'answered', 'feedback' => 'Customer is ready to return.', 'followup_status' => 'resolved_reactivated'];
    $this->actingAs($officer)->post(route('admin.daily_customer_followup.calls.store', $ownCustomer), $payload)->assertRedirect();
    $this->actingAs($officer)->post(route('admin.daily_customer_followup.calls.store', $otherCustomer), $payload)->assertForbidden();
});
