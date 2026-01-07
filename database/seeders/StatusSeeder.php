<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('status')->insert([
            [
                'nama' => 'Inisiasi & Proses',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Naskah Dikirim',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Acc Rektor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Naskah Dicetak',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Terbit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Pending / Batal / Proses dilanjut unit lain',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
