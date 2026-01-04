<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (v1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {

        // 🔓 Public routes (rate limited)
        Route::middleware('throttle:auth')->group(function () {
            Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
            Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
        });

        // 🔐 Protected routes (Sanctum)
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
            Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
            Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('auth.logout-all');
            Route::post('/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
        });

    });

    // ❤️ Health check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
        ]);
    })->name('health');

});
