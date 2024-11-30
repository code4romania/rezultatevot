<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToCounty;
use App\Concerns\BelongsToElection;
use App\Concerns\BelongsToLocality;
use Database\Factories\MandateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Mandate extends Model
{
    use BelongsToElection;
    use BelongsToCounty;
    use BelongsToLocality;
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
        'initial',
        'redistributed',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'initial' => 'integer',
            'redistributed' => 'integer',
        ];
    }

    public function votable(): MorphTo
    {
        return $this->morphTo();
    }
}
