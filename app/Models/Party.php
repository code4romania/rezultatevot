<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PartyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Party extends Model implements HasMedia
{
    /** @use HasFactory<PartyFactory> */
    use HasFactory;
    use InteractsWithMedia;

    protected static string $factory = PartyFactory::class;

    protected $fillable = [
        'name',
        'acronym',
        'color',
        'election_id',
    ];

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
                    ->fit(Fit::Contain, 64, 64)
                    ->keepOriginalImageFormat()
                    ->optimize();

                $this->addMediaConversion('large')
                    ->fit(Fit::Contain, 256, 256)
                    ->keepOriginalImageFormat()
                    ->optimize();
            });
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }
}
