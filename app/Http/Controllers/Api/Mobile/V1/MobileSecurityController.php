<?php

namespace App\Http\Controllers\Api\Mobile\V1;

use App\Http\Controllers\Api\Mobile\V1\Concerns\RespondsToMobileApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Mobile\V1\ChangePasswordRequest;
use App\Http\Requests\Api\Mobile\V1\ChangePinRequest;
use App\Http\Requests\Api\Mobile\V1\DeactivateAccountRequest;
use App\Http\Requests\Api\Mobile\V1\RequestAccountDeletionRequest;
use App\Models\MobileAccountDeletionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
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

    public function requestDeletion(RequestAccountDeletionRequest $request): JsonResponse
    {
        $user = $request->user();
        $retentionNotice = 'Profile data not required for security, fraud prevention, disputes, financial recordkeeping or legal compliance will be deleted or anonymized after verification. Required transaction and compliance records may be retained for the applicable legal period.';

        $deletionRequest = DB::transaction(function () use ($request, $retentionNotice, $user) {
            $deletionRequest = MobileAccountDeletionRequest::query()
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->first();

            if (! $deletionRequest) {
                $deletionRequest = MobileAccountDeletionRequest::create([
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'username' => $user->username,
                    'status' => 'pending',
                    'reason' => $request->string('reason')->trim()->toString() ?: null,
                    'retention_notice' => $retentionNotice,
                    'requested_at' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            $user->update(['is_deactivated' => true]);
            $user->tokens()->delete();
            $user->mobileRefreshTokens()->update(['revoked_at' => now()]);
            $user->mobileDeviceInstallations()->update(['enabled' => false, 'revoked_at' => now()]);

            return $deletionRequest;
        });

        return $this->successResponse('Your account deletion request has been submitted. Access has been disabled and the request will be reviewed within 30 days.', [
            'request' => [
                'id' => $deletionRequest->id,
                'status' => $deletionRequest->status,
                'requested_at' => $deletionRequest->requested_at?->toIso8601String(),
                'retention_notice' => $deletionRequest->retention_notice,
            ],
        ], 202);
    }
}
