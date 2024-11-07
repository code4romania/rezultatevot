<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Country extends Model
{
    use Searchable;

    public $timestamps = false;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'aliases',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'aliases' => 'array',
        ];
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
            'name' => $this->name,
            'aliases' => $this->aliases,
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
                        'name' => 'name',
                        'type' => 'string',
                    ],
                    [
                        'name' => 'aliases',
                        'type' => 'string[]',
                    ],
                ],
            ],
            'search-parameters' => [
                'query_by' => 'name,aliases',
            ],
        ];
    }
}
