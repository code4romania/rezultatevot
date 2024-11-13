<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Election;
use App\Models\Scopes\BelongsToElectionScope;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToElection
{
    public function initializeBelongsToElection(): void
    {
        //
    }

    protected static function bootBelongsToElection(): void
    {
        static::creating(function (self $model) {
            // No need to change the election id if it's already set.
            if (filled($model->election_id)) {
                return;
            }

            if (! Filament::auth()->check() || ! Filament::hasTenancy()) {
                return;
            }
            // There's no tenant outside of Filament.
            if (filled($election = Filament::getTenant())) {
                $model->election()->associate($election);
            }
        });

        static::addGlobalScope(new BelongsToElectionScope);
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }
}
