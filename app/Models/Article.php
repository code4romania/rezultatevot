<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToElection;
use App\Enums\User\Role;
use Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Article extends Model implements HasMedia
{
    use HasFactory;
    use BelongsToElection;
    use InteractsWithMedia;

    /** @use HasFactory<ArticleFactory> */
    protected $fillable = [
        'title',
        'user_id',
        'content',
        'slug',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->slug = Str::slug($model->title);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)
            ->where('role', Role::CONTRIBUTOR);
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }
}
