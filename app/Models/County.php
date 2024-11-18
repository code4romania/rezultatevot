<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Laravel\Scout\Searchable;

class County extends Model
{
    use Searchable;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'code',
        'name',
        'old_id',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('alphabetical', function (Builder $query) {
            return $query
                ->orderBy('name');
        });
    }

    public function localities(): HasMany
    {
        return $this->hasMany(Locality::class);
    }

    public function turnouts(): HasManyThrough
    {
        return $this->hasManyThrough(Turnout::class, Locality::class);
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (string) $this->id,
            'code' => $this->code,
            'name' => $this->name,
        ];
    }

    public static function getTypesenseModelSettings(): array
    {
        return [
            'collection-schema' => [
                'fields' => [
                    [
                        'name' => 'id',
                        'type' => 'string',
                    ],
                    [
                        'name' => 'code',
                        'type' => 'string',
                    ],
                    [
                        'name' => 'name',
                        'type' => 'string',
                    ],
                ],
            ],
            'search-parameters' => [
                'query_by' => 'name,code,id',
            ],
        ];
    }
}
