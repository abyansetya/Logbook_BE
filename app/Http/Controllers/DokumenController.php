<?php

namespace App\Http\Controllers;

use App\Http\Requests\addDokumenRequest;
use App\Http\Requests\editDokumenRequest;
use App\Http\Resources\DokumenResource;
use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Manages dokumen (MoU/MoA/IA) CRUD operations and data export.
 */
class DokumenController extends Controller
{
    /**
     * List documents with filtering and search capabilities.
     *
     * @param  Request  $request  Filter parameters (q, status, jenis_dokumen, tahun, bulan, per_page, order).
     * @return JsonResponse Paginated list of documents.
     */
    public function getDokumen(Request $request): JsonResponse
    {
        try {
            $query = Dokumen::with([
                'mitra',
                'user',
                'jenisDokumen',
                'status',
            ]);

            if ($request->has('q')) {
                $search = $request->query('q');
                $query->where(function ($q) use ($search) {
                    $q->where('judul_dokumen', 'LIKE', "%{$search}%")
                        ->orWhere('nomor_dokumen_undip', 'LIKE', "%{$search}%")
                        ->orWhere('nomor_dokumen_mitra', 'LIKE', "%{$search}%");
                });
            }

            if ($request->has('status') && $request->query('status') !== 'all') {
                $query->where('status_id', $request->query('status'));
            }

            if ($request->has('jenis_dokumen') && $request->query('jenis_dokumen') !== 'all') {
                $query->where('jenis_dokumen_id', $request->query('jenis_dokumen'));
            }

            if ($request->has('tahun') && $request->query('tahun') !== 'all') {
                $query->whereYear('tanggal_dokumen', $request->query('tahun'));
            }

            if ($request->has('bulan') && $request->query('bulan') !== 'all') {
                $bulan = (int) $request->query('bulan');
                if ($bulan >= 1 && $bulan <= 12) {
                    $query->whereMonth('tanggal_dokumen', $bulan);
                }
            }

            $perPage = min((int) $request->input('per_page', 10), 100);
            $order = in_array($request->query('order', 'desc'), ['asc', 'desc'])
                ? $request->query('order', 'desc')
                : 'desc';

            $dokumen = $query->orderBy('tanggal_masuk', $order)->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Daftar dokumen berhasil diambil',
                'data' => DokumenResource::collection($dokumen)->response()->getData(true),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem',
            ], 500);
        }
    }

    /**
     * Show detailed information for a single document, including its activity logs.
     *
     * @param  int|string  $id  The document ID.
     * @return JsonResponse Document details with related logs and users.
     */
    public function getLogbyId($id): JsonResponse
    {
        try {
            $dokumen = Dokumen::with([
                'logs' => fn ($q) => $q->orderBy('tanggal_log', 'asc'),
                'logs.user',
                'user',
                'jenisDokumen',
                'status',
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Log aktivitas berhasil dimuat',
                'data' => new DokumenResource($dokumen),
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }
    }

    /**
     * Create a new document entry with optional draft file upload.
     *
     * @param  addDokumenRequest  $request  Validated document data.
     * @return JsonResponse Created document resource.
     */
    public function addDokumen(addDokumenRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $dokumen = Dokumen::create([
                'mitra_id' => $validated['mitra_id'],
                'user_id' => $request->user()->id,
                'jenis_dokumen_id' => $validated['jenis_dokumen_id'],
                'status_id' => $validated['status_id'],
                'judul_dokumen' => $validated['judul_dokumen'],
                'contact_person' => $validated['contact_person'] ?? null,
                'nomor_dokumen_mitra' => $validated['nomor_dokumen_mitra'] ?? null,
                'nomor_dokumen_undip' => $validated['nomor_dokumen_undip'] ?? null,
                'tanggal_dokumen' => $validated['tanggal_dokumen'] ?? null,
                'tanggal_masuk' => $validated['tanggal_masuk'] ?? now()->format('Y-m-d'),
                'tanggal_terbit' => $validated['tanggal_terbit'] ?? null,
            ]);

            if ($request->hasFile('draft_dokumen')) {
                $file = $request->file('draft_dokumen');
                $path = $file->storeAs('dokumen/drafts', $file->hashName(), 'public');
                $dokumen->update(['draft_dokumen' => $path]);
            }

            if ($request->hasFile('final_dokumen')) {
                $file = $request->file('final_dokumen');
                $path = $file->storeAs('dokumen/final', $file->hashName(), 'public');
                $dokumen->update(['final_dokumen' => $path]);
            }

            $dokumen->load(['mitra', 'user', 'jenisDokumen', 'status']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil ditambahkan',
                'data' => new DokumenResource($dokumen),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan dokumen',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem',
            ], 500);
        }
    }

    /**
     * Update document details and/or upload draft/final files.
     *
     * @param  editDokumenRequest  $request  Validated document data.
     * @param  int|string  $id  The document ID.
     * @return JsonResponse Updated document resource.
     */
    public function updateDokumen(editDokumenRequest $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $dokumen = Dokumen::with('status')->lockForUpdate()->findOrFail($id);

            if ($dokumen->status && $dokumen->status->nama === 'Terbit') {
                if (! $request->user()->hasRole('Admin')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Hanya Admin yang dapat mengedit dokumen yang sudah berstatus Terbit',
                    ], 403);
                }
            }

            $validated = $request->validated();

            $dokumen->update([
                'mitra_id' => $validated['mitra_id'],
                'jenis_dokumen_id' => $validated['jenis_dokumen_id'],
                'status_id' => $validated['status_id'],
                'judul_dokumen' => $validated['judul_dokumen'],
                'contact_person' => $validated['contact_person'] ?? null,
                'nomor_dokumen_mitra' => $validated['nomor_dokumen_mitra'] ?? null,
                'nomor_dokumen_undip' => $validated['nomor_dokumen_undip'] ?? null,
                'tanggal_dokumen' => $validated['tanggal_dokumen'] ?? null,
                'tanggal_masuk' => $validated['tanggal_masuk'],
                'tanggal_terbit' => $validated['tanggal_terbit'] ?? null,
            ]);

            if ($request->hasFile('draft_dokumen')) {
                if ($dokumen->draft_dokumen) {
                    Storage::disk('public')->delete($dokumen->draft_dokumen);
                }
                $file = $request->file('draft_dokumen');
                $path = $file->storeAs('dokumen/drafts', $file->hashName(), 'public');
                $dokumen->update(['draft_dokumen' => $path]);
            } elseif ($request->has('draft_dokumen') && empty($request->input('draft_dokumen'))) {
                if ($dokumen->draft_dokumen) {
                    Storage::disk('public')->delete($dokumen->draft_dokumen);
                }
                $dokumen->update(['draft_dokumen' => null]);
            }

            if ($request->hasFile('final_dokumen')) {
                if ($dokumen->final_dokumen) {
                    Storage::disk('public')->delete($dokumen->final_dokumen);
                }
                $file = $request->file('final_dokumen');
                $path = $file->storeAs('dokumen/final', $file->hashName(), 'public');
                $dokumen->update(['final_dokumen' => $path]);
            } elseif ($request->has('final_dokumen') && empty($request->input('final_dokumen'))) {
                if ($dokumen->final_dokumen) {
                    Storage::disk('public')->delete($dokumen->final_dokumen);
                }
                $dokumen->update(['final_dokumen' => null]);
            }

            $dokumen->load(['mitra', 'user', 'jenisDokumen', 'status']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diperbarui',
                'data' => new DokumenResource($dokumen),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui dokumen',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem',
            ], 500);
        }
    }

    /**
     * Permanently delete a document and its associated records.
     *
     * @param  int|string  $id  The document ID.
     * @return JsonResponse Success or failure message.
     */
    public function deleteDokumen($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $dokumen = Dokumen::lockForUpdate()->findOrFail($id);

            // Delete files if they exist
            if ($dokumen->draft_dokumen) {
                Storage::disk('public')->delete($dokumen->draft_dokumen);
            }
            if ($dokumen->final_dokumen) {
                Storage::disk('public')->delete($dokumen->final_dokumen);
            }

            // Delete related records
            $dokumen->logs()->delete();
            $dokumen->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus dokumen',
            ], 500);
        }
    }

    /**
     * Search for documents by title for autocomplete or quick navigation.
     *
     * @param  Request  $request  Search query 'q'.
     * @return JsonResponse List of matching document resources.
     */
    public function searchDokumen(Request $request): JsonResponse
    {
        try {
            $query = $request->query('q');

            if (empty($query)) {
                return response()->json(['success' => true, 'data' => []]);
            }

            $dokumens = Dokumen::where('judul_dokumen', 'LIKE', "%{$query}%")
                ->with('user')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => DokumenResource::collection($dokumens),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem',
            ], 500);
        }
    }

    /**
     * Export the filtered dokumen data to an Excel spreadsheet.
     *
     * @param  Request  $request  Filter parameters (same as index).
     * @return \Symfony\Component\HttpFoundation\StreamedResponse Streamed Excel file download.
     */
    public function exportDokumen(Request $request)
    {
        try {
            $query = Dokumen::with(['mitra.klasifikasiMitra', 'user', 'jenisDokumen', 'status', 'logs.user']);

            if ($request->has('q')) {
                $search = $request->query('q');
                $query->where(function ($q) use ($search) {
                    $q->where('judul_dokumen', 'LIKE', "%{$search}%")
                        ->orWhere('nomor_dokumen_undip', 'LIKE', "%{$search}%")
                        ->orWhere('nomor_dokumen_mitra', 'LIKE', "%{$search}%");
                });
            }

            if ($request->has('status') && $request->query('status') !== 'all') {
                $query->where('status_id', $request->query('status'));
            }

            if ($request->has('jenis_dokumen') && $request->query('jenis_dokumen') !== 'all') {
                $query->where('jenis_dokumen_id', $request->query('jenis_dokumen'));
            }

            if ($request->has('tahun') && $request->query('tahun') !== 'all') {
                $query->whereYear('tanggal_dokumen', $request->query('tahun'));
            }

            if ($request->has('bulan') && $request->query('bulan') !== 'all') {
                $bulan = (int) $request->query('bulan');
                if ($bulan >= 1 && $bulan <= 12) {
                    $query->whereMonth('tanggal_dokumen', $bulan);
                }
            }

            $order = in_array($request->query('order', 'desc'), ['asc', 'desc'])
                ? $request->query('order', 'desc')
                : 'desc';

            $dokumens = $query->orderBy('tanggal_masuk', $order)->get();

            $groupedData = $dokumens->groupBy(fn ($item) => $item->mitra ? $item->mitra->nama : 'Tanpa Mitra');

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();

            $headers = ['No', 'Nama Mitra', 'Judul Dokumen', 'Tanggal', 'Keterangan', 'Contact Person', 'Nomor Dokumen', 'Nomor Mitra', 'Status', 'Kriteria Mitra'];
            $columnLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];

            foreach ($headers as $index => $header) {
                $sheet->setCellValue($columnLetters[$index].'1', $header);
                $sheet->getStyle($columnLetters[$index].'1')->getFont()->setBold(true);
                $sheet->getStyle($columnLetters[$index].'1')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFF00');
                $sheet->getColumnDimension($columnLetters[$index])->setAutoSize(true);
            }

            $row = 2;
            $no = 1;

            foreach ($groupedData as $mitraName => $docs) {
                $docs = ($order === 'desc')
                    ? $docs->sortByDesc('tanggal_masuk')
                    : $docs->sortBy('tanggal_masuk');

                $mitraStartRow = $row;

                foreach ($docs as $dokumen) {
                    $dokumenStartRow = $row;
                    $logs = ($order === 'desc')
                        ? $dokumen->logs->sortByDesc('tanggal_log')
                        : $dokumen->logs->sortBy('tanggal_log');

                    if ($logs->count() > 0) {
                        foreach ($logs as $log) {
                            $sheet->setCellValue('D'.$row, $log->tanggal_log);
                            $sheet->setCellValue('E'.$row, $log->keterangan);
                            $sheet->setCellValue('F'.$row, $log->contact_person);
                            $row++;
                        }
                    } else {
                        $row++;
                    }

                    $dokumenEndRow = $row - 1;

                    if ($dokumenEndRow > $dokumenStartRow) {
                        $sheet->mergeCells("C{$dokumenStartRow}:C{$dokumenEndRow}");
                        $sheet->mergeCells("G{$dokumenStartRow}:G{$dokumenEndRow}");
                        $sheet->mergeCells("H{$dokumenStartRow}:H{$dokumenEndRow}");
                        $sheet->mergeCells("I{$dokumenStartRow}:I{$dokumenEndRow}");
                    }

                    $sheet->setCellValue('C'.$dokumenStartRow, $dokumen->judul_dokumen);
                    $sheet->setCellValue('G'.$dokumenStartRow, $dokumen->nomor_dokumen_undip ?? '-');
                    $sheet->setCellValue('H'.$dokumenStartRow, $dokumen->nomor_dokumen_mitra ?? '-');
                    $sheet->getStyle('G'.$dokumenStartRow)->getAlignment()->setWrapText(true);
                    $sheet->getStyle('H'.$dokumenStartRow)->getAlignment()->setWrapText(true);
                    $sheet->setCellValue('I'.$dokumenStartRow, $dokumen->status ? $dokumen->status->nama : '-');
                }

                $mitraEndRow = $row - 1;

                if ($mitraEndRow > $mitraStartRow) {
                    $sheet->mergeCells("A{$mitraStartRow}:A{$mitraEndRow}");
                    $sheet->mergeCells("B{$mitraStartRow}:B{$mitraEndRow}");
                    $sheet->mergeCells("J{$mitraStartRow}:J{$mitraEndRow}");
                }

                $sheet->setCellValue('A'.$mitraStartRow, $no++);
                $sheet->setCellValue('B'.$mitraStartRow, $mitraName);

                $kriteria = '';
                if ($docs->first()->mitra && $docs->first()->mitra->klasifikasiMitra) {
                    $kriteria = $docs->first()->mitra->klasifikasiMitra->nama;
                }
                $sheet->setCellValue('J'.$mitraStartRow, $kriteria);
            }

            $sheet->getStyle('A1:J'.($row - 1))->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                ],
            ]);

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, 'Logbook_'.date('Y-m-d_H-i-s').'.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal export data',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan sistem',
            ], 500);
        }
    }
}
