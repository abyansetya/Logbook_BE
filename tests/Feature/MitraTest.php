<?php

namespace Tests\Feature;

use App\Models\KlasifikasiMitra;
use App\Models\Mitra;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Feature\Concerns\TestHelpers;

class MitraTest extends TestCase
{
    use RefreshDatabase;
    use TestHelpers;

    public function test_list_mitra_returns_data(): void
    {
        //ARRANGE
        $user = $this->createViewer();
        Sanctum::actingAs($user);

        Mitra::factory()->count(3)->create();

        //ACT
        $response = $this->getJson('/api/v1/mitra');

        //ASSERT
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data.data');
    }

    public function test_admin_add_mitra_is_approved(): void
    {
        //ARRANGE
        $user = $this->createAdmin();
        Sanctum::actingAs($user);

        $klasifikasi = KlasifikasiMitra::factory()->create();

        //ACT
        $response = $this->postJson('/api/v1/mitra', [
            'nama' => 'PT Maju Bersama',
            'klasifikasi_mitra_id' => $klasifikasi->id,
        ]);

        //ASSERT
        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'approved');
    }

    public function test_operator_add_mitra_is_pending(): void
    {
        //ARRANGE
        $user = $this->createOperator();
        Sanctum::actingAs($user);

        $klasifikasi = KlasifikasiMitra::factory()->create();

        //ACT
        $response = $this->postJson('/api/v1/mitra', [
            'nama' => 'PT Maju Bersama',
            'klasifikasi_mitra_id' => $klasifikasi->id,
        ]);

        //ASSERT
        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'pending');
    }

    public function test_operator_update_mitra_reverts_to_pending(): void
    {
        //ARRANGE
        $user = $this->createOperator();
        Sanctum::actingAs($user);

        $mitra = Mitra::factory()->create();

        //ACT
        $response = $this->putJson("/api/v1/mitra/{$mitra->id}", [
            'nama' => 'Nama Baru Mitra',
            'klasifikasi_mitra_id' => $mitra->klasifikasi_mitra_id,
        ]);

        //ASSERT
        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'pending');
    }

    public function test_approve_mitra_admin_only(): void
    {
        //Admin sukses
        $admin = $this->createAdmin();
        Sanctum::actingAs($admin);
        $mitra = Mitra::factory()->pending()->create();

        $response = $this->putJson("/api/v1/mitra/{$mitra->id}/approve");
        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'approved');

        //Operator ditolak
        $operator = $this->createOperator();
        Sanctum::actingAs($operator);
        $mitra2 = Mitra::factory()->pending()->create();

        $response2 = $this->putJson("/api/v1/mitra/{$mitra2->id}/approve");
        $response2->assertStatus(403);
    }

    public function test_delete_mitra_admin_only(): void
    {
        //Admin sukses
        $admin = $this->createAdmin();
        Sanctum::actingAs($admin);
        $mitra = Mitra::factory()->create();

        $response = $this->deleteJson("/api/v1/mitra/{$mitra->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('mitra', ['id' => $mitra->id]);

        //Operator ditolak
        $operator = $this->createOperator();
        Sanctum::actingAs($operator);
        $mitra2 = Mitra::factory()->create();

        $response2 = $this->deleteJson("/api/v1/mitra/{$mitra2->id}");
        $response2->assertStatus(403);
    }
}
