<?php

namespace App\Http\Controllers;


use App\Models\Mitra;
use App\Models\Dokumen;
use App\Models\Log;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController 
{
    public function getDashboardStats(): JsonResponse
    {
        try {
            // 1. Ambil SEMUA master status yang ada di database
            $allStatuses = Status::all();
            $totalDocs = Dokumen::count();

            // 2. Distribusi Status (Samping)
            $documentStatus = Dokumen::select('status_id', DB::raw('count(*) as count'))
                ->groupBy('status_id')
                ->get()
                ->map(function ($item) use ($totalDocs) {
                    return [
                        'status' => $item->status->nama ?? 'Unknown', 
                        'count' => $item->count,
                        'percentage' => $totalDocs > 0 ? round(($item->count / $totalDocs) * 100) : 0
                    ];
                });

            // 3. Data Chart Dinamis (6 Bulan Terakhir)
            $chartData = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                
                $monthlyCounts = Dokumen::whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->select('status_id', DB::raw('count(*) as total'))
                    ->groupBy('status_id')
                    ->pluck('total', 'status_id');

                // Inisialisasi data bulan
                $dataEntry = [
                    'month' => $month->translatedFormat('M'),
                ];

                // Loop SEMUA status dari database untuk mengisi key secara dinamis
                foreach ($allStatuses as $status) {
                    // Gunakan nama status sebagai Key (Contoh: "Inisiasi & Proses" => 5)
                    $dataEntry[$status->nama] = $monthlyCounts[$status->id] ?? 0;
                }

                $chartData[] = $dataEntry;
            }

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
                    'all_status_names' => $allStatuses->pluck('nama'), // Kirim daftar nama status ke frontend
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