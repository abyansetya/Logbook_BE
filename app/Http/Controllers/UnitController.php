<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Manages organizational units associated with activity logs.
 */
class UnitController extends Controller
{
    /**
     * List all organizational units with search and pagination.
     *
     * @param Request $request Filter parameters (q, per_page).
     * @return JsonResponse Paginated list of units.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Unit::query();

            // Search logic
            if ($request->has('q')) {
                $search = $request->query('q');
                $query->where('nama', 'LIKE', "%{$search}%");
            }

            $perPage = $request->input('per_page', 10);
            $units = $query->orderBy('nama', 'asc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Daftar unit berhasil diambil',
                'data'    => [
                    'data' => $units->items(),
                    'meta' => [
                        'current_page' => $units->currentPage(),
                        'from' => $units->firstItem(),
                        'last_page' => $units->lastPage(),
                        'per_page' => $units->perPage(),
                        'to' => $units->lastItem(),
                        'total' => $units->total(),
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
     * Create a new organizational unit.
     *
     * @param Request $request Unit details (nama).
     * @return JsonResponse Created unit data.
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255|unique:unit,nama',
            ]);

            $unit = Unit::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Unit berhasil ditambahkan',
                'data'    => $unit
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
                'message' => 'Gagal menambahkan unit',
                'error'   => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    /**
     * Update an existing organizational unit.
     *
     * @param Request $request Updated unit details.
     * @param int|string $id Unit ID.
     * @return JsonResponse Updated unit data.
     */
    public function update(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $unit = Unit::findOrFail($id);
            
            $validated = $request->validate([
                'nama' => 'required|string|max:255|unique:unit,nama,' . $id,
            ]);

            $unit->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Unit berhasil diperbarui',
                'data'    => $unit
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
                'message' => 'Gagal memperbarui unit',
                'error'   => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    /**
     * Remove an organizational unit from the system.
     *
     * @param int|string $id Unit ID.
     * @return JsonResponse Success or failure message.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $unit = Unit::findOrFail($id);
            $unit->delete();

            return response()->json([
                'success' => true,
                'message' => 'Unit berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus unit'
            ], 500);
        }
    }
}
