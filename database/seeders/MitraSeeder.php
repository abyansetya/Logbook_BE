<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MitraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('mitra')->insert([
            [
                'nama' => 'Bank Central Asia',
                'klasifikasi_mitra_id' => 2,
                'alamat' => 'Jl. MH Thamrin No.1, Jakarta',
                'contact_person' => 'Titik/0812912828',
                'logo_mitra' => 'bca.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Bank Rakyat Indonesia',
                'klasifikasi_mitra_id' => 2,
                'alamat' => 'Jl. Jenderal Sudirman No.44-46, Jakarta',
                'contact_person' => 'Andi/0823344556',
                'logo_mitra' => 'bri.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Universitas Indonesia',
                'klasifikasi_mitra_id' => 8,
                'alamat' => 'Depok, Jawa Barat',
                'contact_person' => 'Rina/0836677889',
                'logo_mitra' => 'ui.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
