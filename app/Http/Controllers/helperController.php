<?php

namespace App\Http\Controllers;

use App\Models\ActivityLogs;
use App\Models\KlasifikasiMitra;
use App\Models\Status;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Provides static/helper data for form selections and filters.
 */
class HelperController extends Controller
{
    /**
     * Retrieve all available document status categories.
     *
     * @return JsonResponse List of statuses.
     */
    public function getStatus(): JsonResponse
    {
        try {
            // Gunakan cache selama 24 jam (86400 detik)
            $status = Cache::remember('helper_statuses', 86400, function () {
                return Status::all();
            });

            // Mengembalikan response sukses
            return response()->json([
                'success' => true,
                'message' => 'Daftar status berhasil diambil',
                'data' => $status,
            ], 200);

        } catch (\Exception $e) {
            // Mengembalikan response error jika terjadi masalah pada database
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data status',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem',
            ], 500);
        }
    }

    /**
     * Retrieve all available partner classifications.
     *
     * @return JsonResponse List of partner classifications.
     */
    public function getKlasifikasi(): JsonResponse
    {
        try {
            // Gunakan cache selama 24 jam (86400 detik)
            $klasifikasi = Cache::remember('helper_klasifikasi', 86400, function () {
                return KlasifikasiMitra::all();
            });

            return response()->json([
                'success' => true,
                'message' => 'Daftar klasifikasi mitra berhasil diambil',
                'data' => $klasifikasi,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data klasifikasi mitra',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem',
            ], 500);
        }
    }

    /**
     * Log a user activity to the database.
     *
     * @param  Request  $request  Activity details (user_id, action, description, type).
     * @return JsonResponse Created activity log data.
     */
    public function saveActivity(Request $request): JsonResponse
    {

        DB::beginTransaction();
        try {
            $activity = ActivityLogs::create([
                'user_id' => $request->user_id,
                'action' => $request->action,
                'description' => $request->description,
                'type' => $request->type,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Aktivitas berhasil disimpan',
                'data' => $activity,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan aktivitas',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem',
            ], 500);
        }
    }

    /**
     * Retrieve the most recent activity logs.
     *
     * @return JsonResponse List of the 5 most recent activities with user data.
     */
    public function getRecentActivities(): JsonResponse
    {
        try {
            // Mengambil 5 aktivitas terbaru dengan relasi user
            $activities = ActivityLogs::with('user:id,nama')
                ->latest()
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'message' => '5 aktivitas terbaru berhasil diambil',
                'data' => $activities,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data aktivitas',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem',
            ], 500);
        }
    }

    /**
     * Retrieve all available organizational units.
     *
     * @return JsonResponse List of units.
     */
    public function getUnit(): JsonResponse
    {
        try {
            $unit = Cache::remember('helper_units', 86400, function () {
                return Unit::all();
            });

            return response()->json([
                'success' => true,
                'message' => 'Daftar unit berhasil diambil',
                'data' => $unit,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data unit',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem',
            ], 500);
        }
    }
}
