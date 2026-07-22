<?php

namespace App\Http\Controllers\Api\Mobile\V1;

use App\Http\Controllers\Api\Mobile\V1\Concerns\RespondsToMobileApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Mobile\V1\UpdateProfileRequest;
use App\Http\Resources\Api\Mobile\V1\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileProfileController extends Controller
{
    use RespondsToMobileApi;

    public function show(Request $request): JsonResponse
    {
        return $this->successResponse('Profile fetched successfully.', [
            'user' => (new UserResource($request->user()))->resolve($request),
        ]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update($request->validated());

        return $this->successResponse('Profile updated successfully.', [
            'user' => (new UserResource($user->fresh()))->resolve($request),
        ]);
    }
}
