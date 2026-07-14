<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\UserPlan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $role = Role::firstOrCreate([
            'role_name' => 'User',
        ]);

        $userPlan = UserPlan::firstOrCreate(
            ['user_plan_name' => 'Default'],
            [
                'plan_level' => 1,
                'is_default' => 1,
                'visibility' => 1,
            ]
        );

        $extension = ['0812','0803','0807','0803'];
        return [
            'username' => fake()->unique()->name(),
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'pin' => rand(1111,9999),
            'phone_number' => $extension[rand(0,3)].rand(1111111,9999999),
            'role_id' => $role->id,
            'user_plan_id' => $userPlan->id,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
