<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dokumens = DB::table('dokumen')->get();
        $users = DB::table('users')->pluck('id'); // Get all user IDs
        $units = DB::table('unit')->pluck('id'); // Get all unit IDs
        $faker = \Faker\Factory::create('id_ID');

        foreach ($dokumens as $dokumen) {
            // Log 1: Inisiasi (Always exists)
            DB::table('log')->insert([
                'user_id' => $users->random(), // Random user
                'mitra_id' => $dokumen->mitra_id,
                'dokumen_id' => $dokumen->id,
                'unit_id' => $units->random(),
                'tanggal_log' => $dokumen->tanggal_masuk,
                'keterangan' => 'Dokumen diinisiasi dan masuk ke sistem',
                'created_at' => $dokumen->tanggal_masuk,
                'updated_at' => $dokumen->tanggal_masuk,
            ]);

            // Log 2: Proses (Random)
            if ($dokumen->status_id > 1) {
                $tanggalLog2 = \Carbon\Carbon::parse($dokumen->tanggal_masuk)->addDays(rand(2, 5));

                DB::table('log')->insert([
                    'user_id' => $users->random(),
                    'mitra_id' => $dokumen->mitra_id,
                    'dokumen_id' => $dokumen->id,
                    'unit_id' => $units->random(),
                    'tanggal_log' => $tanggalLog2,
                    'keterangan' => 'Draf dokumen diperiksa oleh bagian hukum/kerjasama',
                    'created_at' => $tanggalLog2,
                    'updated_at' => $tanggalLog2,
                ]);
            }

            // Log 3: Jika Terbit or nearing terbit
            if ($dokumen->status_id == 5) { // Terbit
                $tanggalLog3 = \Carbon\Carbon::parse($dokumen->tanggal_terbit);

                DB::table('log')->insert([
                    'user_id' => $users->random(),
                    'mitra_id' => $dokumen->mitra_id,
                    'dokumen_id' => $dokumen->id,
                    'unit_id' => $units->random(),
                    'tanggal_log' => $tanggalLog3,
                    'keterangan' => 'Dokumen resmi diterbitkan dan diarsipkan',
                    'created_at' => $tanggalLog3,
                    'updated_at' => $tanggalLog3,
                ]);
            }
        }
    }
}
