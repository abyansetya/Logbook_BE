<?php

namespace App\Http\Controllers;


use App\Models\Mitra;
use App\Models\Dokumen;
use App\Models\Log;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Provides aggregated statistical data and chart information for the application dashboard.
 */
class DashboardController 
{
    /**
     * Retrieve global statistics, status distribution, and document trends for the dashboard.
     *
     * @param \Illuminate\Http\Request $request Includes filter parameters like 'tahun' and 'status'.
     * @return JsonResponse Structured dashboard stats including totals, chart data, and status counts.
     */
    public function index(\Illuminate\Http\Request $request): JsonResponse
    {
        try {
            // 1. Ambil SEMUA master status yang ada di database
            $allStatuses = Status::all();
            
            // Filter tahun
            $tahun = $request->query('tahun');
            
            $docQuery = Dokumen::query();
            if ($tahun && $tahun !== 'all') {
                $docQuery->whereYear('tanggal_dokumen', $tahun);
            }
            
            $totalDocs = (clone $docQuery)->count();

            // 2. Distribusi Status (Samping)
            $documentStatus = (clone $docQuery)
                ->select('status_id', DB::raw('count(*) as count'))
                ->groupBy('status_id')
                ->get()
                ->map(function ($item) use ($totalDocs) {
                    return [
                        'status_id' => $item->status_id,
                        'status' => $item->status->nama ?? 'Unknown', 
                        'count' => $item->count,
                        'percentage' => $totalDocs > 0 ? round(($item->count / $totalDocs) * 100) : 0
                    ];
                });

            // 3. Data Chart: Jenis Dokumen per Tahun
            $statusFilter = $request->query('status'); // Bisa berupa array ID atau 'all'
            
            $availableYears = Dokumen::whereNotNull('tanggal_dokumen')
                ->selectRaw('YEAR(tanggal_dokumen) as year')
                ->distinct()
                ->orderBy('year', 'asc')
                ->pluck('year');

            $chartData = [];
            foreach ($availableYears as $year) {
                // Query dasar untuk tahun ini
                $yearQuery = Dokumen::whereYear('tanggal_dokumen', $year);

                // Terapkan filter status jika ada
                if ($statusFilter && $statusFilter !== 'all') {
                    if (is_array($statusFilter)) {
                        $yearQuery->whereIn('status_id', $statusFilter);
                    } else {
                        // Jika dalam format comma-separated string
                        $ids = explode(',', $statusFilter);
                        $yearQuery->whereIn('status_id', $ids);
                    }
                }

                $countsByType = (clone $yearQuery)
                    ->select('jenis_dokumen_id', DB::raw('count(*) as total'))
                    ->groupBy('jenis_dokumen_id')
                    ->pluck('total', 'jenis_dokumen_id');

                $chartData[] = [
                    'year' => (string)$year,
                    'MoU' => $countsByType[1] ?? 0,
                    'MoA' => $countsByType[2] ?? 0,
                    'IA' => $countsByType[3] ?? 0,
                ];
            }

            // Ambil tahun yang tersedia
            $availableYears = Dokumen::whereNotNull('tanggal_dokumen')
                ->selectRaw('YEAR(tanggal_dokumen) as year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');

            return response()->json([
                'success' => true,
                'data' => [
                    'totals' => [
                        'mitra' => Mitra::count(),
                        'dokumen' => $totalDocs,
                        'logs' => Log::count(),
                    ],
                    'document_status' => $documentStatus,
                    'chart_data' => $chartData,
                    'statuses' => $allStatuses, // Kirim daftar status lengkap ke frontend
                    'available_years' => $availableYears,
                    'stats_periodic' => [
                        'mitra_bulan_ini' => Mitra::whereMonth('created_at', Carbon::now()->month)->count(),
                        'dokumen_minggu_ini' => Dokumen::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
                        'log_hari_ini' => Log::whereDate('created_at', Carbon::today())->count(),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}