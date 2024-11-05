<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ElectionType;
use Database\Factories\ElectionFactory;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Election extends Model implements HasName, HasAvatar
{
    /** @use HasFactory<ElectionFactory> */
    use HasFactory;

    protected static string $factory = ElectionFactory::class;

    protected $fillable = [
        'title',
        'type',
        'subtitle',
        'slug',
        'year',
        'is_live',
        'properties',
    ];

    protected function casts(): array
    {
        return [
            'type' => ElectionType::class,
            'year' => 'int',
            'is_live' => 'boolean',
            'properties' => 'collection',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('latest', function (Builder $query) {
            return $query
                ->orderByDesc('year')
                ->orderByDesc('is_live');
        });
    }

    public function scheduledJobs(): HasMany
    {
        return $this->hasMany(ScheduledJob::class);
    }

    public function scopeWhereLive(Builder $query): Builder
    {
        return $query->where('is_live', true);
    }

    public function getFilamentName(): string
    {
        return \sprintf('%s: %s', $this->year, $this->title);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->is_live) {
            return 'https://ui-avatars.com/api/?name=LIVE&length=4&background=b91c1c&color=fef2f2&font-size=0.33&bold=true';
        }

        return 'https://ui-avatars.com/api/?name=E';
    }
}
