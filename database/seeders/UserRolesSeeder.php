<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRolesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user_roles')->insert([
            [
                'user_id' => 1,
                'role_id' => 1, // admin
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'role_id' => 2, // mahasiswa
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
