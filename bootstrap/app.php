<?php

/**
 * =============================================================================
 * RATE LIMITING CONFIGURATION
 * =============================================================================
 * 
 * Tambahkan kode berikut ke bootstrap/app.php atau App\Providers\AppServiceProvider
 * 
 * Untuk Laravel 11+, tambahkan di bootstrap/app.php:
 */

// Di file bootstrap/app.php, dalam method withMiddleware():

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

// Tambahkan dalam boot method atau di bootstrap/app.php

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // API middleware configuration
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->booted(function () {
        // Rate limiter untuk auth endpoints (login/register)
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by(
                $request->input('email') . '|' . $request->ip()
            )->response(function () {
                return response()->json([
                    'message' => 'Terlalu banyak percobaan. Silakan coba lagi dalam 1 menit.',
                ], 429);
            });
        });

        // Rate limiter untuk API umum
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(
                $request->user()?->id ?: $request->ip()
            );
        });
    })
    ->create();