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
        DB::table('log')->insert([
            [
                'user_id' => 1,
                'mitra_id' => 1,
                'dokumen_id' => 1,
                'tanggal_log' => '2025-12-20',
                'keterangan' => 'Inisiasi kerja sama dan penerimaan dokumen awal',
                'contact_person' => 'Andi - Legal Mitra',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'mitra_id' => 1,
                'dokumen_id' => 1,
                'tanggal_log' => '2025-12-23',
                'keterangan' => 'Dokumen diproses oleh unit terkait',
                'contact_person' => 'Budi - Admin Kerja Sama',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'mitra_id' => 1,
                'dokumen_id' => 1,
                'tanggal_log' => '2025-12-27',
                'keterangan' => 'Naskah dikirim ke pimpinan untuk persetujuan',
                'contact_person' => 'Citra - Sekretariat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'mitra_id' => 1,
                'dokumen_id' => 1,
                'tanggal_log' => '2026-01-02',
                'keterangan' => 'Dokumen disetujui oleh Rektor',
                'contact_person' => 'Sekretariat Rektor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'mitra_id' => 1,
                'dokumen_id' => 1,
                'tanggal_log' => '2026-01-04',
                'keterangan' => 'Naskah dicetak dan disiapkan untuk terbit',
                'contact_person' => 'Admin Percetakan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'mitra_id' => 1,
                'dokumen_id' => 1,
                'tanggal_log' => '2026-01-06',
                'keterangan' => 'Dokumen resmi diterbitkan',
                'contact_person' => 'Admin Kerja Sama',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
