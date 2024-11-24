<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\DataLevel;
use App\Models\Election;
use App\Models\Turnout;
use App\Services\CacheService;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

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
        array $addSelect = [],
    ) {
        return CacheService::make('turnout', $election, $level, $country, $county, $locality, $aggregate, $toBase, $addSelect)
            ->remember(function () use ($election, $level, $country, $county, $locality, $aggregate, $toBase, $addSelect) {
                $query = Turnout::query()
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

    public static function getForLevelAndArea(
        Election $election,
        DataLevel $level,
        ?string $country = null,
        ?int $county = null,
        ?int $locality = null,
        bool $aggregate = false,
        bool $toBase = false,
        array $addSelect = [],
    ) {
        return CacheService::make(['turnout', 'area'], $election, $level, $country, $county, $locality, $aggregate, $toBase, $addSelect)
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
                    ->when($addSelect, fn (EloquentBuilder $query) => $query->addSelect($addSelect))
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
        array $addSelect = [],
    ) {
        return CacheService::make(['turnout', 'demographics'], $election, $level, $country, $county, $locality, $aggregate, $toBase, $addSelect)
            ->remember(function () use ($election, $level, $country, $county, $locality, $aggregate, $toBase, $addSelect) {
                $query = Turnout::query()
                    ->whereBelongsTo($election)
                    ->groupByDemographics(
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
