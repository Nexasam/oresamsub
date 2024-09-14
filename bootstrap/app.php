<?php

use App\Http\Middleware\RoleAssess;
use App\Http\Middleware\AdminSettings;
use Illuminate\Foundation\Application;
use App\Http\Middleware\RoleUserAccess;
use App\Http\Middleware\RoleAdminAccess;
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
        $middleware->alias(['admin' => RoleAdminAccess::class, 'user' => RoleUserAccess::class, ]);
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
