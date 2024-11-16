<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\VoteMonitorStat;

class VoteMonitorStatPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VoteMonitorStat $voteMonitorStat): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VoteMonitorStat $voteMonitorStat): bool
    {
        return $user->isAdmin($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VoteMonitorStat $voteMonitorStat): bool
    {
        return $user->isAdmin($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, VoteMonitorStat $voteMonitorStat): bool
    {
        return $this->delete($user, $voteMonitorStat);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, VoteMonitorStat $voteMonitorStat): bool
    {
        return $this->delete($user, $voteMonitorStat);
    }
}
