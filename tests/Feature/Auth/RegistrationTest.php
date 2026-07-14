<?php

use App\Models\Role;
use App\Models\UserPlan;
use Illuminate\Support\Facades\Event;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    Event::fake([App\Events\Registered::class]);

    Role::create(['role_name' => 'User']);
    UserPlan::create([
        'user_plan_name' => 'Default',
        'plan_level' => 1,
        'is_default' => 1,
        'visibility' => 1,
    ]);

    $response = $this->post('/register', [
        'username' => 'testuser',
        'first_name' => 'Test',
        'last_name' => 'User',
        'pin' => '1234',
        'phone_number' => '08012345678',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    $this->assertAuthenticated();
});
