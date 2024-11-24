<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToElection;
use App\Concerns\CanGroupByDataLevel;
use App\Concerns\HasTemporaryTable;
use App\Contracts\ClearsCache;
use App\Contracts\TemporaryTable;
use App\Enums\DataLevel;
use App\Enums\Part;
use App\Repositories\RecordsRepository;
use Database\Factories\RecordFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tpetry\QueryExpressions\Function\Aggregate\Sum;
use Tpetry\QueryExpressions\Language\Alias;

class Record extends Model implements TemporaryTable, ClearsCache
{
    use BelongsToElection;
    use CanGroupByDataLevel;
    /** @use HasFactory<RecordFactory> */
    use HasFactory;
    use HasTemporaryTable;

    public $timestamps = false;

    protected static string $factory = RecordFactory::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'election_id',
        'county_id',
        'locality_id',
        'section',
        'part',

        // 'eligible_voters_total', // a = a1 + a2
        'eligible_voters_permanent', // a1 >= b1
        'eligible_voters_special', // a2 >= b2
        // 'present_voters_total', // b = b1 + b2 + b3
        'present_voters_permanent', // b1
        'present_voters_special', // b2
        'present_voters_supliment', // b3
        'present_voters_mail', // b4
        'papers_received', // c >= d + e + f
        'papers_unused', // d
        'votes_valid', // e <= b - f
        'votes_null', // f

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
            'eligible_voters_total' => 'integer',
            'eligible_voters_permanent' => 'integer',
            'eligible_voters_special' => 'integer',
            'present_voters_total' => 'integer',
            'present_voters_permanent' => 'integer',
            'present_voters_special' => 'integer',
            'present_voters_supliment' => 'integer',
            'present_voters_mail' => 'integer',
            'papers_received' => 'integer',
            'papers_unused' => 'integer',
            'votes_valid' => 'integer',
            'votes_null' => 'integer',
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
                new Alias(new Sum('eligible_voters_total'), 'eligible_voters_total'),
                new Alias(new Sum('present_voters_total'), 'present_voters_total'),
                new Alias(new Sum('votes_valid'), 'votes_valid'),
                new Alias(new Sum('votes_null'), 'votes_null'),
            ])
            ->forDataLevel($level, $country, $county, $locality, $aggregate);
    }

    public function getTemporaryTableUniqueColumns(): array
    {
        return ['election_id', 'county_id', 'country_id', 'section'];
    }

    public function clearCache(int $electionId): bool
    {
        return RecordsRepository::clearCache($electionId);
    }
}
