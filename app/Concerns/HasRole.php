<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Enums\User\Role;

trait HasRole
{
    public function initializeHasRole(): void
    {
        $this->casts['role'] = Role::class;

        $this->fillable[] = 'role';
    }

    public function hasRole(Role | string $role): bool
    {
        return $this->role->is($role);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(Role::ADMIN);
    }

    public function isContributor(): bool
    {
        return $this->hasRole(Role::CONTRIBUTOR);
    }

    public function isViewer(): bool
    {
        return $this->hasRole(Role::VIEWER);
    }
}
