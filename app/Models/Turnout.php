<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TurnoutFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Turnout extends Model
{
    /** @use HasFactory<TurnoutFactory> */
    use HasFactory;

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
        ];
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
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
}
