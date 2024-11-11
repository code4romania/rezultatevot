<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToElection;
use App\Concerns\HasTemporaryTable;
use App\Contracts\TemporaryTable;
use App\Enums\Part;
use Database\Factories\VoteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Vote extends Model implements TemporaryTable
{
    use BelongsToElection;
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
            // 'part' => Part::class,
            'votes' => 'integer',
        ];
    }

    public function votable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTemporaryTableUniqueColumns(): array
    {
        return ['election_id', 'county_id', 'country_id', 'section', 'votable_type', 'votable_id'];
    }
}
