<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DokumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('dokumen')->insert([
            // ======================
            // BANK CENTRAL ASIA (id:1)
            // ======================

            [
                'mitra_id' => 1,
                'jenis_dokumen_id' => 1, // MoU
                'nomor_dokumen_mitra' => 'BCA/MOU/001/2024',
                'nomor_dokumen_undip' => 'UNDIP/MOU/001/2024',
                'judul_dokumen' => 'Kesepakatan Bersama UNDIP dan Bank Central Asia',
                'status_id' => 5, // Terbit
                'tanggal_masuk' => Carbon::now()->subMonths(3),
                'tanggal_terbit' => Carbon::now()->subMonths(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mitra_id' => 1,
                'jenis_dokumen_id' => 2, // MoA
                'nomor_dokumen_mitra' => null,
                'nomor_dokumen_undip' => null,
                'judul_dokumen' => 'Perjanjian Kerja Sama UNDIP dan Bank Central Asia',
                'status_id' => 2, // Naskah Dikirim
                'tanggal_masuk' => Carbon::now()->subMonths(1),
                'tanggal_terbit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ============================
            // BANK RAKYAT INDONESIA (id:2)
            // ============================

            [
                'mitra_id' => 2,
                'jenis_dokumen_id' => 1, // MoU
                'nomor_dokumen_mitra' => 'BRI/MOU/010/2024',
                'nomor_dokumen_undip' => 'UNDIP/MOU/010/2024',
                'judul_dokumen' => 'Kesepakatan Bersama UNDIP dan Bank Rakyat Indonesia',
                'status_id' => 5, // Terbit
                'tanggal_masuk' => Carbon::now()->subMonths(4),
                'tanggal_terbit' => Carbon::now()->subMonths(3),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mitra_id' => 2,
                'jenis_dokumen_id' => 3, // IA
                'nomor_dokumen_mitra' => null,
                'nomor_dokumen_undip' => null,
                'judul_dokumen' => 'Implementation Arrangement Program Magang BRI–UNDIP',
                'status_id' => 3, // Acc Rektor
                'tanggal_masuk' => Carbon::now()->subWeeks(3),
                'tanggal_terbit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // =========================
            // UNIVERSITAS INDONESIA (id:3)
            // =========================

            [
                'mitra_id' => 3,
                'jenis_dokumen_id' => 1, // MoU
                'nomor_dokumen_mitra' => 'UI/MOU/2024/05',
                'nomor_dokumen_undip' => 'UNDIP/MOU/2024/05',
                'judul_dokumen' => 'Kesepakatan Bersama UNDIP dan Universitas Indonesia',
                'status_id' => 5, // Terbit
                'tanggal_masuk' => Carbon::now()->subMonths(6),
                'tanggal_terbit' => Carbon::now()->subMonths(5),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mitra_id' => 3,
                'jenis_dokumen_id' => 3, // IA
                'nomor_dokumen_mitra' => null,
                'nomor_dokumen_undip' => null,
                'judul_dokumen' => 'Implementation Arrangement Pertukaran Mahasiswa UI–UNDIP',
                'status_id' => 1, // Inisiasi & Proses
                'tanggal_masuk' => Carbon::now()->subDays(10),
                'tanggal_terbit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
