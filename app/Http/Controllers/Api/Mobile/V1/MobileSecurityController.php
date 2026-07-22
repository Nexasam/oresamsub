<?php

namespace App\Http\Controllers\Api\Mobile\V1;

use App\Http\Controllers\Api\Mobile\V1\Concerns\RespondsToMobileApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Mobile\V1\ChangePasswordRequest;
use App\Http\Requests\Api\Mobile\V1\ChangePinRequest;
use App\Http\Requests\Api\Mobile\V1\DeactivateAccountRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class MobileSecurityController extends Controller
{
    use RespondsToMobileApi;

    public function password(ChangePasswordRequest $request): JsonResponse
    {
        $request->user()->update(['password' => Hash::make($request->string('password')->toString())]);
        $request->user()->tokens()->whereKeyNot($request->user()->currentAccessToken()?->id)->delete();
        $request->user()->mobileRefreshTokens()->update(['revoked_at' => now()]);

        return $this->successResponse('Password changed successfully. Other sessions have been signed out.');
    }

    public function pin(ChangePinRequest $request): JsonResponse
    {
        if (! hash_equals((string) $request->user()->pin, $request->string('current_pin')->toString())) {
            return $this->errorResponse('The current transaction PIN is incorrect.', null, 422);
        }

        $request->user()->update(['pin' => $request->string('pin')->toString()]);

        return $this->successResponse('Transaction PIN changed successfully.');
    }

    public function deactivate(DeactivateAccountRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update(['is_deactivated' => true]);
        $user->tokens()->delete();
        $user->mobileRefreshTokens()->update(['revoked_at' => now()]);

        return $this->successResponse('Your account has been deactivated. Contact support if you need help restoring it.');
    }
}
