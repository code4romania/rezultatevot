<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\DataLevel;
use App\Models\Election;
use App\Models\Turnout;
use App\Services\CacheService;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class TurnoutRepository
{
    public static function clearCache(int $election): bool
    {
        return CacheService::make('turnout', $election)->clear();
    }

    public static function getForLevel(
        Election $election,
        DataLevel $level,
        ?string $country = null,
        ?int $county = null,
        ?int $locality = null,
        bool $aggregate = false,
        bool $toBase = false,
    ) {
        return CacheService::make('turnout', $election, $level, $country, $county, $locality, $aggregate, $toBase)
            ->remember(
                fn () => Turnout::query()
                    ->whereBelongsTo($election)
                    ->forLevel(
                        level: $level,
                        country: $country,
                        county: $county,
                        locality: $locality,
                        aggregate: $aggregate,
                    )
                    ->when($toBase, fn (EloquentBuilder $query) => $query->toBase())
                    ->when(
                        $aggregate,
                        fn (EloquentBuilder|QueryBuilder $query) => $query->first(),
                        fn (EloquentBuilder|QueryBuilder $query) => $query->get()
                    )
            );
    }

    public static function getForLevelAndArea(
        Election $election,
        DataLevel $level,
        ?string $country = null,
        ?int $county = null,
        ?int $locality = null,
        bool $aggregate = false,
        bool $toBase = false,
    ) {
        return CacheService::make(['turnout', 'area'], $election, $level, $country, $county, $locality, $aggregate, $toBase)
            ->remember(
                fn () => Turnout::query()
                    ->whereBelongsTo($election)
                    ->groupByLevelAndArea(
                        level: $level,
                        country: $country,
                        county: $county,
                        locality: $locality,
                        aggregate: $aggregate,
                    )
                    ->when($toBase, fn (EloquentBuilder $query) => $query->toBase())
                    ->get()
            );
    }

    public static function getDemographicsForLevel(
        Election $election,
        DataLevel $level,
        ?string $country = null,
        ?int $county = null,
        ?int $locality = null,
        bool $aggregate = false,
        bool $toBase = false,
    ) {
        return CacheService::make(['turnout', 'demographics'], $election, $level, $country, $county, $locality, $aggregate, $toBase)
            ->remember(
                fn () => Turnout::query()
                    ->whereBelongsTo($election)
                    ->groupByDemographics(
                        level: $level,
                        country: $country,
                        county: $county,
                        locality: $locality,
                        aggregate: $aggregate,
                    )
                    ->when($toBase, fn (EloquentBuilder $query) => $query->toBase())
                    ->when(
                        $aggregate,
                        fn (EloquentBuilder|QueryBuilder $query) => $query->first(),
                        fn (EloquentBuilder|QueryBuilder $query) => $query->get()
                    )
            );
    }
}
