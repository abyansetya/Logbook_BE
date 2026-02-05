<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\addDokumenRequest;
use App\Http\Requests\addLogRequest;
use App\Http\Requests\editDokumenRequest; // Added this line
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
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Dokumen::with([
                'mitra',         
                'jenisDokumen',  
                'status'         
            ]);

            // Search logic
            if ($request->has('q')) {
                $search = $request->query('q');
                $query->where(function($q) use ($search) {
                    $q->where('judul_dokumen', 'LIKE', "%{$search}%")
                      ->orWhere('nomor_dokumen_undip', 'LIKE', "%{$search}%")
                      ->orWhere('nomor_dokumen_mitra', 'LIKE', "%{$search}%");
                });
            }

            // Status filter
            if ($request->has('status') && $request->query('status') !== 'all') {
                $statusId = $request->query('status');
                $query->where('status_id', $statusId);
            }

            // Jenis Dokumen filter
            if ($request->has('jenis_dokumen') && $request->query('jenis_dokumen') !== 'all') {
                $jenisId = $request->query('jenis_dokumen');
                $query->where('jenis_dokumen_id', $jenisId);
            }

            // Tahun filter
            if ($request->has('tahun') && $request->query('tahun') !== 'all') {
                $tahun = $request->query('tahun');
                $query->whereYear('tanggal_dokumen', $tahun);
            }

            $perPage = $request->input('per_page', 10);
            $order = $request->query('order', 'desc');
            if (!in_array($order, ['asc', 'desc'])) $order = 'desc';

            $dokumen = $query->orderBy('tanggal_masuk', $order)->paginate($perPage);

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
                'contact_person'      => $validated['contact_person'] ?? null,
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
                'unit_id' => $validated['unit_id'],
                'keterangan' => $validated['keterangan'],
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
                'unit_id'     => $request->unit_id,
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


    public function updateDokumen(editDokumenRequest $request, $id): JsonResponse
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
                'contact_person'      => $validated['contact_person'] ?? null,
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
    public function export(Request $request)
    {
        try {
            $query = Dokumen::with(['mitra.klasifikasiMitra', 'jenisDokumen', 'status', 'logs.user']);

            // Search logic (same as index)
            if ($request->has('q')) {
                $search = $request->query('q');
                $query->where(function($q) use ($search) {
                    $q->where('judul_dokumen', 'LIKE', "%{$search}%")
                      ->orWhere('nomor_dokumen_undip', 'LIKE', "%{$search}%")
                      ->orWhere('nomor_dokumen_mitra', 'LIKE', "%{$search}%");
                });
            }

            // Status filter
            if ($request->has('status') && $request->query('status') !== 'all') {
                $statusId = $request->query('status');
                $query->where('status_id', $statusId);
            }

            // Jenis Dokumen filter
            if ($request->has('jenis_dokumen') && $request->query('jenis_dokumen') !== 'all') {
                $jenisId = $request->query('jenis_dokumen');
                $query->where('jenis_dokumen_id', $jenisId);
            }

            // Tahun filter
            if ($request->has('tahun') && $request->query('tahun') !== 'all') {
                $tahun = $request->query('tahun');
                $query->whereYear('tanggal_dokumen', $tahun);
            }

            $perPage = $request->input('per_page', 10);
            $order = $request->query('order', 'desc');
            if (!in_array($order, ['asc', 'desc'])) $order = 'desc';

            $dokumens = $query->orderBy('tanggal_masuk', $order)->get();

            // Group by Mitra
            $groupedData = $dokumens->groupBy(function ($item) {
                return $item->mitra ? $item->mitra->nama : 'Tanpa Mitra';
            });

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set Header
            $headers = ['No', 'Nama Mitra', 'Judul Dokumen', 'Tanggal', 'Keterangan', 'Contact Person', 'Nomor Dokumen', 'Status', 'Kriteria Mitra'];
            $columnLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];

            foreach ($headers as $index => $header) {
                $sheet->setCellValue($columnLetters[$index] . '1', $header);
                $sheet->getStyle($columnLetters[$index] . '1')->getFont()->setBold(true);
                $sheet->getStyle($columnLetters[$index] . '1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
                $sheet->getColumnDimension($columnLetters[$index])->setAutoSize(true);
            }

            $row = 2;
            $no = 1;

            foreach ($groupedData as $mitraName => $docs) {
                // Sort documents within the group by tanggal_masuk
                $docs = ($order === 'desc') 
                    ? $docs->sortByDesc('tanggal_masuk') 
                    : $docs->sortBy('tanggal_masuk');

                // Calculate total rows for this Mitra
                $mitraStartRow = $row;
                
                foreach ($docs as $dokumen) {
                    $dokumenStartRow = $row;
                    $logs = ($order === 'desc') 
                        ? $dokumen->logs->sortByDesc('tanggal_log') 
                        : $dokumen->logs->sortBy('tanggal_log');

                    // If logs exist, iterate them. If not, one row for document.
                    if ($logs->count() > 0) {
                        foreach ($logs as $log) {
                            $sheet->setCellValue('D' . $row, $log->tanggal_log);
                            $sheet->setCellValue('E' . $row, $log->keterangan);
                            $sheet->setCellValue('F' . $row, $log->contact_person);
                            $row++;
                        }
                    } else {
                        // Empty row for logs if none
                        $row++;
                    }
                    
                    $dokumenEndRow = $row - 1;

                    // Merge Document Columns
                    if ($dokumenEndRow > $dokumenStartRow) {
                        $sheet->mergeCells("C{$dokumenStartRow}:C{$dokumenEndRow}"); // Judul
                        $sheet->mergeCells("G{$dokumenStartRow}:G{$dokumenEndRow}"); // Nomor Dokumen
                        $sheet->mergeCells("H{$dokumenStartRow}:H{$dokumenEndRow}"); // Status
                    }

                    $sheet->setCellValue('C' . $dokumenStartRow, $dokumen->judul_dokumen);

                    $nomorDokumen = [];
                    if ($dokumen->nomor_dokumen_undip) {
                        $nomorDokumen[] = $dokumen->nomor_dokumen_undip;
                    }
                    if ($dokumen->nomor_dokumen_mitra) {
                        $nomorDokumen[] = $dokumen->nomor_dokumen_mitra;
                    }
                    $sheet->setCellValue('G' . $dokumenStartRow, implode("\n", $nomorDokumen));
                    $sheet->getStyle('G' . $dokumenStartRow)->getAlignment()->setWrapText(true);

                    $sheet->setCellValue('H' . $dokumenStartRow, $dokumen->status ? $dokumen->status->nama : '-');
                }

                $mitraEndRow = $row - 1;

                // Merge Mitra Columns
                if ($mitraEndRow > $mitraStartRow) {
                    $sheet->mergeCells("A{$mitraStartRow}:A{$mitraEndRow}"); // No
                    $sheet->mergeCells("B{$mitraStartRow}:B{$mitraEndRow}"); // Nama Mitra
                    $sheet->mergeCells("I{$mitraStartRow}:I{$mitraEndRow}"); // Kriteria Mitra
                }

                $sheet->setCellValue('A' . $mitraStartRow, $no++);
                $sheet->setCellValue('B' . $mitraStartRow, $mitraName);
                
                // Assuming Kriteria Mitra comes from KlasifikasiMitra relation of the first doc's mitra
                $kriteria = '';
                if ($docs->first()->mitra && $docs->first()->mitra->klasifikasiMitra) {
                    $kriteria = $docs->first()->mitra->klasifikasiMitra->nama;
                }
                $sheet->setCellValue('I' . $mitraStartRow, $kriteria);
            }

            // Styling Borders
            $styleArray = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                ],
            ];
            $sheet->getStyle('A1:I' . ($row - 1))->applyFromArray($styleArray);


            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            return response()->streamDownload(function() use ($writer) {
                $writer->save('php://output');
            }, 'Logbook_' . date('Y-m-d_H-i-s') . '.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal export data', 'error' => $e->getMessage()], 500);
        }
    }
}