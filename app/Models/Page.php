<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasSlug;
use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Page extends Model implements HasMedia
{
    /** @use HasFactory<PageFactory> */
    use HasFactory;
    use HasSlug;
    use InteractsWithMedia;

    protected static string $factory = PageFactory::class;

    protected $fillable = [
        'title',
        'slug',
        'content',
    ];
}
