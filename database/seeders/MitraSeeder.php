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
        $partners = [
            [
                'nama' => 'PT Gojek Indonesia',
                'klasifikasi_mitra_id' => 4, // Startup/Teknologi
                'alamat' => 'Gedung Pasaraya Blok M, Jl. Iskandarsyah II No. 2, Jakarta Selatan',
                'contact_person' => 'Budi Santoso/081234567890',
            ],
            [
                'nama' => 'PT Telekomunikasi Indonesia (Persero) Tbk',
                'klasifikasi_mitra_id' => 9, // BUMN
                'alamat' => 'Jl. Japati No. 1, Bandung, Jawa Barat',
                'contact_person' => 'Siti Aminah/081298765432',
            ],
            [
                'nama' => 'PT Pertamina (Persero)',
                'klasifikasi_mitra_id' => 9, // BUMN
                'alamat' => 'Jl. Medan Merdeka Timur No. 1A, Jakarta Pusat',
                'contact_person' => 'Rudi Hartono/081345678901',
            ],
            [
                'nama' => 'Google Indonesia',
                'klasifikasi_mitra_id' => 3, // Teknologi Global
                'alamat' => 'Pacific Century Place Tower Level 45, SCBD, Jakarta',
                'contact_person' => 'Jason Smith/081987654321',
            ],
            [
                'nama' => 'Microsoft Indonesia',
                'klasifikasi_mitra_id' => 3, // Teknologi Global
                'alamat' => 'Jakarta Stock Exchange Building, Tower 2, Jakarta',
                'contact_person' => 'Maria Utami/085678901234',
            ],
            [
                'nama' => 'Universitas Indonesia (UI)',
                'klasifikasi_mitra_id' => 8, // PT DN QS 200
                'alamat' => 'Kampus UI Depok, Jawa Barat',
                'contact_person' => 'Prof. Dr. Ir. Heri Hermansyah/0217867222',
            ],
            [
                'nama' => 'Institut Teknologi Bandung (ITB)',
                'klasifikasi_mitra_id' => 8, // PT DN QS 200
                'alamat' => 'Jl. Ganesha No. 10, Bandung',
                'contact_person' => 'Sekretariat Rektorat/0222500935',
            ],
            [
                'nama' => 'Universitas Gadjah Mada (UGM)',
                'klasifikasi_mitra_id' => 8, // PT DN QS 200
                'alamat' => 'Bulaksumur, Caturtunggal, Sleman, Yogyakarta',
                'contact_person' => 'Humas UGM/0274512763',
            ],
            [
                'nama' => 'PT Bank Central Asia Tbk (BCA)',
                'klasifikasi_mitra_id' => 2, // Nasional Berstandar Tinggi
                'alamat' => 'Menara BCA, Grand Indonesia, Jakarta',
                'contact_person' => 'Halo BCA/1500888',
            ],
            [
                'nama' => 'PT Bank Rakyat Indonesia (Persero) Tbk',
                'klasifikasi_mitra_id' => 9, // BUMN
                'alamat' => 'Gedung BRI 1, Jl. Jenderal Sudirman Kav.44-46, Jakarta',
                'contact_person' => 'Call BRI/14017',
            ],
            [
                'nama' => 'PT Shopee International Indonesia',
                'klasifikasi_mitra_id' => 4, // Startup
                'alamat' => 'Pacific Century Place Tower, SCBD, Jakarta',
                'contact_person' => 'HR Recruitment/02180647100',
            ],
            [
                'nama' => 'PT Tokopedia',
                'klasifikasi_mitra_id' => 4, // Startup
                'alamat' => 'Tokopedia Tower, Ciputra World 2, Jakarta',
                'contact_person' => 'Partnership Team/02150813333',
            ],
            [
                'nama' => 'Badan Riset dan Inovasi Nasional (BRIN)',
                'klasifikasi_mitra_id' => 14, // Lembaga Riset
                'alamat' => 'Jl. M.H. Thamrin No. 8, Jakarta Pusat',
                'contact_person' => 'Sekretariat Utama/0213169999',
            ],
        ];

        foreach ($partners as $partner) {
            DB::table('mitra')->insert([
                'nama' => $partner['nama'],
                'klasifikasi_mitra_id' => $partner['klasifikasi_mitra_id'],
                'alamat' => $partner['alamat'],
                'contact_person' => $partner['contact_person'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

    }
}
