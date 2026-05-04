<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = DB::table('roles')->where('nama', 'Admin')->value('id');
        $operatorId = DB::table('roles')->where('nama', 'Operator')->value('id');
        $viewerId = DB::table('roles')->where('nama', 'Viewer')->value('id');

        $users = [
            [
                'id' => 1,
                'nama' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
                'nim_nip' => 'ADM001',
                'role_id' => $adminId,
            ],
            [
                'id' => 2,
                'nama' => 'Operator User',
                'email' => 'operator@example.com',
                'password' => Hash::make('password123'),
                'nim_nip' => 'OPR001',
                'role_id' => $operatorId,
            ],
            [
                'id' => 3,
                'nama' => 'Viewer User',
                'email' => 'viewer@example.com',
                'password' => Hash::make('password123'),
                'nim_nip' => 'VWR001',
                'role_id' => $viewerId,
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['id' => $user['id']],
                array_merge($user, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
