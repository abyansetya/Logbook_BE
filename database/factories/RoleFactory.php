<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->randomElement(['Admin', 'Operator', 'Viewer']),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => 'Admin',
        ]);
    }

    public function operator(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => 'Operator',
        ]);
    }

    public function viewer(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => 'Viewer',
        ]);
    }
}
