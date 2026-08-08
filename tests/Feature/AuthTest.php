<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Feature\Concerns\TestHelpers;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    use TestHelpers;

    public function test_register_creates_pending_user(): void
    {
        // ARRANGE: buat role Viewer (karena dibutuhkan RegisterController:43)
        Role::factory()->viewer()->create();

        // ACT
        $response = $this->postJson('/api/v1/auth/register', [
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'nim_nip' => '24060123120001',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        // ASSERT
        $response->assertStatus(201)
            ->assertJsonPath('data.user.account_status', 'pending')
            ->assertJsonPath('data.user.email', 'budi@example.com');

        $this->assertDatabaseHas('users', [
            'email' => 'budi@example.com',
            'account_status' => 'pending',
        ]);
    }

    public function test_login_success_returns_token(): void
    {
        // ARRANGE: user approved (default factory sudah 'approved')
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'Password123!',
        ]);

        // ACT
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@example.com',
            'password' => 'Password123!',
        ]);

        // ASSERT
        $response->assertStatus(200)
            ->assertJsonPath('data.user.email', 'admin@example.com')
            ->assertJsonStructure(['data' => ['token']]);
    }

    public function test_login_wrong_password_returns_401(): void
    {
        //ARRANGE
        User::factory()->create([
            'email' => 'budi@example.com',
            'password' => 'Password123!',
        ]);

        //ACT
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'budi@example.com',
            'password' => 'wrongPassword123'
        ]);

        //ASSERT
        $response->assertStatus(401)
            ->assertJsonMissing(['token']);
    }

    public function test_login_pending_account_returns_403(): void
    {
        //ARRANGE
        $user = $this->createPendingUser();

        //ACT
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password', //default factory password
        ]);

        //ASSERT
        $response->assertStatus(403)
            ->assertJsonPath('message', 'Akun Anda masih menunggu persetujuan admin');
    }

    public function test_login_rejected_account_returns_403(): void
    {
        //ARRANGE
        $user = User::factory()->rejected()->create();

        //ACT
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password', //default factory password
        ]);

        //ASSERT
        $response->assertStatus(403)
            ->assertJsonPath('message', 'Registrasi akun Anda ditolak admin');
    }

    public function test_logout_revokes_token(): void
    {
        //ARRANGE
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        //ACT
        $response = $this->postJson('/api/v1/auth/logout');

        //ASSERT
        $response->assertStatus(200);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_get_me_returns_user_with_roles(): void
    {
        //ARRANGE
        $user = $this->createAdmin();
        Sanctum::actingAs($user);

        //ACT
        $response = $this->getJson('/api/v1/auth/me');

        //ASSERT
        $response->assertStatus(200)
            ->assertJsonPath('data.user.email', $user->email)
            ->assertJsonPath('data.user.roles', ['Admin']);
    }
}
