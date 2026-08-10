<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['Admin', 'Operator', 'Viewer'];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['nama' => $role],
                ['updated_at' => now()]
            );
        }
    }
}
