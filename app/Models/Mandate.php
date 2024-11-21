<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToElection;
use App\Concerns\HasTemporaryTable;
use App\Contracts\TemporaryTable;
use Database\Factories\MandateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mandate extends Model implements TemporaryTable
{
    use BelongsToElection;
    /** @use HasFactory<MandateFactory> */
    use HasFactory;
    use HasTemporaryTable;

    public $timestamps = false;

    protected static string $factory = MandateFactory::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'election_id',
        'county_id',

        'party_id',
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

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function getTemporaryTableUniqueColumns(): array
    {
        return ['election_id', 'county_id', 'party_id'];
    }
}
