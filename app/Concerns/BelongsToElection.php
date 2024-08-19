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
            if (! Filament::auth()->check()) {
                return;
            }

            if (! Filament::hasTenancy()) {
                return;
            }

            $model->election()->associate(Filament::getTenant());
        });

        static::addGlobalScope(new BelongsToElectionScope);
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }
}
