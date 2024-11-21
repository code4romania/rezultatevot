<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToElection;
use App\Concerns\Publishable;
use App\Enums\User\Role;
use Database\Factories\ArticleFactory;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Article extends Model implements HasMedia
{
    use HasFactory;
    use BelongsToElection;
    use InteractsWithMedia;
    use Publishable;

    /** @use HasFactory<ArticleFactory> */
    protected $fillable = [
        'title',
        'author_id',
        'content',
        'slug',
    ];

    protected function casts(): array
    {
        return [
            'embeds' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->slug = Str::slug($model->title);
            if (blank($model->election_id)) {
                $model->election_id = Filament::getTenant()->id;
            }
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class)
            ->where('role', Role::CONTRIBUTOR);
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')
            ->useFallbackUrl(
                \sprintf(
                    'https://ui-avatars.com/api/?%s',
                    Arr::query([
                        'name' => $this->title,
                        'color' => 'FFFFFF',
                        'background' => '09090B',
                    ])
                )
            )
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
}
