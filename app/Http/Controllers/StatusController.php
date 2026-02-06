<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StatusController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Status::query();

            // Search logic
            if ($request->has('q')) {
                $search = $request->query('q');
                $query->where('nama', 'LIKE', "%{$search}%");
            }

            $perPage = $request->input('per_page', 10);
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

    public function store(Request $request): JsonResponse
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

    public function update(Request $request, $id): JsonResponse
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

    public function destroy($id): JsonResponse
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
