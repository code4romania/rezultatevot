<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\DataLevel;
use App\Models\Election;
use App\Models\Record;
use App\Services\CacheService;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class RecordsRepository
{
    public static function clearCache(int $election): bool
    {
        return CacheService::make('records', $election)->clear();
    }

    public static function getForLevel(
        Election $election,
        DataLevel $level,
        ?string $country = null,
        ?int $county = null,
        ?int $locality = null,
        bool $aggregate = false,
        bool $toBase = false,
        array $addSelect = [],
    ) {
        return CacheService::make('records', $election, $level, $country, $county, $locality, $aggregate, $toBase, $addSelect)
            ->remember(function () use ($election, $level, $country, $county, $locality, $aggregate, $toBase, $addSelect) {
                $query = Record::query()
                    ->whereBelongsTo($election)
                    ->forLevel(
                        level: $level,
                        country: $country,
                        county: $county,
                        locality: $locality,
                        aggregate: $aggregate,
                    )
                    ->when($addSelect, fn (EloquentBuilder $query) => $query->addSelect($addSelect))
                    ->when($toBase, fn (EloquentBuilder $query) => $query->toBase());

                return $aggregate ? $query->first() : $query->get();
            });
    }
}
