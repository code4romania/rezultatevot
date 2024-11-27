<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToElection;
use App\Concerns\ClearsCache;
use App\Contracts\HasDisplayName;
use Database\Factories\CandidateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Candidate extends Model implements HasMedia, HasDisplayName
{
    use BelongsToElection;
    use ClearsCache;
    /** @use HasFactory<CandidateFactory> */
    use HasFactory;
    use InteractsWithMedia;

    protected static string $factory = CandidateFactory::class;

    protected $fillable = [
        'name',
        'display_name',
        'color',
        'election_id',
        'party_id',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')
            ->useFallbackUrl(
                \sprintf(
                    'https://ui-avatars.com/api/?%s',
                    Arr::query([
                        'name' => $this->name,
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

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    public function getDisplayName(): string
    {
        return $this->display_name ?? $this->name;
    }

    public function getCacheTags(): array
    {
        return [
            "candidates:{$this->election_id}",
        ];
    }
}
