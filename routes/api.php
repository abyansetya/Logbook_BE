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
            Route::post('/register', [RegisterController::class, 'submitRegister'])->name('auth.register');
            Route::post('/login', [AuthController::class, 'submitLogin'])->name('auth.login');
        });

        // 🔐 Protected routes (Sanctum)
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', [AuthController::class, 'getMe'])->name('auth.me');
            Route::post('/logout', [AuthController::class, 'submitLogout'])->name('auth.logout');
            Route::post('/logout-all', [AuthController::class, 'submitLogoutAll'])->name('auth.logout-all');
            Route::post('/refresh', [AuthController::class, 'refreshToken'])->name('auth.refresh');
        });

    });

    Route::prefix('profile')->group(function () {
        // 🔐 Protected routes (Sanctum)
        Route::middleware('auth:sanctum')->group(function () {
            Route::patch('/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
            Route::put('/changePassword', [ProfileController::class, 'updatePassword'])->name('profile.changePassword');
        });
    });

    Route::prefix('users')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [\App\Http\Controllers\UserController::class, 'getUsers'])->name('users.index');
        Route::put('/{id}/role', [\App\Http\Controllers\UserController::class, 'updateUserRole'])->name('users.update-role');
        Route::delete('/{id}', [\App\Http\Controllers\UserController::class, 'deleteUser'])->name('users.destroy');
        Route::get('/search', [\App\Http\Controllers\UserController::class, 'searchUser'])->name('users.search')->middleware('role:Admin');
    });

    Route::prefix('dashboard')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [DashboardController::class, 'getDashboardStats'])->name('dashboard.index');
    });

    Route::prefix('logbook')->middleware('auth:sanctum', 'throttle:api')->group(function () {
        // 📄 Dokumen routes
        Route::get('/', [DokumenController::class, 'getDokumen'])->name('logbook.index');
        Route::get('/dokumen/{id}', [DokumenController::class, 'getLogbyId'])->name('logbook.show-dokumen');
        Route::get('/search-dokumen', [DokumenController::class, 'searchDokumen'])->name('logbook.search-dokumen');
        Route::get('/export', [DokumenController::class, 'exportDokumen'])->name('logbook.export');

        // 🛡️ Admin & Operator actions
        Route::middleware('role:Admin,Operator')->group(function () {
            // Dokumen
            Route::post('/dokumen', [DokumenController::class, 'addDokumen'])->name('logbook.add-dokumen');
            Route::put('/edit-dokumen/{id}', [DokumenController::class, 'updateDokumen'])->name('logbook.update-dokumen');
            Route::delete('/delete-dokumen/{id}', [DokumenController::class, 'deleteDokumen'])->name('logbook.delete-dokumen');

            // 📝 Log (activity) routes
            Route::post('/add-log', [LogbookController::class, 'addLog'])->name('logbook.add-log');
            Route::put('/edit-log/{id}', [LogbookController::class, 'updateLog'])->name('logbook.update-log');
            Route::delete('/delete-log/{id}', [LogbookController::class, 'deleteLog'])->name('logbook.delete-log');
        });
    });

    Route::prefix('helper')->middleware('auth:sanctum')->group(function() {
        Route::get('/getStatus', [HelperController::class, 'getStatus'])->name('helper.getStatus');
        Route::get('/getKlasifikasi', [HelperController::class, 'getKlasifikasi'])->name('helper.getKlasifikasi');
        Route::get('/getUnit', [HelperController::class, 'getAllUnits'])->name('helper.getUnit');
        Route::get('/activities', [HelperController::class, 'getRecentActivities'])->name('helper.activities');

        Route::middleware('role:Admin,Operator')->group(function () {
            Route::post('/save-activities', [HelperController::class, 'saveActivity'])->name('helper.save-activity');
        });
    }); 

    Route::prefix('mitra')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [MitraController::class, 'getMitra'])->name('mitra.index');
        Route::get('/search', [MitraController::class, 'searchMitra'])->name('mitra.search');

        // 🛡️ Admin & Operator actions
        Route::middleware('role:Admin,Operator')->group(function () {
            Route::post('/', [MitraController::class, 'addMitra'])->name('mitra.add');
            Route::put('/{id}', [MitraController::class, 'updateMitra'])->name('mitra.update');
            Route::post('/quick', [MitraController::class, 'addMitraQuick'])->name('mitra.add-quick');
        });

        // 🛡️ Admin only actions
        Route::middleware('role:Admin')->group(function () {
            Route::put('/{id}/approve', [MitraController::class, 'approveMitra'])->name('mitra.approve');
            Route::put('/{id}/reject', [MitraController::class, 'rejectMitra'])->name('mitra.reject');
            Route::delete('/{id}', [MitraController::class, 'deleteMitra'])->name('mitra.delete');
        });
    });

    Route::prefix('unit')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [\App\Http\Controllers\UnitController::class, 'getUnit'])->name('unit.index');

        // 🛡️ Admin only actions
        Route::middleware('role:Admin')->group(function () {
            Route::post('/', [\App\Http\Controllers\UnitController::class, 'addUnit'])->name('unit.add');
            Route::put('/{id}', [\App\Http\Controllers\UnitController::class, 'updateUnit'])->name('unit.update');
            Route::delete('/{id}', [\App\Http\Controllers\UnitController::class, 'deleteUnit'])->name('unit.delete');
        });
    });

    Route::prefix('status')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [\App\Http\Controllers\StatusController::class, 'getStatus'])->name('status.index');

        // 🛡️ Admin only actions
        Route::middleware('role:Admin')->group(function () {
            Route::post('/', [\App\Http\Controllers\StatusController::class, 'addStatus'])->name('status.add');
            Route::put('/{id}', [\App\Http\Controllers\StatusController::class, 'updateStatus'])->name('status.update');
            Route::delete('/{id}', [\App\Http\Controllers\StatusController::class, 'deleteStatus'])->name('status.delete');
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
