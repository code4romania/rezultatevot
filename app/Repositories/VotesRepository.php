<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\DataLevel;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Mandate;
use App\Models\Party;
use App\Models\Vote;
use App\Services\CacheService;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;
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
                $result = Vote::query()
                    ->whereBelongsTo($election)
                    ->forLevel(
                        level: $level,
                        country: $country,
                        county: $county,
                        locality: $locality,
                        aggregate: $aggregate,
                    )
                    ->when($addSelect, fn (EloquentBuilder $query) => $query->addSelect($addSelect))
                    ->when($toBase, fn (EloquentBuilder $query) => $query->toBase())
                    ->get();

                $votables = self::getVotables($result, $election);

                if (
                    $election->has_lists &&
                    filled($election->properties->get('total_seats'))
                ) {
                    $mandates = Mandate::query()
                        ->whereBelongsTo($election)
                        ->forLevel(
                            level: $level,
                            county: $county,
                            // locality: $locality,
                            aggregate: $aggregate,
                        )
                        ->toBase()
                        ->get();
                } else {
                    $mandates = null;
                }

                $total = $result->sum('votes');

                return $result->map(function (stdClass|Vote $vote) use ($votables, $total, $mandates) {
                    $votable = $votables->get($vote->votable_type)->get($vote->votable_id);

                    $data = [
                        'name' => $votable->acronym ?? $votable->name,
                        'image' => $votable->getFirstMediaUrl('default', 'thumb'),
                        'votes' => (int) ensureNumeric($vote->votes),
                        'percent' => percent($vote->votes, $total),
                        'color' => hex2rgb($votable->color),
                    ];

                    if (filled($mandates)) {
                        $data['mandates'] = (int) $mandates
                            ->where('votable_type', $vote->votable_type)
                            ->where('votable_id', $vote->votable_id)
                            ->first()?->mandates;
                    }

                    return $data;
                });
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
                $result = DB::query()
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
                    ->when($toBase, fn (EloquentBuilder $query) => $query->toBase())
                    ->get();

                $votables = self::getVotables($result, $election);

                return $result->mapWithKeys(function (stdClass|Vote $vote) use ($votables) {
                    $votable = $votables->get($vote->votable_type)->get($vote->votable_id);

                    return [
                        $vote->place => [
                            'value' => percent($vote->votes, $vote->total_votes, formatted: true),
                            'color' => $votable->color,
                            'label' => $votable->getDisplayName(),
                        ],
                    ];
                });
            });
    }

    protected static function getVotables(Collection $result, Election $election): Collection
    {
        return $result
            ->groupBy('votable_type')
            ->map(function (Collection $items, string $type) use ($election) {
                $query = match ($type) {
                    (new Party)->getMorphClass() => Party::query(),
                    (new Candidate)->getMorphClass() => Candidate::query()
                        ->with('party.media'),
                };

                return $query
                    ->whereBelongsTo($election)
                    ->with('media')
                    ->whereIn('id', $items->pluck('votable_id'))
                    ->get()
                    ->keyBy('id');
            });
    }
}
