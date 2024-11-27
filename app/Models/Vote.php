<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToElection;
use App\Concerns\CanGroupByDataLevel;
use App\Concerns\HasTemporaryTable;
use App\Contracts\TemporaryTable;
use App\Enums\DataLevel;
use App\Enums\Part;
use Database\Factories\VoteFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Tpetry\QueryExpressions\Function\Aggregate\Min;
use Tpetry\QueryExpressions\Function\Aggregate\Sum;
use Tpetry\QueryExpressions\Language\Alias;

class Vote extends Model implements TemporaryTable
{
    use BelongsToElection;
    use CanGroupByDataLevel;
    /** @use HasFactory<VoteFactory> */
    use HasFactory;
    use HasTemporaryTable;

    protected static string $factory = VoteFactory::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'election_id',
        'country_id',
        'county_id',
        'locality_id',
        'section',
        'part',
        'votable_type',
        'votable_id',
        'votes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'part' => Part::class,
            'votes' => 'integer',
        ];
    }

    public function votable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForLevel(Builder $query, DataLevel $level, ?string $country = null, ?int $county = null, ?int $locality = null, bool $aggregate = false): Builder
    {
        return $query
            ->select([
                'votable_type',
                'votable_id',
                new Alias(new Sum('votes'), 'votes'),
                new Alias(new Min('part'), 'part'),
            ])
            ->groupBy('votable_type', 'votable_id')
            ->forDataLevel($level, $country, $county, $locality, $aggregate)
            ->orderByDesc('votes');
    }

    public function scopeWithVotable(Builder $query, bool $withMedia = false): Builder
    {
        return $query->with([
            'votable' => function (MorphTo $morphTo) use ($withMedia) {
                $morphTo->morphWith([
                    Candidate::class => $withMedia ? ['party.media'] : ['party'],
                    Party::class => $withMedia ? ['media'] : [],
                ]);
            },
        ]);
    }

    public function getTemporaryTableUniqueColumns(): array
    {
        return ['election_id', 'county_id', 'country_id', 'section', 'votable_type', 'votable_id'];
    }
}
