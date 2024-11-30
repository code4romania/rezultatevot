<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Locality;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToLocality
{
    public function initializeBelongsToLocality(): void
    {
        $this->fillable[] = 'locality_id';
    }

    public function locality(): BelongsTo
    {
        return $this->belongsTo(Locality::class);
    }
}
