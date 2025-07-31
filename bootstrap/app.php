<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// return Application::configure(basePath: dirname(__DIR__))
//     ->withRouting(
//         web: __DIR__.'/../routes/web.php',
//         api: __DIR__.'/../routes/api.php',
//         commands: __DIR__.'/../routes/console.php',
//         health: '/up',
//     )
//     ->withMiddleware(function (Middleware $middleware): void {
//         $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);
//     })
//     ->withMiddleware(function (Middleware $middleware) {
//     $middleware->validateCsrfTokens(except: [
//         'admin/*',
//         // 'admin/login' ဆိုပြီးလည်းထည့်နိုင်ပါတယ်
//     ]);
// })
//     ->withExceptions(function (Exceptions $exceptions): void {
//         //
//     })->create();

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);
        
        $middleware->trustProxies(at: '*');
        $middleware->validateCsrfTokens(except: [
            'admin/login',
            'admin/authenticate'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();