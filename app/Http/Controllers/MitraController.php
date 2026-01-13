<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MitraController extends Controller
{
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