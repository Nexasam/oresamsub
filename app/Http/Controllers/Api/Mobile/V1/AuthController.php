<?php

namespace App\Http\Controllers\Api\Mobile\V1;

use App\Http\Controllers\Api\Mobile\V1\Concerns\RespondsToMobileApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Mobile\V1\LoginRequest;
use App\Http\Requests\Api\Mobile\V1\RefreshTokenRequest;
use App\Http\Requests\Api\Mobile\V1\RegisterRequest;
use App\Http\Resources\Api\Mobile\V1\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Models\UserPlan;
use App\Services\BonusService;
use App\Services\Mobile\MobileEmailOtpService;
use App\Services\Mobile\MobilePasswordResetOtpService;
use App\Services\Mobile\MobileTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;
use RuntimeException;

class AuthController extends Controller
{
    use RespondsToMobileApi;

    public function __construct(
        private readonly MobileTokenService $tokens,
        private readonly BonusService $bonuses,
        private readonly MobileEmailOtpService $emailOtp,
        private readonly MobilePasswordResetOtpService $passwordResetOtp,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $role = Role::query()->where('role_name', 'User')->first();
        $defaultPlan = UserPlan::query()->where('is_default', 1)->first();

        if (! $role || ! $defaultPlan) {
            return $this->errorResponse('Registration is temporarily unavailable. Please contact support.', null, 503);
        }

        $uplineId = User::query()
            ->where('phone_number', $request->input('referral_phone_number'))
            ->value('id');

        $user = DB::transaction(function () use ($request, $role, $defaultPlan, $uplineId) {
            return User::create([
                'first_name' => $request->string('first_name')->toString(),
                'last_name' => $request->string('last_name')->toString(),
                'username' => $request->string('username')->toString(),
                'email' => $request->string('email')->toString(),
                'password' => $request->string('password')->toString(),
                'role_id' => $role->id,
                'user_plan_id' => $defaultPlan->id,
                'upline_id' => $uplineId,
                'pin' => null,
                'phone_verification' => false,
                'email_verified_at' => null,
            ]);
        });

        $this->bonuses->captureRegistrationContext($user, $request);
        $this->emailOtp->issue($user);

        return $this->successResponse('Account created. Enter the six-digit code sent to your email.', [
            'email' => $user->email,
            'verification_required' => true,
        ], 201);
    }

    public function resendEmailVerification(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email:rfc']]);
        $email = mb_strtolower(trim($request->string('email')->toString()));
        $user = User::query()->where('email', $email)->first();

        if ($user && ! $user->hasVerifiedEmail() && ! (bool) $user->is_deactivated) {
            $this->emailOtp->issue($user);
        }

        return $this->successResponse('If that email belongs to an unverified account, a new verification code has been sent.');
    }

    public function verifyEmailOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email:rfc'],
            'otp' => ['required', 'digits:6'],
            'device_name' => ['required', 'string', 'max:120'],
        ]);
        $user = User::query()->where('email', mb_strtolower(trim($validated['email'])))->first();

        if (! $user || (bool) $user->is_deactivated || ! $this->emailOtp->verify($user, $validated['otp'])) {
            return $this->errorResponse('The verification code is invalid or expired.', null, 422);
        }

        $this->bonuses->evaluate($user, $request);
        $tokenData = $this->tokens->issue($user, $validated['device_name'], $request);

        return $this->successResponse('Email verified successfully.', $this->sessionPayload($user->fresh(), $tokenData));
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $login = trim($request->string('login')->toString());
        $normalizedEmail = mb_strtolower($login);

        $user = User::query()
            ->where('email', $normalizedEmail)
            ->orWhere('username', $login)
            ->orWhere('phone_number', $login)
            ->first();

        if (! $user || ! Hash::check($request->string('password')->toString(), $user->password)) {
            return $this->errorResponse('The provided credentials are incorrect.', null, 401);
        }

        if ((bool) $user->is_deactivated) {
            return $this->errorResponse('This account has been deactivated. Please contact support.', null, 403);
        }

        if (! $user->hasVerifiedEmail()) {
            return $this->errorResponse('Please verify your email address before signing in.', null, 403);
        }

        // Temporary mobile-app bypass while phone OTP verification is disabled.
        // Remove this block when phone verification becomes compulsory again.
        if (! (bool) $user->phone_verification) {
            $user->update(['phone_verification' => true]);
        }

        $this->bonuses->captureLoginContext($user, $request);
        $this->bonuses->evaluate($user, $request);
        $tokenData = $this->tokens->issue($user, $request->string('device_name')->toString(), $request);

        return $this->successResponse('Login successful.', $this->sessionPayload($user, $tokenData));
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email:rfc']]);
        $email = mb_strtolower(trim($request->string('email')->toString()));

        $user = User::query()->where('email', $email)->first();
        if ($user && ! (bool) $user->is_deactivated) {
            $this->passwordResetOtp->issue($user);
        }

        return $this->successResponse('If an account matches that email, a password reset code has been sent.');
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'otp' => ['required', 'digits:6'],
            'email' => ['required', 'email:rfc'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $user = User::query()->where('email', mb_strtolower(trim($validated['email'])))->first();
        if (! $user || (bool) $user->is_deactivated || ! $this->passwordResetOtp->consume($user, $validated['otp'])) {
            return $this->errorResponse('The password reset code is invalid or expired.', null, 422);
        }

        $user->forceFill(['password' => Hash::make($validated['password'])])->save();
        $user->tokens()->delete();
        $user->mobileRefreshTokens()->whereNull('revoked_at')->update(['revoked_at' => now()]);

        return $this->successResponse('Password reset successfully. Sign in with your new password.');
    }

    public function refresh(RefreshTokenRequest $request): JsonResponse
    {
        try {
            $result = $this->tokens->rotate($request->string('refresh_token')->toString(), $request);
        } catch (RuntimeException) {
            return $this->errorResponse('The refresh token is invalid, expired or revoked.', null, 401);
        }

        if ((bool) $result['user']->is_deactivated) {
            return $this->errorResponse('This account has been deactivated. Please contact support.', null, 403);
        }

        if (! $result['user']->hasVerifiedEmail()) {
            $result['user']->tokens()->delete();
            $result['user']->mobileRefreshTokens()->whereNull('revoked_at')->update(['revoked_at' => now()]);

            return $this->errorResponse('Please verify your email address before signing in.', null, 403);
        }

        return $this->successResponse('Session refreshed successfully.', $this->sessionPayload($result['user'], $result['tokens']));
    }

    public function session(Request $request): JsonResponse
    {
        return $this->successResponse('Session fetched successfully.', $this->sessionPayload($request->user()));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->successResponse('Logged out successfully.');
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        $request->user()->mobileRefreshTokens()->whereNull('revoked_at')->update(['revoked_at' => now()]);

        return $this->successResponse('Logged out from all devices successfully.');
    }

    private function sessionPayload(User $user, ?array $tokens = null): array
    {
        return array_filter([
            'user' => UserResource::make($user)->resolve(),
            'tokens' => $tokens,
            'onboarding' => [
                'phone_verified' => (bool) $user->phone_verification,
                'transaction_pin_set' => filled($user->pin),
                'profile_complete' => filled($user->first_name) && filled($user->last_name) && filled($user->email),
            ],
        ], fn ($value) => $value !== null);
    }
}
