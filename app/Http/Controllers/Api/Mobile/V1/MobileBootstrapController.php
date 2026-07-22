<?php

namespace App\Http\Controllers\Api\Mobile\V1;

use App\Http\Controllers\Api\Mobile\V1\Concerns\RespondsToMobileApi;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class MobileBootstrapController extends Controller
{
    use RespondsToMobileApi;

    public function health(): JsonResponse
    {
        return $this->successResponse(
            message: 'OresamSub mobile API is available.',
            data: [
                'api_version' => config('mobile.api_version'),
                'server_time' => now()->toIso8601String(),
            ],
        );
    }

    public function config(): JsonResponse
    {
        return $this->successResponse(
            message: 'Mobile configuration fetched successfully.',
            data: [
                'api_version' => config('mobile.api_version'),
                'minimum_app_version' => config('mobile.minimum_app_version'),
                'latest_app_version' => config('mobile.latest_app_version'),
                'force_update' => config('mobile.force_update'),
                'maintenance_mode' => config('mobile.maintenance_mode'),
                'maintenance_message' => config('mobile.maintenance_message'),
                'store_urls' => ['android' => config('mobile.android_store_url'), 'ios' => config('mobile.ios_store_url')],
                'legal' => ['privacy_url' => config('mobile.privacy_url'), 'terms_url' => config('mobile.terms_url'), 'account_deletion_url' => config('mobile.account_deletion_url')],
                'features' => config('mobile.features'),
            ],
        );
    }
}
