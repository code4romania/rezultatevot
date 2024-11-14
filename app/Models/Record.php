<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToElection;
use App\Concerns\HasTemporaryTable;
use App\Contracts\TemporaryTable;
use App\Enums\Part;
use Database\Factories\RecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Record extends Model implements TemporaryTable
{
    use BelongsToElection;
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

    public function getTemporaryTableUniqueColumns(): array
    {
        return ['election_id', 'county_id', 'country_id', 'section'];
    }
}
