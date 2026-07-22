<?php

use App\Jobs\SendMobilePushNotification;
use App\Models\MobileDeviceInstallation;
use App\Models\MobilePushDelivery;
use App\Models\User;
use Illuminate\Support\Facades\Http;

it('delivers a sanitized transactional push and deduplicates its event key', function () {
    Http::fake(['exp.host/*' => Http::response(['data' => ['status' => 'ok', 'id' => 'ticket-1']])]);
    $user = User::factory()->create();
    MobileDeviceInstallation::create([
        'user_id' => $user->id, 'device_uuid' => fake()->uuid(), 'expo_push_token' => 'ExponentPushToken[push_delivery_test]',
        'platform' => 'android', 'enabled' => true,
    ]);
    $job = new SendMobilePushNotification($user->id, 'transaction:test:successful', 'Transaction update', 'Your data transaction is successful.', ['transaction_id' => 'safe-id']);
    $job->handle();
    $job->handle();

    Http::assertSentCount(1);
    Http::assertSent(fn ($request) => $request['body'] === 'Your data transaction is successful.' && ! str_contains(json_encode($request->data()), 'balance'));
    expect(MobilePushDelivery::count())->toBe(1)->and(MobilePushDelivery::first()->status)->toBe('sent');
});

it('revokes a token rejected as unregistered by Expo', function () {
    Http::fake(['exp.host/*' => Http::response(['data' => ['status' => 'error', 'details' => ['error' => 'DeviceNotRegistered']]])]);
    $user = User::factory()->create();
    $device = MobileDeviceInstallation::create([
        'user_id' => $user->id, 'device_uuid' => fake()->uuid(), 'expo_push_token' => 'ExponentPushToken[invalid_test]',
        'platform' => 'ios', 'enabled' => true,
    ]);
    (new SendMobilePushNotification($user->id, 'security:test', 'Security update', 'Your account security changed.'))->handle();

    expect($device->fresh()->enabled)->toBeFalse()->and($device->fresh()->revoked_at)->not->toBeNull();
});
