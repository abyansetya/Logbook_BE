<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KlasifikasiMitra>
 */
class KlasifikasiMitraFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->randomElement([
                'BUMN',
                'Pemerintah Daerah',
                'Kementerian/Lembaga',
                'Perguruan Tinggi',
                'Industri',
            ]),
        ];
    }
}
