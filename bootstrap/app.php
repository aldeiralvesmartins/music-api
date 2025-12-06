<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // adicione se ainda não tiver
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'handler.exception' => \App\Http\Middleware\HandlerException::class,
        ]);

        // Ensure tenant middleware runs for all routes in 'web' and 'api' groups
//        $middleware->appendToGroup('web', \App\Http\Middleware\SetCompanyFromDomain::class);
//        $middleware->appendToGroup('api', \App\Http\Middleware\SetCompanyFromDomain::class);
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
