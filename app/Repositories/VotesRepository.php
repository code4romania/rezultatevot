<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\DataLevel;
use App\Models\Election;
use App\Models\Vote;
use App\Services\CacheService;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\DB;
use Tpetry\QueryExpressions\Function\Aggregate\Max;
use Tpetry\QueryExpressions\Function\Aggregate\Min;
use Tpetry\QueryExpressions\Function\Aggregate\Sum;
use Tpetry\QueryExpressions\Language\Alias;

class VotesRepository
{
    public static function clearCache(int $election): bool
    {
        return CacheService::make('votes', $election)->clear();
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
        return CacheService::make('votes', $election, $level, $country, $county, $locality, $aggregate, $toBase, $addSelect)
            ->remember(function () use ($election, $level, $country, $county, $locality, $aggregate, $toBase, $addSelect) {
                $query = Vote::query()
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

                return $query->get();
            });
    }

    public static function getMapDataForLevel(
        Election $election,
        DataLevel $level,
        ?string $country = null,
        ?int $county = null,
        ?int $locality = null,
        bool $aggregate = false,
        bool $toBase = false,
        array $addSelect = [],
    ) {
        return CacheService::make(['votes', 'map-data'], $election, $level, $country, $county, $locality, $aggregate, $toBase, $addSelect)
            ->remember(function () use ($election, $level, $country, $county, $locality, $aggregate, $toBase, $addSelect) {
                $query = DB::query()
                    ->select([
                        new Alias(DB::raw('ANY_VALUE(votable_id)'), 'votable_id'),
                        new Alias(DB::raw('ANY_VALUE(votable_type)'), 'votable_type'),
                        new Alias(new Max('votes'), 'votes'),
                        new Alias(new Sum('votes'), 'total_votes'),
                        new Alias(new Min('part'), 'part'),
                        'place',
                    ])
                    ->groupBy('place')
                    ->fromSub(
                        Vote::query()
                            ->whereBelongsTo($election)
                            ->forLevel(
                                level: $level,
                                country: $country,
                                county: $county,
                                locality: $locality,
                                aggregate: $aggregate,
                            )
                            ->toBase(),
                        'votes'
                    )
                    ->when($addSelect, fn (EloquentBuilder $query) => $query->addSelect($addSelect))
                    ->when($toBase, fn (EloquentBuilder $query) => $query->toBase());

                return $query->get();
            });
    }
}
