<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Log;
use App\Models\Mitra;
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
     * @param  \Illuminate\Http\Request  $request  Includes filter parameters like 'tahun' and 'status'.
     * @return JsonResponse Structured dashboard stats including totals, chart data, and status counts.
     */
    public function getDashboardStats(\Illuminate\Http\Request $request): JsonResponse
    {
        try {
            // 1. Ambil SEMUA master status yang ada di database
            $allStatuses = Status::all();
            $statusById = $allStatuses->keyBy('id');

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
                ->map(function ($item) use ($totalDocs, $statusById) {
                    return [
                        'status_id' => $item->status_id,
                        'status' => $statusById->get($item->status_id)?->nama ?? 'Unknown',
                        'count' => $item->count,
                        'percentage' => $totalDocs > 0 ? round(($item->count / $totalDocs) * 100) : 0,
                    ];
                });

            // 3. Data Chart: Jenis Dokumen per Tahun
            $statusFilter = $request->query('status'); // Bisa berupa array ID atau 'all'
            $yearExpression = DB::connection()->getDriverName() === 'sqlite'
                ? "strftime('%Y', tanggal_dokumen)"
                : 'YEAR(tanggal_dokumen)';

            $chartYears = Dokumen::whereNotNull('tanggal_dokumen')
                ->selectRaw("{$yearExpression} as year")
                ->distinct()
                ->orderBy('year', 'asc')
                ->pluck('year');

            if ($tahun && $tahun !== 'all') {
                $chartYears = $chartYears->filter(fn ($year) => (string) $year === (string) $tahun)->values();
            }

            $chartQuery = Dokumen::whereNotNull('tanggal_dokumen')
                ->selectRaw("{$yearExpression} as year, jenis_dokumen_id, count(*) as total")
                ->groupBy('year', 'jenis_dokumen_id')
                ->orderBy('year', 'asc');

            if ($tahun && $tahun !== 'all') {
                $chartQuery->whereYear('tanggal_dokumen', $tahun);
            }

            if ($statusFilter && $statusFilter !== 'all') {
                $statusIds = is_array($statusFilter) ? $statusFilter : explode(',', $statusFilter);
                $statusIds = array_values(array_filter(array_map('intval', $statusIds)));

                if (! empty($statusIds)) {
                    $chartQuery->whereIn('status_id', $statusIds);
                }
            }

            $countsByYear = $chartQuery->get()
                ->groupBy(fn ($item) => (string) $item->year);

            $chartData = $chartYears
                ->map(function ($year) use ($countsByYear) {
                    $countsByType = $countsByYear
                        ->get((string) $year, collect())
                        ->pluck('total', 'jenis_dokumen_id');

                    return [
                        'year' => (string) $year,
                        'MoU' => $countsByType[1] ?? 0,
                        'MoA' => $countsByType[2] ?? 0,
                        'IA' => $countsByType[3] ?? 0,
                    ];
                })
                ->values();

            // Ambil tahun yang tersedia
            $availableYears = Dokumen::whereNotNull('tanggal_dokumen')
                ->selectRaw("{$yearExpression} as year")
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
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
