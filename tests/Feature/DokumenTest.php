<?php

namespace Tests\Feature;

use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\Mitra;
use App\Models\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\TestHelpers;
use Tests\TestCase;

class DokumenTest extends TestCase
{
    use RefreshDatabase;
    use TestHelpers;

    public function test_list_dokumen_returns_paginated_data(): void
    {
        // ARRANGE
        $user = $this->createViewer();
        Sanctum::actingAs($user);

        Dokumen::factory()->count(3)->create();

        // ACT
        $response = $this->getJson('/api/v1/logbook');

        // ASSERT
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data.data');
    }

    public function test_create_dokumen_success(): void
    {
        // ARRANGE
        $user = $this->createAdmin();
        Sanctum::actingAs($user);

        $mitra = Mitra::factory()->create();
        $jenis = JenisDokumen::factory()->mou()->create();
        $status = Status::factory()->proses()->create();

        // ACT
        $response = $this->postJson('/api/v1/logbook/dokumen', [
            'mitra_id' => $mitra->id,
            'jenis_dokumen_id' => $jenis->id,
            'status_id' => $status->id,
            'judul_dokumen' => 'Kerja sama penelitian',
            'nomor_dokumen_undip' => 'UNDIP-2026-001',
        ]);

        // ASSERT
        $response->assertStatus(201)
            ->assertJsonPath('data.judul_dokumen', 'Kerja sama penelitian');

        $this->assertDatabaseHas('dokumen', ['judul_dokumen' => 'Kerja sama penelitian']);
    }

    public function test_create_dokumen_validation_error(): void
    {
        // ARRANGE
        $user = $this->createAdmin();
        Sanctum::actingAs($user);

        // ACT
        $response = $this->postJson('/api/v1/logbook/dokumen', [
            'judul_dokumen' => '',
            'mitra_id' => 999,
        ]);

        // ASSERT
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['judul_dokumen', 'mitra_id']);
    }

    public function test_show_dokumen_returns_detail(): void
    {
        // ARRANGE
        $user = $this->createViewer();
        Sanctum::actingAs($user);

        $dokumen = Dokumen::factory()->create();

        // ACT
        $response = $this->getJson("/api/v1/logbook/dokumen/{$dokumen->id}");

        // ASSERT
        $response->assertStatus(200)
            ->assertJsonPath('data.judul_dokumen', $dokumen->judul_dokumen);
    }

    public function test_update_dokumen_success(): void
    {
        // ARRANGE
        $user = $this->createAdmin();
        Sanctum::actingAs($user);

        $dokumen = Dokumen::factory()->create();

        // ACT
        $response = $this->putJson("/api/v1/logbook/edit-dokumen/{$dokumen->id}", [
            'mitra_id' => $dokumen->mitra_id,
            'jenis_dokumen_id' => $dokumen->jenis_dokumen_id,
            'status_id' => $dokumen->status_id,
            'judul_dokumen' => 'Judul diperbarui',
        ]);

        // ASSERT
        $response->assertStatus(200)
            ->assertJsonPath('data.judul_dokumen', 'Judul diperbarui');
    }

    public function test_operator_cannot_edit_terbit_dokumen(): void
    {
        // ARRANGE
        $user = $this->createOperator();
        Sanctum::actingAs($user);

        $dokumen = Dokumen::factory()->terbit()->create();

        // ACT
        $response = $this->putJson("/api/v1/logbook/edit-dokumen/{$dokumen->id}", [
            'mitra_id' => $dokumen->mitra_id,
            'jenis_dokumen_id' => $dokumen->jenis_dokumen_id,
            'status_id' => $dokumen->status_id,
            'judul_dokumen' => 'Coba edit dokumen terbit',
        ]);

        // ASSERT
        $response->assertStatus(403);
    }

    public function test_delete_dokumen_success(): void
    {
        // ARRANGE
        $user = $this->createAdmin();
        Sanctum::actingAs($user);

        $dokumen = Dokumen::factory()->create();

        // ACT
        $response = $this->deleteJson("/api/v1/logbook/delete-dokumen/{$dokumen->id}");

        // ASSERT
        $response->assertStatus(200);
        $this->assertDatabaseMissing('dokumen', ['id' => $dokumen->id]);
    }

    public function test_export_dokumen_returns_xlsx(): void
    {
        // ARRANGE
        $user = $this->createViewer();
        Sanctum::actingAs($user);

        Dokumen::factory()->count(2)->create();

        // ACT
        $response = $this->getJson('/api/v1/logbook/export');

        // ASSERT
        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
