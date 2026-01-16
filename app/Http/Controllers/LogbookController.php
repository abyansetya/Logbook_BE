<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\addDokumenRequest;
use App\Http\Requests\addLogRequest;
use App\Http\Requests\updateLogRequest;
use App\Http\Resources\DokumenResource;
use App\Http\Resources\MitraResource;
use App\Models\Dokumen;
use App\Models\Log;
use App\Models\Mitra;
use GrahamCampbell\ResultType\Success;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            $dokumen = Dokumen::with([
                // Menambahkan fungsi penutup untuk mengurutkan log
                'logs' => function ($query) {
                    $query->orderBy('tanggal_log', 'asc'); // 'desc' untuk terbaru di atas
                },
                'logs.user', 
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

    public function addLog(addLogRequest $request):JsonResponse
    {
        DB::beginTransaction();
        try{
            $validated = $request->validated();

            $log= Log::create([
                'user_id' => $validated['user_id'],
                'mitra_id' => $validated['mitra_id'],
                'dokumen_id' => $validated['dokumen_id'],
                'keterangan' => $validated['keterangan'],
                'contact_person' => $validated['contact_person'],
                'tanggal_log' => $validated['tanggal_log']
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data retrieved Successfully',
                'data' => $log
            ], 201);

        } catch(\Exception $e)
        {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan Log',
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

    public function updateLog(updateLogRequest $request, $id): JsonResponse
    {
        // 1. Mulai Transaksi agar data konsisten (semua berhasil atau semua gagal)
        DB::beginTransaction();

        try {
            // 2. Pastikan pencarian berdasarkan ID ini terindeks (Primary Key)
            $log = Log::findOrFail($id);

            // 3. Update data berdasarkan request yang sudah divalidasi
            $log->update([
                'keterangan'  => $request->keterangan,
                'tanggal_log' => $request->tanggal_log ?? now(),
                'user_id'     => Auth::id(), // Menggunakan helper id() yang benar
                'updated_at'     => now('Asia/Jakarta'),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Log aktivitas berhasil diperbarui',
                'data'    => $log
            ], 200);

        } catch (\Exception $e) {
            // Jika ada error, batalkan semua perubahan data
            DB::rollBack();

            return response()->json([
                'success' => false, 
                'message' => 'Gagal memperbarui log: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteLog($id): JsonResponse
    {
        try{
            $log = Log::findOrFail($id);
            $log->delete();

            return response()->json([
                'success' => true,
                'message' => 'Log berhasil dihapus'
            ], 200);
        }catch(\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Log'
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

    // hapus dokumen by id
    public function deleteDokumen($id): JsonResponse
    {
        try {
            $dokumen = Dokumen::findOrFail($id);
            $dokumen->delete();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus dokumen'
            ], 500);
        }
    }
};