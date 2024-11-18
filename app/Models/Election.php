<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasSlug;
use App\Enums\ElectionType;
use Database\Factories\ElectionFactory;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Election extends Model implements HasName, HasAvatar
{
    /** @use HasFactory<ElectionFactory> */
    use HasFactory;
    use HasSlug;

    protected static string $factory = ElectionFactory::class;

    protected $fillable = [
        'title',
        'type',
        'subtitle',
        'slug',
        'date',
        'is_live',
        'properties',
        'old_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => ElectionType::class,
            'date' => 'date',
            'year' => 'int',
            'is_live' => 'boolean',
            'properties' => 'collection',
            'old_id' => 'int',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('latest', function (Builder $query) {
            return $query
                ->orderByDesc('year')
                ->orderByDesc('is_live');
        });

        static::creating(function (self $model) {
            $model->slug = Str::slug("{$model->title}-{$model->date->year}");
        });
    }

    public function scheduledJobs(): HasMany
    {
        return $this->hasMany(ScheduledJob::class);
    }

    public function voteMonitorStats(): HasMany
    {
        return $this->hasMany(VoteMonitorStat::class);
    }

    public function scopeWhereLive(Builder $query): Builder
    {
        return $query->where('is_live', true);
    }

    public function getDefaultUrl(): string
    {
        $name = match ($this->properties?->get('default_tab')) {
            'results' => 'front.elections.results',
            'turnout' => 'front.elections.turnout',
            default => 'front.elections.turnout',
        };

        return route($name, $this);
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
