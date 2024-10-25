<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Area;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Statistic extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    public static function segments(): Collection
    {
        return collect(['men', 'women'])
            ->crossJoin([
                '18-24', '25-34', '35-44', '45-64', '65+',
                // ...range(18, 120),
            ]);
    }

    protected function casts(): array
    {
        return [
            'area' => Area::class,
            ...static::segments()
                ->mapWithKeys(fn (array $segment) => [
                    "{$segment[0]}_{$segment[1]}" => 'integer',
                ])
                ->all(),
        ];
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    public function locality(): BelongsTo
    {
        return $this->belongsTo(Locality::class);
    }
}
