<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('unit')->insert([
            ['nama' => 'Rektorat', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Wakil Rektor I', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Wakil Rektor II', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Wakil Rektor III', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Wakil Rektor IV', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'DHO', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
