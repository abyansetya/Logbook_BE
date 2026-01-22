<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActivityLogs;
use App\Models\Status;
use App\Models\KlasifikasiMitra;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HelperController extends Controller
{
    /**
     * Mengambil semua daftar status dari database
     */
    public function getStatus(): JsonResponse
    {
        try {
            // Mengambil semua data status
            $status = Status::all();

            // Mengembalikan response sukses
            return response()->json([
                'success' => true,
                'message' => 'Daftar status berhasil diambil',
                'data'    => $status
            ], 200);

        } catch (\Exception $e) {
            // Mengembalikan response error jika terjadi masalah pada database
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data status',
                'error'   => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    /**
     * Mengambil semua daftar klasifikasi mitra dari database
     */
    public function getKlasifikasi(): JsonResponse
    {
        try {
            $klasifikasi = KlasifikasiMitra::all();

            return response()->json([
                'success' => true,
                'message' => 'Daftar klasifikasi mitra berhasil diambil',
                'data'    => $klasifikasi
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data klasifikasi mitra',
                'error'   => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    public function saveActivities(Request $request): JsonResponse
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
                'data'    => $activity
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan aktivitas',
                'error'   => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

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
                'data'    => $activities
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data aktivitas',
                'error'   => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem'
            ], 500);
        }
    }
}
