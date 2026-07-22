<?php

use App\Http\Middleware\AuthenticateExternalIntegration;
use App\Http\Middleware\EnsureMobileUserIsActive;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\MarketerMiddleware;
use App\Http\Middleware\RoleAdminAccess;
use App\Http\Middleware\RoleAssess;
use App\Http\Middleware\RoleUserAccess;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\SetTransactionPin;
use App\Http\Middleware\TemplateSetting;
use App\Http\Middleware\ValidateApiToken;
use App\Http\Middleware\ValidateSanctumUser;
use App\Http\Middleware\ValidateWhatsappApiToken;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api/mobile/v1')
                ->name('mobile.v1.')
                ->group(base_path('routes/mobile.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // $middleware->append(RoleAssess::class);
        $middleware->alias([
            'template_setting' => TemplateSetting::class,
            'admin' => RoleAdminAccess::class,
            'user' => RoleUserAccess::class,
            'marketer' => MarketerMiddleware::class,
            'validate_user' => ValidateSanctumUser::class,
            'api_token' => ValidateApiToken::class,
            // 'whatsapp.token' => ValidateWhatsappApiToken::class,
            'set_transaction_pin' => SetTransactionPin::class,
            'set_locale' => SetLocale::class,
            'mobile.user.active' => EnsureMobileUserIsActive::class,
        ]);

        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        // 'api_access' => AuthenticateExternalIntegration::class
        // $middleware->alias(['user' => RoleUserAccess::class]);
        $middleware->statefulApi();
        $middleware->validateCsrfTokens(
            // Specify the routes to exclude from CSRF protection
            except: ['register']
        );
    })
    ->booted(function () {
        RateLimiter::for('mobile-login', fn (Request $request) => [
            Limit::perMinute(5)->by($request->ip().'|'.mb_strtolower((string) $request->input('login'))),
        ]);

        RateLimiter::for('mobile-register', fn (Request $request) => [
            Limit::perHour(10)->by($request->ip()),
        ]);

        RateLimiter::for('mobile-refresh', fn (Request $request) => [
            Limit::perMinute(20)->by($request->ip()),
        ]);

        RateLimiter::for('mobile-password', fn (Request $request) => [
            Limit::perHour(5)->by($request->ip().'|'.mb_strtolower((string) $request->input('email'))),
        ]);

        RateLimiter::for('mobile-otp-send', fn (Request $request) => [
            Limit::perMinute(1)->by((string) ($request->user()?->id ?? $request->ip())),
            Limit::perHour(5)->by((string) ($request->user()?->id ?? $request->ip())),
        ]);

        RateLimiter::for('mobile-otp-verify', fn (Request $request) => [
            Limit::perMinute(5)->by((string) ($request->user()?->id ?? $request->ip())),
        ]);

        RateLimiter::for('mobile-pin', fn (Request $request) => [
            Limit::perMinute(5)->by((string) ($request->user()?->id ?? $request->ip())),
        ]);

        RateLimiter::for('mobile-purchase', fn (Request $request) => [
            Limit::perMinute(10)->by($request->user()?->id ?: $request->ip()),
            Limit::perDay(200)->by($request->user()?->id ?: $request->ip()),
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $isMobileApi = fn (Request $request) => $request->is('api/mobile/v1/*');

        $exceptions->shouldRenderJsonWhen(fn (Request $request) => $isMobileApi($request) || $request->expectsJson());

        $exceptions->render(function (ValidationException $exception, Request $request) use ($isMobileApi) {
            if (! $isMobileApi($request)) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Please check the provided information.',
                'data' => null,
                'meta' => null,
                'errors' => $exception->errors(),
            ], 422);
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) use ($isMobileApi) {
            if (! $isMobileApi($request)) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Authentication is required.',
                'data' => null,
                'meta' => null,
                'errors' => null,
            ], 401);
        });

        $exceptions->render(function (TooManyRequestsHttpException $exception, Request $request) use ($isMobileApi) {
            if (! $isMobileApi($request)) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Too many attempts. Please wait and try again.',
                'data' => null,
                'meta' => null,
                'errors' => null,
            ], 429, $exception->getHeaders());
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) use ($isMobileApi) {
            if (! $isMobileApi($request)) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'The requested resource was not found.',
                'data' => null,
                'meta' => null,
                'errors' => null,
            ], 404);
        });
    })->create();
