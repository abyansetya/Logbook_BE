<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Status>
 */
class StatusFactory extends Factory
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
                'Inisiasi & Proses',
                'Acc Rektor',
                'Naskah Dikirim',
                'Naskah Dicetak',
                'Terbit',
                'Pending / Batal / Proses dilanjut unit lain',
            ]),
        ];
    }

    public function terbit(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => 'Terbit',
        ]);
    }

    public function proses(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => 'Inisiasi & Proses',
        ]);
    }
}
