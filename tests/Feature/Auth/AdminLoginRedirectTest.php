<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('an admin login leaves inertia before loading the blade dashboard', function () {
    $adminRole = Role::create(['role_name' => 'Admin']);
    $admin = User::factory()->create([
        'role_id' => $adminRole->id,
        'password' => 'password',
    ]);

    $response = $this->withHeader('X-Inertia', 'true')->post(route('inertia.login.store'), [
        'email' => $admin->email,
        'password' => 'password',
    ]);

    $response
        ->assertStatus(409)
        ->assertHeader('X-Inertia-Location', route('dashboard'));

    $this->assertAuthenticatedAs($admin);
});
