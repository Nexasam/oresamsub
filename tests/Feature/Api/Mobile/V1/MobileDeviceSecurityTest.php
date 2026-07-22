<?php

use App\Models\MobileDeviceInstallation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

function mobileSecurityHeaders(User $user): array
{
    return ['Authorization' => 'Bearer '.$user->createToken('mobile:test', ['mobile'])->plainTextToken];
}

it('registers updates and revokes only the authenticated users device', function () {
    $user = User::factory()->create();
    $headers = mobileSecurityHeaders($user);
    $payload = [
        'device_uuid' => fake()->uuid(),
        'expo_push_token' => 'ExponentPushToken[test_token_123]',
        'platform' => 'android',
        'app_version' => '1.0.0',
        'device_name' => 'Test Phone',
    ];

    $deviceId = postJson('/api/mobile/v1/devices', $payload, $headers)
        ->assertCreated()->assertJsonPath('data.device.enabled', true)->json('data.device.id');
    postJson('/api/mobile/v1/devices', [...$payload, 'app_version' => '1.0.1'], $headers)->assertOk();
    expect(MobileDeviceInstallation::count())->toBe(1)->and(MobileDeviceInstallation::first()->app_version)->toBe('1.0.1');

    deleteJson("/api/mobile/v1/devices/$deviceId", [], $headers)->assertOk();
    expect(MobileDeviceInstallation::first()->enabled)->toBeFalse();
});

it('stores transactional and promotional preferences separately', function () {
    $user = User::factory()->create();
    $headers = mobileSecurityHeaders($user);

    getJson('/api/mobile/v1/notification-preferences', $headers)
        ->assertOk()->assertJsonPath('data.transactional_enabled', true)->assertJsonPath('data.promotional_enabled', false);
    putJson('/api/mobile/v1/notification-preferences', ['transactional_enabled' => true, 'promotional_enabled' => true], $headers)
        ->assertOk()->assertJsonPath('data.promotional_enabled', true);
});

it('changes password and pin only after verifying existing credentials', function () {
    $user = User::factory()->create(['password' => Hash::make('OldPassword123!'), 'pin' => '1234']);
    $headers = mobileSecurityHeaders($user);

    putJson('/api/mobile/v1/security/password', [
        'current_password' => 'OldPassword123!', 'password' => 'NewPassword123!', 'password_confirmation' => 'NewPassword123!',
    ], $headers)->assertOk();
    expect(Hash::check('NewPassword123!', $user->fresh()->password))->toBeTrue();

    app('auth')->forgetGuards();
    $headers = mobileSecurityHeaders($user->fresh());
    putJson('/api/mobile/v1/security/pin', ['current_pin' => '9999', 'pin' => '5678', 'pin_confirmation' => '5678'], $headers)->assertUnprocessable();
    putJson('/api/mobile/v1/security/pin', ['current_pin' => '1234', 'pin' => '5678', 'pin_confirmation' => '5678'], $headers)->assertOk();
    expect($user->fresh()->pin)->toBe('5678');
});
