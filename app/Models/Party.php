<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToElection;
use App\Concerns\ClearsCache;
use App\Contracts\HasDisplayName;
use Database\Factories\PartyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Party extends Model implements HasMedia, HasDisplayName
{
    use BelongsToElection;
    use ClearsCache;
    /** @use HasFactory<PartyFactory> */
    use HasFactory;
    use InteractsWithMedia;

    protected static string $factory = PartyFactory::class;

    protected $fillable = [
        'name',
        'acronym',
        'color',
    ];

    public static function booted(): void
    {
        static::creating(function (Party $party) {
            if (blank($party->acronym)) {
                $party->acronym = Str::initials($party->name);
            }
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')
            ->useFallbackUrl(
                \sprintf(
                    'https://ui-avatars.com/api/?%s',
                    Arr::query([
                        'name' => $this->acronym,
                        'color' => 'FFFFFF',
                        'background' => '09090B',
                    ])
                )
            )
            ->singleFile()
            ->registerMediaConversions(function () {
                $this->addMediaConversion('thumb')
                    ->fit(Fit::Crop, 64, 64)
                    ->keepOriginalImageFormat()
                    ->optimize();

                $this->addMediaConversion('large')
                    ->fit(Fit::Crop, 256, 256)
                    ->keepOriginalImageFormat()
                    ->optimize();
            });
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    public function getDisplayName(): string
    {
        return $this->name;
    }

    public function getCacheTags(): array
    {
        return [
            "parties:{$this->election_id}",
            "candidates:{$this->election_id}",
        ];
    }
}
