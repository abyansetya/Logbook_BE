<?php

namespace Database\Seeders;

use App\Models\Mitra;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            // RoleSeeder::class,
            //UserSeeder::class,
            //UserRolesSeeder::class,
            // JenisDokumenSeeder::class,
            // KlasifikasiMitraSeeder::class,
            // MitraSeeder::class,
            // StatusSeeder::class,
            // DokumenSeeder::class,
            //LogSeeder::class,
        ]);
    }
}
