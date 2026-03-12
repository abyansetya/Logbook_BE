<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'id' => 1,
                'nama' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
            ],           
            [
                'id' => 2,
                'nama' => 'Operator User',
                'email' => 'operator@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'id' => 3,
                'nama' => 'Viewer User',
                'email' => 'viewer@example.com',
                'password' => Hash::make('password123'),
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
