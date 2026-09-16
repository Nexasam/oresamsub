<?php

use App\Models\MobileAccountDeletionRequest;
use App\Models\MobileDeviceInstallation;
use App\Models\MobileRefreshToken;
use App\Models\Role;
use App\Models\User;
use App\Models\UserPlan;
use App\Notifications\MobileVerifyEmailNotification;
use App\Notifications\MobilePasswordResetOtpNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\getJson;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\postJson;

it('registers a user without silently assigning a transaction pin', function () {
    Notification::fake();
    Role::create(['role_name' => 'User']);
    UserPlan::create([
        'user_plan_name' => 'Default',
        'plan_level' => 1,
        'is_default' => 1,
        'visibility' => 1,
    ]);

    $response = postJson('/api/mobile/v1/auth/register', [
        'first_name' => 'Mobile',
        'last_name' => 'Customer',
        'username' => 'mobilecustomer',
        'email' => 'new-mobile@example.com',
        'password' => 'SecurePass123!',
        'password_confirmation' => 'SecurePass123!',
        'device_name' => 'Test iPhone',
        'terms_accepted' => true,
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.email', 'new-mobile@example.com')
        ->assertJsonPath('data.verification_required', true)
        ->assertJsonMissingPath('data.tokens');

    $user = User::where('email', 'new-mobile@example.com')->firstOrFail();

    expect($user->pin)->toBeNull()
        ->and($user->email_verified_at)->toBeNull()
        ->and(Hash::check('SecurePass123!', $user->password))->toBeTrue();
    Notification::assertSentTo($user, MobileVerifyEmailNotification::class, fn ($notification) => preg_match('/^\d{6}$/', $notification->code) === 1);
});

it('verifies a mobile email with an otp and starts the device session', function () {
    Notification::fake();
    Role::firstOrCreate(['role_name' => 'User']);
    UserPlan::firstOrCreate(['plan_level' => 1], [
        'user_plan_name' => 'Default', 'is_default' => 1, 'visibility' => 1,
    ]);

    postJson('/api/mobile/v1/auth/register', [
        'first_name' => 'Otp', 'last_name' => 'Customer', 'username' => 'otpcustomer',
        'email' => 'otp-mobile@example.com', 'password' => 'SecurePass123!',
        'password_confirmation' => 'SecurePass123!', 'device_name' => 'Test iPhone',
        'terms_accepted' => true,
    ])->assertCreated();

    $user = User::where('email', 'otp-mobile@example.com')->firstOrFail();
    $otp = null;
    Notification::assertSentTo($user, MobileVerifyEmailNotification::class, function ($notification) use (&$otp) {
        $otp = $notification->code;
        return true;
    });

    postJson('/api/mobile/v1/auth/email/verify-otp', [
        'email' => $user->email,
        'otp' => $otp,
        'device_name' => 'Test iPhone',
    ])->assertOk()
        ->assertJsonPath('data.user.email', $user->email)
        ->assertJsonStructure(['data' => ['tokens' => ['access_token', 'refresh_token']]]);

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue()
        ->and(MobileRefreshToken::where('user_id', $user->id)->count())->toBe(1);
});

it('verifies a mobile email through its temporary signed browser link', function () {
    $user = User::factory()->unverified()->create();
    $url = URL::temporarySignedRoute('mobile.email.verify', now()->addMinutes(10), [
        'id' => $user->id,
        'hash' => sha1($user->getEmailForVerification()),
    ]);

    $this->get($url)
        ->assertOk()
        ->assertSee('Email verified')
        ->assertSee($user->email);

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

it('resends mobile email verification without exposing unknown accounts', function () {
    Notification::fake();
    $user = User::factory()->unverified()->create(['email' => 'verify-mobile@example.com']);

    postJson('/api/mobile/v1/auth/email/resend', ['email' => $user->email])
        ->assertOk()
        ->assertJsonPath('success', true);
    Notification::assertSentTo($user, MobileVerifyEmailNotification::class);

    postJson('/api/mobile/v1/auth/email/resend', ['email' => 'unknown@example.com'])
        ->assertOk()
        ->assertJsonPath('message', 'If that email belongs to an unverified account, a new verification code has been sent.');
});

it('logs in with an exact identifier and returns a device session', function () {
    $user = User::factory()->create([
        'email' => 'mobile@example.com',
        'password' => Hash::make('SecurePass123!'),
        'phone_verification' => false,
    ]);

    $response = postJson('/api/mobile/v1/auth/login', [
        'login' => 'mobile@example.com',
        'password' => 'SecurePass123!',
        'device_name' => 'Test iPhone',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.user.id', $user->id)
        ->assertJsonPath('data.onboarding.phone_verified', true)
        ->assertJsonStructure([
            'data' => [
                'user',
                'tokens' => [
                    'access_token',
                    'access_token_expires_at',
                    'refresh_token',
                    'refresh_token_expires_at',
                    'token_type',
                ],
                'onboarding',
            ],
        ]);

    expect(MobileRefreshToken::where('user_id', $user->id)->count())->toBe(1)
        ->and($user->refresh()->phone_verification)->toBeTruthy();
});

it('rejects incorrect credentials without creating a session', function () {
    User::factory()->create([
        'email' => 'mobile@example.com',
        'password' => Hash::make('SecurePass123!'),
    ]);

    postJson('/api/mobile/v1/auth/login', [
        'login' => 'mobile@example.com',
        'password' => 'wrong-password',
        'device_name' => 'Test Android',
    ])
        ->assertUnauthorized()
        ->assertJsonPath('success', false);

    expect(MobileRefreshToken::count())->toBe(0);
});

it('prevents an unverified email address from signing in', function () {
    User::factory()->unverified()->create([
        'email' => 'unverified@example.com',
        'password' => Hash::make('SecurePass123!'),
    ]);

    postJson('/api/mobile/v1/auth/login', [
        'login' => 'unverified@example.com',
        'password' => 'SecurePass123!',
        'device_name' => 'Test Android',
    ])
        ->assertForbidden()
        ->assertJsonPath('message', 'Please verify your email address before signing in.');

    expect(MobileRefreshToken::count())->toBe(0);
});

it('uses the standard mobile error envelope for validation and authentication failures', function () {
    postJson('/api/mobile/v1/auth/login', [])
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Please check the provided information.')
        ->assertJsonStructure(['data', 'meta', 'errors' => ['login', 'password', 'device_name']]);

    getJson('/api/mobile/v1/auth/session')
        ->assertUnauthorized()
        ->assertJson([
            'success' => false,
            'message' => 'Authentication is required.',
            'data' => null,
            'meta' => null,
            'errors' => null,
        ]);
});

it('resets a password with an emailed otp without exposing whether an account exists', function () {
    Notification::fake();
    $user = User::factory()->create(['email' => 'reset@example.com']);

    postJson('/api/mobile/v1/auth/forgot-password', ['email' => 'reset@example.com'])
        ->assertOk()
        ->assertJsonPath('success', true);

    $otp = null;
    Notification::assertSentTo($user, MobilePasswordResetOtpNotification::class, function ($notification) use (&$otp) {
        $otp = $notification->code;
        return preg_match('/^\d{6}$/', $otp) === 1;
    });

    postJson('/api/mobile/v1/auth/reset-password', [
        'email' => $user->email,
        'otp' => $otp,
        'password' => 'NewSecurePass123!',
        'password_confirmation' => 'NewSecurePass123!',
    ])->assertOk()->assertJsonPath('success', true);

    expect(Hash::check('NewSecurePass123!', $user->fresh()->password))->toBeTrue();

    postJson('/api/mobile/v1/auth/forgot-password', ['email' => 'missing@example.com'])
        ->assertOk()
        ->assertJsonPath('message', 'If an account matches that email, a password reset code has been sent.');
});

it('rotates refresh tokens and rejects replay of the old token', function () {
    User::factory()->create([
        'email' => 'mobile@example.com',
        'password' => Hash::make('SecurePass123!'),
    ]);

    $login = postJson('/api/mobile/v1/auth/login', [
        'login' => 'mobile@example.com',
        'password' => 'SecurePass123!',
        'device_name' => 'Test Android',
    ])->assertOk();

    $oldRefreshToken = $login->json('data.tokens.refresh_token');

    postJson('/api/mobile/v1/auth/refresh', [
        'refresh_token' => $oldRefreshToken,
    ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonMissingExact(['refresh_token' => $oldRefreshToken]);

    postJson('/api/mobile/v1/auth/refresh', [
        'refresh_token' => $oldRefreshToken,
    ])
        ->assertUnauthorized()
        ->assertJsonPath('success', false);
});

it('returns the authenticated session and revokes its access token on logout', function () {
    $user = User::factory()->create();
    $plainAccessToken = $user->createToken('mobile:test', ['mobile'], now()->addMinutes(15))->plainTextToken;

    getJson('/api/mobile/v1/auth/session', [
        'Authorization' => 'Bearer '.$plainAccessToken,
    ])
        ->assertOk()
        ->assertJsonPath('data.user.id', $user->id);

    postJson('/api/mobile/v1/auth/logout', [], [
        'Authorization' => 'Bearer '.$plainAccessToken,
    ])->assertOk();

    expect($user->tokens()->count())->toBe(0);
    app('auth')->forgetGuards();

    getJson('/api/mobile/v1/auth/session', [
        'Authorization' => 'Bearer '.$plainAccessToken,
    ])->assertUnauthorized();
});

it('blocks an already authenticated device after the account is deactivated', function () {
    $user = User::factory()->create(['is_deactivated' => true]);
    $plainAccessToken = $user->createToken('mobile:test')->plainTextToken;

    getJson('/api/mobile/v1/auth/session', ['Authorization' => 'Bearer '.$plainAccessToken])
        ->assertForbidden()
        ->assertJsonPath('success', false);
});

it('deactivates with the dedicated flag without changing email verification', function () {
    $user = User::factory()->create([
        'email' => 'deactivate@example.com',
        'password' => Hash::make('SecurePass123!'),
    ]);
    $verifiedAt = $user->email_verified_at;
    $login = postJson('/api/mobile/v1/auth/login', [
        'login' => 'deactivate@example.com',
        'password' => 'SecurePass123!',
        'device_name' => 'Test Android',
    ])->assertOk();

    deleteJson('/api/mobile/v1/account', [
        'password' => 'SecurePass123!',
        'confirmation' => 'DELETE',
    ], [
        'Authorization' => 'Bearer '.$login->json('data.tokens.access_token'),
    ])->assertOk();

    $user->refresh();
    expect((bool) $user->is_deactivated)->toBeTrue()
        ->and($user->email_verified_at?->equalTo($verifiedAt))->toBeTrue()
        ->and($user->tokens()->count())->toBe(0)
        ->and($user->mobileRefreshTokens()->whereNull('revoked_at')->count())->toBe(0);
});

it('records a formal deletion request separately from ordinary deactivation', function () {
    $user = User::factory()->create([
        'email' => 'delete-request@example.com',
        'password' => Hash::make('SecurePass123!'),
    ]);
    $login = postJson('/api/mobile/v1/auth/login', [
        'login' => 'delete-request@example.com',
        'password' => 'SecurePass123!',
        'device_name' => 'Deletion Test Android',
    ])->assertOk();
    $device = MobileDeviceInstallation::create([
        'user_id' => $user->id,
        'device_uuid' => fake()->uuid(),
        'expo_push_token' => 'ExponentPushToken[deletion_request_test]',
        'platform' => 'android',
        'enabled' => true,
    ]);

    postJson('/api/mobile/v1/account-deletion-requests', [
        'password' => 'SecurePass123!',
        'confirmation' => 'DELETE MY ACCOUNT',
        'reason' => 'I no longer need the account.',
    ], [
        'Authorization' => 'Bearer '.$login->json('data.tokens.access_token'),
    ])->assertAccepted()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.request.status', 'pending');

    $user->refresh();
    $device->refresh();
    $deletionRequest = MobileAccountDeletionRequest::where('user_id', $user->id)->firstOrFail();

    expect((bool) $user->is_deactivated)->toBeTrue()
        ->and($deletionRequest->email)->toBe('delete-request@example.com')
        ->and($deletionRequest->reason)->toBe('I no longer need the account.')
        ->and($deletionRequest->status)->toBe('pending')
        ->and($user->tokens()->count())->toBe(0)
        ->and($user->mobileRefreshTokens()->whereNull('revoked_at')->count())->toBe(0)
        ->and($device->enabled)->toBeFalse()
        ->and($device->revoked_at)->not->toBeNull();
});
