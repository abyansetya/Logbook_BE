<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KlasifikasiMitraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();


        
        DB::table('klasifikasi_mitra')->insert([
            [
                'nama' => 'Perusahaan Multinasional',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Perusahaan Nasional Berstandar Tinggi',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Perusahaan Teknologi Global',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Perusahaan Rintisan (Startup) Teknologi',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Organisasi Nirlaba Kelas Dunia',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Institusi atau Organisasi Multilateral',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Perguruan Tinggi Luar Negeri yang Masuk dalam Daftar QS Top 200 Berdasarkan Bidang Ilmu',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Perguruan Tinggi Dalam Negeri yang Masuk dalam Daftar QS Top 200 Berdasarkan Bidang Ilmu',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Instansi Pemerintah Pusat dan/atau Daerah, BUMN, dan/atau BUMD',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Rumah Sakit',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Dunia Usaha',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Institusi Pendidikan',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Organisasi, Perguruan Tinggi, Fakultas, atau Program Studi dalam Bidang yang Relevan',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Lembaga Riset Pemerintah, Swasta, Nasional, maupun Internasional',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Lembaga Kebudayaan Berskala Nasional atau Bereputasi Internasional',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
