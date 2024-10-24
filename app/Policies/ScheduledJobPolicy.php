<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ScheduledJob;
use App\Models\User;

class ScheduledJobPolicy
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
    public function view(User $user, ScheduledJob $scheduledJob): bool
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
    public function update(User $user, ScheduledJob $scheduledJob): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ScheduledJob $scheduledJob): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ScheduledJob $scheduledJob): bool
    {
        return $this->delete($user, $scheduledJob);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ScheduledJob $scheduledJob): bool
    {
        return $this->delete($user, $scheduledJob);
    }
}
