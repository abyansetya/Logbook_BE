<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\addDokumenRequest;
use App\Http\Resources\DokumenResource;
use App\Http\Resources\MitraResource;
use App\Models\Dokumen;
use App\Models\Mitra;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function addDokumen(addDokumenRequest $request): JsonResponse
    {
        // Gunakan DB::beginTransaction() di sini
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $dokumen = Dokumen::create([
                'mitra_id'            => $validated['mitra_id'],
                'jenis_dokumen_id'    => $validated['jenis_dokumen_id'],
                'status_id'           => $validated['status_id'],
                'judul_dokumen'       => $validated['judul_dokumen'],
                'nomor_dokumen_mitra' => $validated['nomor_dokumen_mitra'] ?? null,
                'nomor_dokumen_undip' => $validated['nomor_dokumen_undip'] ?? null,
                'tanggal_masuk'       => $validated['tanggal_masuk'] ?? now()->format('Y-m-d'),
                'tanggal_terbit'      => $validated['tanggal_terbit'] ?? null,
            ]);

            // Load relasi agar DokumenResource bisa menampilkan data lengkap ke React
            $dokumen->load(['mitra', 'jenisDokumen', 'status']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil ditambahkan',
                'data'    => new DokumenResource($dokumen)
            ], 201);

        } catch (\Exception $e) {
            // PENTING: Batalkan transaksi jika ada error
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan dokumen',
                // Gunakan $e->getMessage() hanya di mode dev untuk keamanan
                'error'   => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    public function searchDokumen(Request $request): JsonResponse
    {
        try {
            $query = $request->query('q');

            if (empty($query)) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $dokumens = Dokumen::where('judul_dokumen', 'LIKE', "%{$query}%")
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => DokumenResource::collection($dokumens)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    public function updateDokumen(addDokumenRequest $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $dokumen = Dokumen::findOrFail($id);
            
            // Validasi menggunakan Request yang sama dengan Add
            $validated = $request->validated();

            $dokumen->update([
                'mitra_id'            => $validated['mitra_id'],
                'jenis_dokumen_id'    => $validated['jenis_dokumen_id'],
                'status_id'           => $validated['status_id'],
                'judul_dokumen'       => $validated['judul_dokumen'],
                'nomor_dokumen_mitra' => $validated['nomor_dokumen_mitra'] ?? null,
                'nomor_dokumen_undip' => $validated['nomor_dokumen_undip'] ?? null,
                'tanggal_masuk'       => $validated['tanggal_masuk'],
                'tanggal_terbit'      => $validated['tanggal_terbit'] ?? null,
            ]);

            // Load relasi agar response lengkap
            $dokumen->load(['mitra', 'jenisDokumen', 'status']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diperbarui',
                'data'    => new DokumenResource($dokumen)
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui dokumen',
                'error'   => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem'
            ], 500);
        }
    }
};