<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_has_role_returns_true_for_owned_role(): void
    {
        $user = User::factory()->create();
        $role = Role::factory()->admin()->create();
        $user->roles()->attach($role->id);

        $this->assertTrue($user->hasRole('Admin'));
    }

    public function test_has_role_returns_false_for_unowned_role(): void
    {
        $user = User::factory()->create();
        $role = Role::factory()->operator()->create();
        $user->roles()->attach($role->id);

        $this->assertFalse($user->hasRole('Admin'));
    }

    public function test_has_any_role_returns_true_when_matching_one(): void
    {
        $user = User::factory()->create();
        $role = Role::factory()->operator()->create();
        $user->roles()->attach($role->id);

        $this->assertTrue($user->hasAnyRole(['Admin', 'Operator']));
    }
}
