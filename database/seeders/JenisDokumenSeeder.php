<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisDokumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('jenis_dokumen')->insert([
            [
                'nama' => 'Memorandum of Understanding (MoU)',
            ],
            [
                'nama' => 'Memorandum of Agreement (MoA)',
            ],
            [
                'nama' => 'Implementation Arrangement (IA)',
            ],
        ]);
    }
}
