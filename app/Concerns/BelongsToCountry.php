<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Country;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToCountry
{
    public function initializeBelongsToCountry(): void
    {
        $this->fillable[] = 'country_id';
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
