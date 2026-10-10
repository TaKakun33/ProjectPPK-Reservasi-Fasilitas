<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\PastikanAkunTerverifikasi;
use App\Http\Middleware\SecurityHeaders;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Di Vercel, HTTPS di-terminasi oleh proxy; percayai header X-Forwarded-* agar URL aset (CSS/JS) bukan http://
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'role' => CheckRole::class,
        ]);

        // Cek ulang status akun di setiap request (E2)
        $middleware->web(append: [
            PastikanAkunTerverifikasi::class,
            SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
