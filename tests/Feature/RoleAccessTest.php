<?php

namespace Tests\Feature;

use App\Models\Mitra;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\TestHelpers;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;
    use TestHelpers;

    public function test_admin_can_access_users_list(): void
    {
        // ARRANGE
        $user = $this->createAdmin();
        Sanctum::actingAs($user);

        // ACT
        $response = $this->getJson('/api/v1/users');

        // ASSERT
        $response->assertStatus(200);
    }

    public function test_admin_can_access_user_search(): void
    {
        // ARRANGE
        $user = $this->createAdmin();
        Sanctum::actingAs($user);

        // ACT
        $response = $this->getJson('/api/v1/users/search?q=budi');

        // ASSERT
        $response->assertStatus(200);
    }

    public function test_operator_cannot_access_user_search(): void
    {
        // ARRANGE
        $user = $this->createOperator();
        Sanctum::actingAs($user);

        // ACT
        $response = $this->getJson('/api/v1/users/search?q=budi');

        // ASSERT
        $response->assertStatus(403);
    }

    public function test_unauthenticated_request_returns_401(): void
    {
        // ACT
        $response = $this->getJson('/api/v1/users');

        // ASSERT
        $response->assertStatus(401);
    }

    public function test_admin_can_delete_mitra(): void
    {
        // ARRANGE
        $mitra = Mitra::factory()->create();
        $user = $this->createAdmin();
        Sanctum::actingAs($user);

        // ACT
        $response = $this->deleteJson("/api/v1/mitra/{$mitra->id}");

        // ASSERT
        $response->assertStatus(200);
    }

    public function test_operator_cannot_delete_mitra(): void
    {
        // ARRANGE
        $mitra = Mitra::factory()->create();
        $user = $this->createOperator();
        Sanctum::actingAs($user);

        // ACT
        $response = $this->deleteJson("/api/v1/mitra/{$mitra->id}");

        // ASSERT
        $response->assertStatus(403);
    }
}
