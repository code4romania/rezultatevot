<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ElectionFactory;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Election extends Model implements HasName
{
    /** @use HasFactory<ElectionFactory> */
    use HasFactory;

    protected static string $factory = ElectionFactory::class;

    protected $fillable = [
        'title',
        'subtitle',
        'slug',
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

    public function type(): BelongsTo
    {
        return $this->belongsTo(ElectionType::class);
    }

    public function getFilamentName(): string
    {
        return \sprintf('%s: %s', $this->year, $this->title);
    }
}
