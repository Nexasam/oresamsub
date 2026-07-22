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
use App\Services\Mobile\MobileTokenService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;
use RuntimeException;

class AuthController extends Controller
{
    use RespondsToMobileApi;

    public function __construct(private readonly MobileTokenService $tokens) {}

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
            ]);
        });

        event(new Registered($user));

        $tokenData = $this->tokens->issue($user, $request->string('device_name')->toString(), $request);

        return $this->successResponse('Registration successful. Continue with phone verification.', $this->sessionPayload($user, $tokenData), 201);
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

        $tokenData = $this->tokens->issue($user, $request->string('device_name')->toString(), $request);

        return $this->successResponse('Login successful.', $this->sessionPayload($user, $tokenData));
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email:rfc']]);
        $email = mb_strtolower(trim($request->string('email')->toString()));

        if (User::query()->where('email', $email)->exists()) {
            Password::sendResetLink(['email' => $email]);
        }

        return $this->successResponse('If an account matches that email, password reset instructions have been sent.');
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email:rfc'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $status = Password::reset($validated, function (User $user, string $password) {
            $user->forceFill(['password' => $password])->save();
            $user->tokens()->delete();
            $user->mobileRefreshTokens()->whereNull('revoked_at')->update(['revoked_at' => now()]);
        });

        if ($status !== Password::PASSWORD_RESET) {
            return $this->errorResponse('The password reset token is invalid or expired.', null, 422);
        }

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
