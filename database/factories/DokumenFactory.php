<?php

namespace Database\Factories;

use App\Models\JenisDokumen;
use App\Models\Mitra;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dokumen>
 */
class DokumenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mitra_id' => Mitra::factory(),
            'jenis_dokumen_id' => JenisDokumen::factory(),
            'status_id' => Status::factory(),
            'nomor_dokumen_mitra' => fake()->boolean(70) ? fake()->unique()->numerify('MITRA-####') : null,
            'nomor_dokumen_undip' => fake()->boolean(70) ? fake()->unique()->numerify('UNDIP-####') : null,
            'judul_dokumen' => fake()->sentence(6),
            'contact_person' => fake()->name(),
            'tanggal_dokumen' => fake()->date(),
            'tanggal_masuk' => fake()->date(),
            'tanggal_terbit' => null,
            'draft_dokumen' => null,
            'final_dokumen' => null,
        ];
    }

    public function terbit(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => Status::factory()->terbit(),
            'tanggal_terbit' => fake()->date(),
        ]);
    }

    public function proses(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => Status::factory()->proses(),
        ]);
    }
}
