<?php

namespace App\Http\Controllers\Api\Mobile\V1;

use App\Http\Controllers\Api\Mobile\V1\Concerns\RespondsToMobileApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Mobile\V1\RegisterDeviceRequest;
use App\Http\Requests\Api\Mobile\V1\UpdateNotificationPreferencesRequest;
use App\Models\MobileDeviceInstallation;
use App\Models\MobileNotificationPreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
