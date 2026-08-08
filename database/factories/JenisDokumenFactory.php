<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JenisDokumen>
 */
class JenisDokumenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->randomElement(['MoU', 'MoA', 'IA']),
        ];
    }

    public function mou(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => 'MoU',
        ]);
    }

    public function moa(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => 'MoA',
        ]);
    }

    public function ia(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => 'IA',
        ]);
    }
}
