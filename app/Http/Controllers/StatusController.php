<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Manages document status categories (e.g., Draft, Terbit, Batal).
 */
class StatusController extends Controller
{
    /**
     * List all available document statuses with search and pagination.
     *
     * @param Request $request Filter parameters (q, per_page).
     * @return JsonResponse Paginated list of statuses.
     */
    public function getStatus(Request $request): JsonResponse
    {
        try {
            $query = Status::query();

            // Search logic
            if ($request->has('q')) {
                $search = $request->query('q');
                $query->where('nama', 'LIKE', "%{$search}%");
            }

            $perPage = min((int)$request->input('per_page', 10), 100);
            $statuses = $query->orderBy('created_at', 'asc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Daftar status berhasil diambil',
                'data'    => [
                    'data' => $statuses->items(),
                    'meta' => [
                        'current_page' => $statuses->currentPage(),
                        'from' => $statuses->firstItem(),
                        'last_page' => $statuses->lastPage(),
                        'per_page' => $statuses->perPage(),
                        'to' => $statuses->lastItem(),
                        'total' => $statuses->total(),
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new document status category.
     *
     * @param Request $request Status details (nama).
     * @return JsonResponse Created status data.
     */
    public function addStatus(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255|unique:status,nama',
            ]);

            $status = Status::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil ditambahkan',
                'data'    => $status
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan status',
                'error'   => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    /**
     * Update an existing document status category.
     *
     * @param Request $request Updated status details.
     * @param int|string $id Status ID.
     * @return JsonResponse Updated status data.
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $status = Status::findOrFail($id);
            
            $validated = $request->validate([
                'nama' => 'required|string|max:255|unique:status,nama,' . $id,
            ]);

            $status->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui',
                'data'    => $status
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status',
                'error'   => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    /**
     * Remove a status category from the system.
     *
     * @param int|string $id Status ID.
     * @return JsonResponse Success or failure message.
     */
    public function deleteStatus($id): JsonResponse
    {
        try {
            $status = Status::findOrFail($id);
            $status->delete();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus status'
            ], 500);
        }
    }
}
