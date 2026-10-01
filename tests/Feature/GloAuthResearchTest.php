<?php

declare(strict_types=1);

use App\Http\Controllers\GloAuthResearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

it('logs in once and tests refresh without exposing credentials or tokens', function (): void {
    app()->detectEnvironment(fn (): string => 'local');

    Http::fake([
        'https://glocafeapp.gloworld.com/api/auth/v1/oauth2-utils/login/' => Http::response([
            'access_token' => 'first-access-secret',
            'refresh_token' => 'first-refresh-secret',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
        ]),
        'https://glocafeapp.gloworld.com/api/auth/v1/oauth2/token/' => Http::response([
            'access_token' => 'second-access-secret',
            'refresh_token' => 'second-refresh-secret',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
        ]),
    ]);

    $request = Request::create('/api-research/glo/auth-refresh', 'POST', [
        'phone_number' => '08012345678',
        'glo_pin' => '432198',
    ]);

    $view = app(GloAuthResearchController::class)->probe($request);
    $result = $view->getData()['result'];
    $rendered = $view->render();

    expect($result)->toMatchArray([
        'login_successful' => true,
        'login_expires_in' => 3600,
        'refresh_successful' => true,
        'refresh_has_access_token' => true,
        'refresh_has_refresh_token' => true,
        'refresh_access_token_rotated' => true,
        'refresh_token_rotated' => true,
        'refresh_expires_in' => 3600,
    ]);

    expect($rendered)
        ->not->toContain('08012345678')
        ->not->toContain('432198')
        ->not->toContain('first-access-secret')
        ->not->toContain('first-refresh-secret')
        ->not->toContain('second-access-secret')
        ->not->toContain('second-refresh-secret');

    Http::assertSentCount(2);
    Http::assertSent(fn ($outbound): bool =>
        $outbound->url() === 'https://glocafeapp.gloworld.com/api/auth/v1/oauth2-utils/login/'
        && $outbound['username'] === '8012345678'
        && $outbound['password'] === '432198'
    );
    Http::assertSent(fn ($outbound): bool =>
        $outbound->url() === 'https://glocafeapp.gloworld.com/api/auth/v1/oauth2/token/'
        && $outbound['client_id'] === 'T4TsZKPIYzQ0ygQa4Tkhd51pNvTXHIDtCkiGXOhX'
        && $outbound['grant_type'] === 'refresh_token'
        && $outbound['scope'] === 'read write openid'
        && $outbound['refresh_token'] === 'first-refresh-secret'
    );
});

it('does not attempt refresh when login fails', function (): void {
    app()->detectEnvironment(fn (): string => 'local');

    Http::fake([
        '*' => Http::response(['detail' => 'Invalid credentials'], 401),
    ]);

    $request = Request::create('/api-research/glo/auth-refresh', 'POST', [
        'phone_number' => '08012345678',
        'glo_pin' => '999999',
    ]);

    $view = app(GloAuthResearchController::class)->probe($request);
    $result = $view->getData()['result'];

    expect($result['login_successful'])->toBeFalse()
        ->and($result)->not->toHaveKey('refresh_http_status');

    Http::assertSentCount(1);
});
