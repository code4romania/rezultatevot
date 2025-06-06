<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToCounty;
use App\Concerns\BelongsToElection;
use App\Concerns\BelongsToLocality;
use App\Concerns\CanGroupByDataLevel;
use App\Enums\DataLevel;
use Database\Factories\MandateFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Tpetry\QueryExpressions\Function\Aggregate\Sum;
use Tpetry\QueryExpressions\Language\Alias;

class Mandate extends Model
{
    use BelongsToElection;
    use BelongsToCounty;
    use BelongsToLocality;
    use CanGroupByDataLevel;
    /** @use HasFactory<MandateFactory> */
    use HasFactory;

    public $timestamps = false;

    protected static string $factory = MandateFactory::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'votable_type',
        'votable_id',
        'mandates',

    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'mandates' => 'integer',
        ];
    }

    public function votable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForLevel(Builder $query, DataLevel $level, ?string $country = null, ?int $county = null, ?int $locality = null, bool $aggregate = false): Builder
    {
        $query
            ->select([
                'votable_type',
                'votable_id',
                new Alias(new Sum('mandates'), 'mandates'),
            ])
            ->groupBy('votable_type', 'votable_id');

        if ($level->is(DataLevel::TOTAL)) {
            $query->groupByTotal();
        }

        if ($level->is(DataLevel::DIASPORA)) {
            $query->whereNull('county_id')
                ->groupBy('county_id');
        }

        if ($level->is(DataLevel::NATIONAL)) {
            if (filled($locality)) {
                $query->where('locality_id', $locality)
                    ->groupByLocality();
            } elseif (filled($county)) {
                $query->where('county_id', $county);

                if ($aggregate) {
                    $query->groupByCounty();
                } else {
                    $query->groupByLocality();
                }
            } else {
                $query->whereNotNull('county_id');

                if ($aggregate) {
                    $query->groupByTotal();
                } else {
                    $query->groupByCounty();
                }
            }
        }
        // ->forDataLevel($level, $country, $county, $locality, $aggregate)

        return $query->orderByDesc('mandates');
    }
}
