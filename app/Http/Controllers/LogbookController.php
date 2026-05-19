<?php

namespace App\Http\Controllers;

use App\Http\Requests\addLogRequest;
use App\Http\Requests\updateLogRequest;
use App\Models\Dokumen;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Manages activity log entries (logbook) within a document.
 */
class LogbookController extends Controller
{
    /**
     * Add a new activity log entry to a document.
     *
     * @param  addLogRequest  $request  Validated log data.
     * @return JsonResponse Created log entry.
     */
    public function addLog(addLogRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $dokumen = Dokumen::with('status')->lockForUpdate()->findOrFail($validated['dokumen_id']);
            if ($dokumen->status && $dokumen->status->nama === 'Terbit') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menambah log pada dokumen yang sudah terbit',
                ], 422);
            }

            $log = Log::create([
                'user_id' => $request->user()->id,
                'mitra_id' => $dokumen->mitra_id,
                'dokumen_id' => $dokumen->id,
                'unit_id' => $validated['unit_id'],
                'keterangan' => $validated['keterangan'],
                'tanggal_log' => $validated['tanggal_log'],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Log aktivitas berhasil ditambahkan',
                'data' => $log,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan log',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem',
            ], 500);
        }
    }

    /**
     * Update an existing activity log entry.
     *
     * @param  updateLogRequest  $request  Validated updated log data.
     * @param  int|string  $id  The log ID.
     * @return JsonResponse Updated log entry.
     */
    public function updateLog(updateLogRequest $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $log = Log::findOrFail($id);

            $log->update([
                'keterangan' => $request->keterangan,
                'tanggal_log' => $request->tanggal_log ?? now(),
                'user_id' => Auth::id(),
                'unit_id' => $request->unit_id,
                'updated_at' => now('Asia/Jakarta'),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Log aktivitas berhasil diperbarui',
                'data' => $log,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui log: '.(config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem'),
            ], 500);
        }
    }

    /**
     * Delete an activity log entry.
     *
     * @param  int|string  $id  The log ID.
     * @return JsonResponse Success or failure message.
     */
    public function deleteLog($id): JsonResponse
    {
        try {
            $log = Log::findOrFail($id);
            $log->delete();

            return response()->json([
                'success' => true,
                'message' => 'Log berhasil dihapus',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus log',
            ], 500);
        }
    }
}
