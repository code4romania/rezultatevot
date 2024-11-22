<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToElection;
use App\Concerns\CanGroupByDataLevel;
use App\Concerns\HasTemporaryTable;
use App\Contracts\TemporaryTable;
use App\Enums\Area;
use App\Enums\DataLevel;
use Database\Factories\TurnoutFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Tpetry\QueryExpressions\Function\Aggregate\Sum;
use Tpetry\QueryExpressions\Language\Alias;

class Turnout extends Model implements TemporaryTable
{
    use BelongsToElection;
    use CanGroupByDataLevel;
    /** @use HasFactory<TurnoutFactory> */
    use HasFactory;
    use HasTemporaryTable;

    public $timestamps = false;

    protected static string $factory = TurnoutFactory::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'initial_permanent',
        'initial_complement',
        'permanent',
        'complement',
        'supplement',
        'mobile',
        'has_issues',
        'country_id',
        'county_id',
        'locality_id',
        'election_id',
        'section',
        'area',
        'men_18-24',
        'men_25-34',
        'men_35-44',
        'men_45-64',
        'men_65',
        'women_18-24',
        'women_25-34',
        'women_35-44',
        'women_45-64',
        'women_65',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'initial_permanent' => 'integer',
            'initial_complement' => 'integer',
            'permanent' => 'integer',
            'complement' => 'integer',
            'supplement' => 'integer',
            'mobile' => 'integer',
            'has_issues' => 'boolean',

            'area' => Area::class,
            'men_18-24' => 'integer',
            'men_25-34' => 'integer',
            'men_35-44' => 'integer',
            'men_45-64' => 'integer',
            'men_65' => 'integer',
            'women_18-24' => 'integer',
            'women_25-34' => 'integer',
            'women_35-44' => 'integer',
            'women_45-64' => 'integer',
            'women_65' => 'integer',
        ];
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

    public function scopeForLevel(Builder $query, DataLevel $level, ?string $country = null, ?int $county = null, ?int $locality = null, bool $aggregate = false): Builder
    {
        return $query
            ->select([
                new Alias(new Sum('initial_total'), 'initial_total'),
                new Alias(new Sum('total'), 'total'),
            ])
            ->forDataLevel($level, $country, $county, $locality, $aggregate);
    }

    public function scopeGroupByLevelAndArea(Builder $query, DataLevel $level, ?string $country = null, ?int $county = null, ?int $locality = null, bool $aggregate = false): Builder
    {
        return $query
            ->select([
                new Alias(new Sum('total'), 'total'),
                'area',
            ])
            ->forDataLevel($level, $country, $county, $locality, $aggregate)
            ->groupBy('area');
    }

    public function scopeGroupByDemographics(Builder $query, DataLevel $level, ?string $country = null, ?int $county = null, ?int $locality = null, bool $aggregate = false): Builder
    {
        return $query
            ->select([
                new Alias(new Sum('men_18-24'), 'men_18-24'),
                new Alias(new Sum('men_25-34'), 'men_25-34'),
                new Alias(new Sum('men_35-44'), 'men_35-44'),
                new Alias(new Sum('men_45-64'), 'men_45-64'),
                new Alias(new Sum('men_65'), 'men_65'),
                new Alias(new Sum('women_18-24'), 'women_18-24'),
                new Alias(new Sum('women_25-34'), 'women_25-34'),
                new Alias(new Sum('women_35-44'), 'women_35-44'),
                new Alias(new Sum('women_45-64'), 'women_45-64'),
                new Alias(new Sum('women_65'), 'women_65'),
            ])
            ->forDataLevel($level, $country, $county, $locality, $aggregate);
    }

    public static function segments(): Collection
    {
        return collect(['men', 'women'])
            ->crossJoin(['18-24', '25-34', '35-44', '45-64', '65']);
    }

    public static function segmentsMap(): Collection
    {
        return static::segments()
            ->mapWithKeys(fn (array $segment) => [
                "{$segment[0]}_{$segment[1]}" => \sprintf(
                    '%s %s',
                    match ($segment[0]) {
                        'men' => 'Barbati',
                        'women' => 'Femei',
                    },
                    match ($segment[1]) {
                        default => $segment[1],
                        '65' => '65+'
                    }
                ),
            ]);
    }

    public function getTemporaryTableUniqueColumns(): array
    {
        return ['election_id', 'county_id', 'country_id', 'section'];
    }

    public function getNameAttribute(): string
    {
        if ($this->hasAttribute('locality_id') && $this->locality_id) {
            return $this->loadMissing('locality')->locality->name;
        }

        if ($this->hasAttribute('county_id') && $this->county_id) {
            return $this->loadMissing('county')->county->name;
        }
        if ($this->hasAttribute('country_id') && $this->country_id) {
            return $this->loadMissing('country')->country->name;
        }
    }
}
