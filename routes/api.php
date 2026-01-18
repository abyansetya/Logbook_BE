<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HelperController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
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

    Route::prefix('profile')->group(function () {
        // 🔐 Protected routes (Sanctum)
        Route::middleware('auth:sanctum')->group(function () {
            Route::patch('/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
            Route::put('/changePassword', [ProfileController::class, 'changePassword'])->name('profile.changePassword');
        });
    });

    Route::prefix('users')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
        Route::put('/{id}/role', [\App\Http\Controllers\UserController::class, 'updateRole'])->name('users.update-role');
        Route::delete('/{id}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/search', [\App\Http\Controllers\UserController::class, 'searchUser'])->name('users.search');
    });

    Route::prefix('dashboard')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [DashboardController::class, 'getDashboardStats'])->name('dashboard.index');
    });

    Route::prefix('logbook')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [LogbookController::class, 'index'])->name('logbook.index');
        Route::get('/dokumen/{id}', [LogbookController::class, 'showByDokumen'])->name('logbook.show-dokumen');
        Route::post('/dokumen', [LogbookController::class, 'addDokumen'])->name('logbook.add-dokumen');
        Route::get('/search-dokumen', [LogbookController::class, 'searchDokumen'])->name('logbook.search-dokumen');
        Route::put('/edit-dokumen/{id}', [LogbookController::class, 'updateDokumen']);
        Route::delete('/delete-dokumen/{id}', [LogbookController::class, 'deleteDokumen']);
        Route::post('/add-log', [LogbookController::class, 'addLog']);
        Route::put('/edit-log/{id}', [LogbookController::class, 'updateLog']);
        Route::delete('/delete-log/{id}', [LogbookController::class, 'deleteLog']);
    });

    // Pindahkan ke luar grup untuk testing
    //Route::delete('/logbook/delete-dokumen/{id}', [LogbookController::class, 'deleteDokumen']);

    Route::prefix('helper')->middleware('auth:sanctum')->group(function() {
        Route::get('/getStatus', [HelperController::class, 'getStatus'])->name('helper.getStatus');
    });

    Route::prefix('mitra')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [MitraController::class, 'index']);
        Route::post('/', [MitraController::class, 'store']);
        Route::put('/{id}', [MitraController::class, 'update']);
        Route::delete('/{id}', [MitraController::class, 'destroy']);
        Route::get('/search', [MitraController::class, 'searchMitra'])->name('mitra.search');
        Route::post('/addMitraWithoutClass', [MitraController::class, 'addMitraWithoutClass'])->name('MitraWithoutClass.add');
    });

    // ❤️ Health check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
        ]);
    })->name('health');

});
