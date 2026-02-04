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
        // Ambil semua mitra
        $mitras = DB::table('mitra')->get();
        $faker = \Faker\Factory::create('id_ID');

        foreach ($mitras as $mitra) {
            // ======================
            // Generate MoU for each Mitra (Assuming almost all have MoU)
            // ======================
            $tanggalMasukMoU = Carbon::now()->subMonths(rand(6, 12));
            $tanggalTerbitMoU = (clone $tanggalMasukMoU)->addMonths(1);
            
            $docId = DB::table('dokumen')->insertGetId([
                'mitra_id' => $mitra->id,
                'jenis_dokumen_id' => 1, // MoU
                'nomor_dokumen_mitra' => strtoupper(substr($mitra->nama, 0, 3)) . '/MOU/' . rand(100, 999) . '/' . $tanggalTerbitMoU->year,
                'nomor_dokumen_undip' => 'UNDIP/MOU/' . rand(100, 999) . '/' . $tanggalTerbitMoU->year,
                'judul_dokumen' => 'Nota Kesepahaman antara Universitas Diponegoro dan ' . $mitra->nama . ' tentang Pendidikan, Penelitian, dan Pengabdian kepada Masyarakat',
                'status_id' => 5, // Terbit
                'tanggal_masuk' => $tanggalMasukMoU,
                'tanggal_terbit' => $tanggalTerbitMoU,
                'contact_person' => $faker->name . ' - ' . $faker->phoneNumber,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // ======================
            // Generate MoA/PKS (Perjanjian Kerja Sama) for random Mitra
            // ======================
            if (rand(0, 1)) {
                $tanggalMasukMoA = Carbon::now()->subMonths(rand(1, 5));
                $statusId = rand(1, 4); // Random status not yet published
                
                DB::table('dokumen')->insert([
                    'mitra_id' => $mitra->id,
                    'jenis_dokumen_id' => 2, // MoA
                    'nomor_dokumen_mitra' => null,
                    'nomor_dokumen_undip' => null,
                    'judul_dokumen' => 'Perjanjian Kerja Sama antara Fakultas Teknik UNDIP dan ' . $mitra->nama . ' tentang Program Magang Mahasiswa',
                    'status_id' => $statusId, 
                    'tanggal_masuk' => $tanggalMasukMoA,
                    'tanggal_terbit' => null,
                    'contact_person' => $faker->name . ' - ' . $faker->phoneNumber,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // ======================
            // Generate IA (Implementation Arrangement)
            // ======================
            if (rand(0, 1)) {
                 $tanggalMasukIA = Carbon::now()->subWeeks(rand(1, 8));
                 
                 DB::table('dokumen')->insert([
                    'mitra_id' => $mitra->id,
                    'jenis_dokumen_id' => 3, // IA
                    'nomor_dokumen_mitra' => null,
                    'nomor_dokumen_undip' => null,
                    'judul_dokumen' => 'Implementation Arrangement: Kuliah Tamu oleh Praktisi dari ' . $mitra->nama,
                    'status_id' => 1, // Inisiasi
                    'tanggal_masuk' => $tanggalMasukIA,
                    'tanggal_terbit' => null,
                    'contact_person' => $faker->name . ' - ' . $faker->phoneNumber,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
