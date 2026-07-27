<?php

use App\Models\MobileRefreshToken;
use App\Models\Role;
use App\Models\User;
use App\Models\UserPlan;
use App\Notifications\MobileVerifyEmailNotification;
use Illuminate\Auth\Notifications\ResetPassword;
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
    Notification::assertSentTo($user, MobileVerifyEmailNotification::class);
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
        ->assertJsonPath('message', 'If that email belongs to an unverified account, a new verification link has been sent.');
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

it('sends password reset instructions without exposing whether an account exists', function () {
    Notification::fake();
    $user = User::factory()->create(['email' => 'reset@example.com']);

    postJson('/api/mobile/v1/auth/forgot-password', ['email' => 'reset@example.com'])
        ->assertOk()
        ->assertJsonPath('success', true);

    Notification::assertSentTo($user, ResetPassword::class);

    postJson('/api/mobile/v1/auth/forgot-password', ['email' => 'missing@example.com'])
        ->assertOk()
        ->assertJsonPath('message', 'If an account matches that email, password reset instructions have been sent.');
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
