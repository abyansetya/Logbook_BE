<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MitraController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Mitra::with('klasifikasiMitra'); // Eager load klasifikasi

            // Search logic
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'LIKE', "%{$search}%")
                      ->orWhere('alamat', 'LIKE', "%{$search}%")
                      ->orWhere('contact_person', 'LIKE', "%{$search}%");
                });
            }

            // Pagination (default 10)
            $perPage = $request->input('per_page', 10);
            $mitras = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'List Data Mitra',
                'data' => $mitras
            ]);
        } catch (\Exception $e) {
            Log::error("Get Mitra Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data mitra',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:mitra,nama',
            'klasifikasi_mitra_id' => 'required|exists:klasifikasi_mitra,id',
            'alamat' => 'nullable|string',
            'contact_person' => 'nullable|string',
        ]);

        try {
            $mitra = Mitra::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Mitra berhasil ditambahkan',
                'data' => $mitra
            ], 201);
        } catch (\Exception $e) {
            Log::error("Create Mitra Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan mitra',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'string|max:255',
            'klasifikasi_mitra_id' => 'exists:klasifikasi_mitra,id',
            'alamat' => 'nullable|string',
            'contact_person' => 'nullable|string',
        ]);

        try {
            $mitra = Mitra::find($id);

            if (!$mitra) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mitra tidak ditemukan'
                ], 404);
            }

            $mitra->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Mitra berhasil diperbarui',
                'data' => $mitra
            ]);
        } catch (\Exception $e) {
            Log::error("Update Mitra Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui mitra',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $mitra = Mitra::find($id);

            if (!$mitra) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mitra tidak ditemukan'
                ], 404);
            }

            $mitra->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mitra berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error("Delete Mitra Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus mitra',
                'error' => $e->getMessage()
            ], 500);
        }
    }

     public function searchMitra(Request $request) // Gunakan Request, bukan $q
    {
        try {
            // Ambil nilai dari ?q=bank
            $query = $request->query('q');

            // Jika q kosong, kembalikan array kosong agar frontend tidak error
            if (empty($query)) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            // Lakukan pencarian di database
            // Pastikan kolom di tabel mitra memang bernama 'nama'
            $mitras = Mitra::where('nama', 'LIKE', "%{$query}%")
                ->limit(10) // Opsional: batasi hasil agar cepat
                ->get();

            return response()->json([
                'success' => true,
                'data' => $mitras
            ]);
            
        } catch (\Exception $e) {
            // Log error agar bisa dicek di storage/logs/laravel.log
            Log::error("Search Mitra Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage() // Munculkan ini untuk debugging
            ], 500);
        }
    }

    public function addMitraWithoutClass(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:mitra,nama',
        ]);

        try {
            $mitra = Mitra::create([
                'nama' => $request->input('nama'),
                'klasifikasi_mitra_id' => 16,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mitra berhasil ditambahkan',
                'data' => $mitra
            ], 201);
        } catch (\Exception $e) {
            Log::error("Add Mitra Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan mitra',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}