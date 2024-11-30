<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\County;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToCounty
{
    public function initializeBelongsToCounty(): void
    {
        $this->fillable[] = 'county_id';
    }

    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }
}
