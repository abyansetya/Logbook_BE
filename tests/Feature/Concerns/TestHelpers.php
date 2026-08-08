<?php

namespace Tests\Feature\Concerns;

use App\Models\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

trait TestHelpers
{
    protected function createAdmin(): User
    {
        return $this->createUserWithRole('Admin');
    }

    protected function createOperator(): User
    {
        return $this->createUserWithRole('Operator');
    }

    protected function createViewer(): User
    {
        return $this->createUserWithRole('Viewer');
    }

    protected function createPendingUser(): User
    {
        return User::factory()->pending()->create();
    }

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::factory()->create(['nama' => $roleName]);
        $user = User::factory()->create();
        $user->roles()->attach($role->id);

        return $user;
    }
}