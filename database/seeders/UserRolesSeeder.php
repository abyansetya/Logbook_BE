<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRolesSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = DB::table('roles')->where('nama', 'Admin')->value('id');
        $operatorId = DB::table('roles')->where('nama', 'Operator')->value('id');
        $viewerId = DB::table('roles')->where('nama', 'Viewer')->value('id');

        $userRoles = [
            ['user_id' => 1, 'role_id' => $adminId],    // Abyan
            ['user_id' => 2, 'role_id' => $operatorId], // Fakhrel
            ['user_id' => 3, 'role_id' => $viewerId],   // Hedar
        ];

        foreach ($userRoles as $ur) {
            if ($ur['role_id']) {
                DB::table('user_roles')->updateOrInsert(
                    ['user_id' => $ur['user_id'], 'role_id' => $ur['role_id']],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }
}
