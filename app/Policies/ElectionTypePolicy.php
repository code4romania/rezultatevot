<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ElectionType;
use App\Models\User;

class ElectionTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ElectionType $electionType): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ElectionType $electionType): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ElectionType $electionType): bool
    {
        return $user->isAdmin() && $electionType->elections_count === 0;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ElectionType $electionType): bool
    {
        return $this->delete($user, $electionType);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ElectionType $electionType): bool
    {
        return $this->delete($user, $electionType);
    }
}
