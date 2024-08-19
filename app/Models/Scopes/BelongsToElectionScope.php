<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class BelongsToElectionScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (! Filament::auth()->check()) {
            return;
        }

        if (! Filament::hasTenancy()) {
            return;
        }

        $builder->whereBelongsTo(Filament::getTenant());
    }
}
