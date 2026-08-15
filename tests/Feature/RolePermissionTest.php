<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

function permissionRole(string $name, array $keys = []): Role
{
    $role = Role::create(['role_name' => $name]);

    foreach ($keys as $key) {
        $permission = Permission::firstOrCreate(['key' => $key], [
            'name' => str($key)->replace('.', ' ')->title(),
            'group' => str($key)->before('.')->toString(),
        ]);
        $role->accessPermissions()->attach($permission);
    }

    return $role;
}

it('keeps the legacy primary role while combining supplementary role permissions', function () {
    $primary = permissionRole('User', ['profile.view']);
    $officer = permissionRole('Account Officer', ['followups.view_assigned', 'followups.log_call']);
    $user = User::factory()->create(['role_id' => $primary->id]);
    $user->roles()->attach($officer);

    expect($user->role->is($primary))->toBeTrue()
        ->and($user->hasPermission('profile.view'))->toBeTrue()
        ->and($user->hasPermission('followups.view_assigned'))->toBeTrue()
        ->and($user->hasPermission('roles.manage'))->toBeFalse();
});

it('always grants the protected super admin every permission', function () {
    $user = User::factory()->create(['email' => 'adebsholey4real@gmail.com']);

    expect($user->hasPermission('roles.manage'))->toBeTrue()
        ->and($user->hasPermission('officers.allocate_customers'))->toBeTrue();
});

it('allows only the protected super admin to replace role permissions', function () {
    $target = permissionRole('Support');
    $adminRole = Role::firstOrCreate(['role_name' => 'Admin']);
    $ordinaryAdmin = User::factory()->create(['role_id' => $adminRole->id]);
    $superAdmin = User::factory()->create([
        'role_id' => $adminRole->id,
        'email' => 'adebsholey4real@gmail.com',
    ]);

    $this->actingAs($ordinaryAdmin)
        ->post(route('admin.roles.permissions.update', $target), [
            'permissions' => ['followups.view_assigned'],
        ])
        ->assertForbidden();

    $this->actingAs($superAdmin)
        ->post(route('admin.roles.permissions.update', $target), [
            'permissions' => ['followups.view_assigned', 'followups.log_call'],
        ])
        ->assertRedirect();

    expect($target->fresh()->accessPermissions()->pluck('key')->sort()->values()->all())
        ->toBe(['followups.log_call', 'followups.view_assigned']);
});
