<?php

use App\Http\Middleware\CheckCookieConsent;
use App\Http\Middleware\EnsureOtpIsVerified;
use App\Http\Middleware\SetLocaleFromRequest;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 👇 لجعل middleware يشتغل على كل الريكوستات
        $middleware->append(CheckCookieConsent::class);
//        $middleware->append(SetLocaleFromRequest::class);
        //        $middleware->append(EnsureOtpIsVerified::class);
        $middleware->appendToGroup('web', SetLocaleFromRequest::class);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            //            'setlocale' => \App\Http\Middleware\SetLocaleFromRequest::class,
            'ensure.otp.verified' => EnsureOtpIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
