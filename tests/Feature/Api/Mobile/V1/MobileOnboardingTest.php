<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\postJson;

it('sends and verifies a real provider-backed phone otp flow', function () {
    Http::fake([
        'api.ng.termii.com/api/sms/otp/send' => Http::response(['pinId' => 'termii-pin-123'], 200),
        'api.ng.termii.com/api/sms/otp/verify' => Http::response(['verified' => true], 200),
    ]);

    $user = User::factory()->create(['phone_number' => null, 'phone_verification' => false, 'pin' => null]);
    $token = $user->createToken('mobile:test', ['mobile'], now()->addMinutes(15))->plainTextToken;
    $headers = ['Authorization' => 'Bearer '.$token];

    postJson('/api/mobile/v1/auth/phone/send-otp', ['phone_number' => '08012345678'], $headers)
        ->assertOk()
        ->assertJsonPath('success', true);

    expect($user->refresh()->termii_pin_id)->toBe('termii-pin-123')
        ->and($user->phone_number)->toBe('08012345678');

    postJson('/api/mobile/v1/auth/phone/verify-otp', ['otp' => '123456'], $headers)
        ->assertOk();

    expect($user->refresh()->phone_verification)->toBeTruthy()
        ->and($user->termii_pin_id)->toBeNull();
});

it('does not verify a phone when the otp provider rejects the code', function () {
    Http::fake(['api.ng.termii.com/*' => Http::response(['verified' => false], 200)]);

    $user = User::factory()->create(['phone_verification' => false, 'termii_pin_id' => 'termii-pin-123']);
    $token = $user->createToken('mobile:test')->plainTextToken;

    postJson('/api/mobile/v1/auth/phone/verify-otp', ['otp' => '999999'], ['Authorization' => 'Bearer '.$token])
        ->assertUnprocessable()
        ->assertJsonPath('success', false);

    expect($user->refresh()->phone_verification)->toBeFalsy();
});

it('sets and verifies a non-default transaction pin for the authenticated user', function () {
    $user = User::factory()->create(['pin' => null]);
    $token = $user->createToken('mobile:test')->plainTextToken;
    $headers = ['Authorization' => 'Bearer '.$token];

    postJson('/api/mobile/v1/security/pin', ['pin' => '4826', 'pin_confirmation' => '4826'], $headers)
        ->assertOk();

    expect($user->refresh()->pin)->toBe('4826');

    postJson('/api/mobile/v1/security/pin/verify', ['pin' => '4826'], $headers)->assertOk();
    postJson('/api/mobile/v1/security/pin/verify', ['pin' => '1112'], $headers)->assertUnprocessable();
});
