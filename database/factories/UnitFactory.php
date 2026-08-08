<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Unit>
 */
class UnitFactory extends Factory
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
                'Fakultas Teknik',
                'Fakultas Ekonomika dan Bisnis',
                'Fakultas Hukum',
                'Fakultas Kedokteran',
                'Sekolah Vokasi',
                'Rektorat',
            ]),
        ];
    }
}
