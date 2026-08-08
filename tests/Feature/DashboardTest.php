<?php

namespace Tests\Feature;

use App\Models\Dokumen;
use App\Models\Mitra;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Feature\Concerns\TestHelpers;

class DashboardTest extends TestCase
{
    use RefreshDatabase;
    use TestHelpers;

    public function test_dashboard_returns_correct_totals(): void
    {
        //ARRANGE
        $user = $this->createViewer();
        Sanctum::actingAs($user);

        $mitras = Mitra::factory()->count(3)->create();
        // Buat dokumen dengan mitra yang sudah ada, agar jumlah mitra pasti 3
        Dokumen::factory()->count(5)->create([
            'mitra_id' => $mitras->first()->id,
        ]);

        //ACT
        $response = $this->getJson('/api/v1/dashboard');

        //ASSERT
        $response->assertStatus(200)
            ->assertJsonPath('data.totals.mitra', 3)
            ->assertJsonPath('data.totals.dokumen', 5)
            ->assertJsonPath('data.totals.logs', 0);
    }

    public function test_dashboard_has_complete_structure(): void
    {
        //ARRANGE
        $user = $this->createAdmin();
        Sanctum::actingAs($user);

        Mitra::factory()->count(2)->create();
        Dokumen::factory()->count(2)->create();

        //ACT
        $response = $this->getJson('/api/v1/dashboard');

        //ASSERT
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'totals' => ['mitra', 'dokumen', 'logs'],
                    'document_status',
                    'chart_data',
                    'statuses',
                    'available_years',
                    'stats_periodic' => ['mitra_bulan_ini', 'dokumen_minggu_ini', 'log_hari_ini'],
                ],
            ]);
    }

    public function test_dashboard_filter_by_year(): void
    {
        //ARRANGE
        $user = $this->createAdmin();
        Sanctum::actingAs($user);

        Dokumen::factory()->create(['tanggal_dokumen' => '2026-05-10']);
        Dokumen::factory()->create(['tanggal_dokumen' => '2025-05-10']);

        //ACT
        $response = $this->getJson('/api/v1/dashboard?tahun=2026');

        //ASSERT
        $response->assertStatus(200)
            ->assertJsonPath('data.totals.dokumen', 1);
    }
}
