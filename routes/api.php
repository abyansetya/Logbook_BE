<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\HelperController;
use App\Http\Controllers\DokumenController;
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
            Route::post('/register', [RegisterController::class, 'register'])->name('auth.register');
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
        Route::get('/search', [\App\Http\Controllers\UserController::class, 'searchUser'])->name('users.search')->middleware('role:Admin');
    });

    Route::prefix('dashboard')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [DashboardController::class, 'getDashboardStats'])->name('dashboard.index');
    });

    Route::prefix('logbook')->middleware('auth:sanctum', 'throttle:api')->group(function () {
        // 📄 Dokumen routes
        Route::get('/', [DokumenController::class, 'index'])->name('logbook.index');
        Route::get('/dokumen/{id}', [DokumenController::class, 'show'])->name('logbook.show-dokumen');
        Route::get('/search-dokumen', [DokumenController::class, 'search'])->name('logbook.search-dokumen');
        Route::get('/export', [DokumenController::class, 'export'])->name('logbook.export');

        // 🛡️ Admin & Operator actions
        Route::middleware('role:Admin,Operator')->group(function () {
            // Dokumen
            Route::post('/dokumen', [DokumenController::class, 'store'])->name('logbook.add-dokumen');
            Route::put('/edit-dokumen/{id}', [DokumenController::class, 'update'])->name('logbook.update-dokumen');
            Route::delete('/delete-dokumen/{id}', [DokumenController::class, 'destroy'])->name('logbook.delete-dokumen');

            // 📝 Log (activity) routes
            Route::post('/add-log', [LogbookController::class, 'store'])->name('logbook.add-log');
            Route::put('/edit-log/{id}', [LogbookController::class, 'update'])->name('logbook.update-log');
            Route::delete('/delete-log/{id}', [LogbookController::class, 'destroy'])->name('logbook.delete-log');
        });
    });

    Route::prefix('helper')->middleware('auth:sanctum')->group(function() {
        Route::get('/getStatus', [HelperController::class, 'getStatus'])->name('helper.getStatus');
        Route::get('/getKlasifikasi', [HelperController::class, 'getKlasifikasi'])->name('helper.getKlasifikasi');
        Route::get('/getUnit', [HelperController::class, 'getUnit'])->name('helper.getUnit');
        Route::get('/activities', [HelperController::class, 'getRecentActivities'])->name('helper.activities');

        Route::middleware('role:Admin,Operator')->group(function () {
            Route::post('/save-activities', [HelperController::class, 'saveActivities'])->name('logbook.save-activities');
        }); 
    }); 

    Route::prefix('mitra')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [MitraController::class, 'index']);
        Route::get('/search', [MitraController::class, 'searchMitra'])->name('mitra.search');

        // 🛡️ Admin & Operator actions
        Route::middleware('role:Admin,Operator')->group(function () {
            Route::post('/', [MitraController::class, 'store']);
            Route::put('/{id}', [MitraController::class, 'update']);
            Route::post('/addMitraWithoutClass', [MitraController::class, 'addMitraWithoutClass'])->name('MitraWithoutClass.add');
        });

        // 🛡️ Admin only actions
        Route::middleware('role:Admin')->group(function () {
            Route::put('/{id}/approve', [MitraController::class, 'approve']);
            Route::put('/{id}/reject', [MitraController::class, 'reject']);
            Route::delete('/{id}', [MitraController::class, 'destroy']);
        });
    });

    Route::prefix('unit')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [\App\Http\Controllers\UnitController::class, 'index'])->name('unit.index');
        
        // 🛡️ Admin only actions
        Route::middleware('role:Admin')->group(function () {
            Route::post('/', [\App\Http\Controllers\UnitController::class, 'store'])->name('unit.store');
            Route::put('/{id}', [\App\Http\Controllers\UnitController::class, 'update'])->name('unit.update');
            Route::delete('/{id}', [\App\Http\Controllers\UnitController::class, 'destroy'])->name('unit.destroy');
        });
    });

    Route::prefix('status')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [\App\Http\Controllers\StatusController::class, 'index'])->name('status.index');
        
        // 🛡️ Admin only actions
        Route::middleware('role:Admin')->group(function () {
            Route::post('/', [\App\Http\Controllers\StatusController::class, 'store'])->name('status.store');
            Route::put('/{id}', [\App\Http\Controllers\StatusController::class, 'update'])->name('status.update');
            Route::delete('/{id}', [\App\Http\Controllers\StatusController::class, 'destroy'])->name('status.destroy');
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
