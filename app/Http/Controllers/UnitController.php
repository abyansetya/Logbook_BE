<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class UnitController extends Controller
{
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
                'data'    => $units
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
