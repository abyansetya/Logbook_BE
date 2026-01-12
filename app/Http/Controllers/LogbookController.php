<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\DokumenResource;
use App\Http\Resources\MitraResource;
use App\Models\Dokumen;
use App\Models\Mitra;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class LogbookController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            // Ambil data langsung dari Dokumen agar sesuai baris tabel di UI
            $dokumen = Dokumen::with([
                'mitra',         // Untuk kolom "Nama Mitra"
                'jenisDokumen',  // Untuk kolom "Jenis Dokumen"
                'status'         // Untuk kolom "Status"
            ])
            ->latest() // Menampilkan dokumen terbaru di atas
            ->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Daftar dokumen berhasil diambil',
                'data'    => DokumenResource::collection($dokumen)->response()->getData(true)
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function showByDokumen($id): JsonResponse
    {
        try {
            // Ambil dokumen spesifik beserta log aktivitasnya
            $dokumen = Dokumen::with([
                'logs.user', // Untuk menampilkan "Admin Undip"
                'jenisDokumen',
                'status'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Log aktivitas berhasil dimuat',
                'data'    => new DokumenResource($dokumen)
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }
    }
}