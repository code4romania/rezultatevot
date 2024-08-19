<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ElectionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    /** @use HasFactory<ElectionFactory> */
    use HasFactory;

    protected static string $factory = ElectionFactory::class;

    protected $fillable = [
        'title',
        'subtitle',
        'year',
        'is_live',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'int',
            'is_live' => 'boolean',
        ];
    }
}
