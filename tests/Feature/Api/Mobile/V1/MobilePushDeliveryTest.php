<?php

use App\Jobs\CheckExpoPushReceipt;
use App\Jobs\SendMobilePushNotification;
use App\Models\MobileDeviceInstallation;
use App\Models\MobileNotificationPreference;
use App\Models\MobilePushDelivery;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

it('delivers a sanitized transactional push and deduplicates its event key', function () {
    Queue::fake([CheckExpoPushReceipt::class]);
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
    Queue::assertPushed(CheckExpoPushReceipt::class);
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

it('checks Expo receipts and records confirmed delivery', function () {
    $user = User::factory()->create();
    $device = MobileDeviceInstallation::create([
        'user_id' => $user->id, 'device_uuid' => fake()->uuid(), 'expo_push_token' => 'ExponentPushToken[receipt_test]',
        'platform' => 'android', 'enabled' => true,
    ]);
    $delivery = MobilePushDelivery::create([
        'mobile_device_installation_id' => $device->id,
        'event_key' => 'receipt:test',
        'category' => 'transactional',
        'status' => 'sent',
        'expo_ticket_id' => 'ticket-receipt-1',
    ]);
    Http::fake([
        'exp.host/*' => Http::response(['data' => [
            'ticket-receipt-1' => ['status' => 'ok'],
        ]]),
    ]);

    (new CheckExpoPushReceipt($delivery->id))->handle();

    expect($delivery->fresh()->status)->toBe('delivered')
        ->and($delivery->fresh()->delivered_at)->not->toBeNull();
});

it('delivers an admin broadcast to an enabled app installation independently of ordinary preferences', function () {
    Queue::fake([CheckExpoPushReceipt::class]);
    Http::fake(['exp.host/*' => Http::response(['data' => ['status' => 'ok', 'id' => 'ticket-broadcast']])]);
    $user = User::factory()->create();
    MobileNotificationPreference::create([
        'user_id' => $user->id,
        'transactional_enabled' => false,
        'promotional_enabled' => false,
    ]);
    MobileDeviceInstallation::create([
        'user_id' => $user->id,
        'device_uuid' => fake()->uuid(),
        'expo_push_token' => 'ExponentPushToken[broadcast_delivery_test]',
        'platform' => 'android',
        'enabled' => true,
    ]);

    (new SendMobilePushNotification(
        $user->id,
        'campaign:test',
        'Campaign title',
        'Campaign message',
        ['screen' => 'dashboard'],
        false,
        'broadcast',
    ))->handle();

    Http::assertSentCount(1);
    expect(MobilePushDelivery::first()->category)->toBe('broadcast');
});
