<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToElection;
use App\Concerns\ClearsCache;
use App\Concerns\Publishable;
use App\Enums\User\Role;
use Database\Factories\ArticleFactory;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Article extends Model implements HasMedia
{
    use ClearsCache;
    use HasFactory;
    use BelongsToElection;
    use InteractsWithMedia;
    use Publishable;

    /** @use HasFactory<ArticleFactory> */
    protected $fillable = [
        'title',
        'author_id',
        'content',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (blank($model->election_id)) {
                $model->election_id = Filament::getTenant()->id;
            }

            if (blank($model->author_id)) {
                $model->author_id = auth()->id();
            }

            if (blank($model->published_at)) {
                $model->published_at = now();
            }
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class)
            ->where('role', Role::CONTRIBUTOR);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')
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

    public function getCacheTags(): array
    {
        return [
            "election:{$this->election_id}:articles",
        ];
    }
}
