<?php

use App\Http\Middleware\AdminSettings;
use App\Http\Middleware\ApiTokenMiddleware;
use App\Http\Middleware\AuthenticateExternalIntegration;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\MarketerMiddleware;
use App\Http\Middleware\RoleAdminAccess;
use App\Http\Middleware\RoleAssess;
use App\Http\Middleware\RoleUserAccess;
use App\Http\Middleware\SetTransactionPin;
use App\Http\Middleware\TemplateSetting;
use App\Http\Middleware\ValidateApiToken;
use App\Http\Middleware\ValidateSanctumUser;
use App\Http\Middleware\ValidateWhatsappApiToken;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // $middleware->append(RoleAssess::class);
        $middleware->alias([
            'template_setting'=>TemplateSetting::class,
            'admin' => RoleAdminAccess::class,
            'user' => RoleUserAccess::class, 
            'marketer' => MarketerMiddleware::class, 
            'validate_user' => ValidateSanctumUser::class,
            'api_token' => ValidateApiToken::class,
            // 'whatsapp.token' => ValidateWhatsappApiToken::class,
            'set_transaction_pin' => SetTransactionPin::class,
            'set_locale' => \App\Http\Middleware\SetLocale::class,
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
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
