<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 👇 لجعل middleware يشتغل على كل الريكوستات
        $middleware->append(\App\Http\Middleware\SetLocaleFromRequest::class);


        $middleware->alias([
            'role'                => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'          => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission'  => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
//            'setlocale' => \App\Http\Middleware\SetLocaleFromRequest::class,
        ]);    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
