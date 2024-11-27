<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Support\Facades\Cache;

trait ClearsCache
{
    abstract public function getCacheTags(): array;

    public static function bootClearsCache(): void
    {
        self::created(fn (self $model) => self::clearCache($model));
        self::updated(fn (self $model) => self::clearCache($model));
        self::deleted(fn (self $model) => self::clearCache($model));
    }

    private static function clearCache(self $model): void
    {
        Cache::tags($model->getCacheTags())->flush();
    }
}
