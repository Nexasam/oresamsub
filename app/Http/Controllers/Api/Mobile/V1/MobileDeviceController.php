<?php

namespace App\Http\Controllers\Api\Mobile\V1;

use App\Http\Controllers\Api\Mobile\V1\Concerns\RespondsToMobileApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Mobile\V1\RegisterDeviceRequest;
use App\Http\Requests\Api\Mobile\V1\UpdateNotificationPreferencesRequest;
use App\Jobs\SendMobilePushNotification;
use App\Models\MobileDeviceInstallation;
use App\Models\MobileNotificationPreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MobileDeviceController extends Controller
{
    use RespondsToMobileApi;

    public function store(RegisterDeviceRequest $request): JsonResponse
    {
        $data = $request->validated();
        MobileDeviceInstallation::where('expo_push_token', $data['expo_push_token'])->where('user_id', '!=', $request->user()->id)
            ->get()->each(fn (MobileDeviceInstallation $old) => $old->update([
                'expo_push_token' => 'revoked:'.$old->id,
                'enabled' => false,
                'revoked_at' => now(),
            ]));
        $device = MobileDeviceInstallation::updateOrCreate(
            ['user_id' => $request->user()->id, 'device_uuid' => $data['device_uuid']],
            [...$data, 'enabled' => true, 'last_seen_at' => now(), 'revoked_at' => null]
        );

        return $this->successResponse('Device registered successfully.', ['device' => ['id' => $device->id, 'enabled' => $device->enabled]], $device->wasRecentlyCreated ? 201 : 200);
    }

    public function destroy(Request $request, string $device): JsonResponse
    {
        $installation = MobileDeviceInstallation::where('user_id', $request->user()->id)->whereKey($device)->firstOrFail();
        $installation->update(['enabled' => false, 'revoked_at' => now()]);

        return $this->successResponse('Device notifications disabled.');
    }

    public function status(Request $request): JsonResponse
    {
        $registeredDevices = MobileDeviceInstallation::query()
            ->where('user_id', $request->user()->id)
            ->where('enabled', true)
            ->whereNull('revoked_at')
            ->count();

        return $this->successResponse('Push notification status fetched.', [
            'registered_devices' => $registeredDevices,
            'push_ready' => $registeredDevices > 0,
        ]);
    }

    public function testNotification(Request $request): JsonResponse
    {
        $hasDevice = MobileDeviceInstallation::query()
            ->where('user_id', $request->user()->id)
            ->where('enabled', true)
            ->whereNull('revoked_at')
            ->exists();

        if (! $hasDevice) {
            return $this->errorResponse('This phone is not registered for push notifications. Allow notifications and try again.', null, 422);
        }

        SendMobilePushNotification::dispatch(
            (string) $request->user()->id,
            'test:'.Str::uuid(),
            'OresamSub notifications are ready',
            'You will receive transaction and wallet updates on this phone.',
            ['screen' => 'wallet'],
        );

        return $this->successResponse('Test notification queued. It should arrive shortly.', ['queued' => true], 202);
    }

    public function preferences(Request $request): JsonResponse
    {
        $preferences = MobileNotificationPreference::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['transactional_enabled' => true, 'promotional_enabled' => false]
        );

        return $this->successResponse('Notification preferences fetched.', ['transactional_enabled' => $preferences->transactional_enabled, 'promotional_enabled' => $preferences->promotional_enabled]);
    }

    public function updatePreferences(UpdateNotificationPreferencesRequest $request): JsonResponse
    {
        $preferences = MobileNotificationPreference::updateOrCreate(['user_id' => $request->user()->id], $request->validated());

        return $this->successResponse('Notification preferences updated.', ['transactional_enabled' => $preferences->transactional_enabled, 'promotional_enabled' => $preferences->promotional_enabled]);
    }
}
