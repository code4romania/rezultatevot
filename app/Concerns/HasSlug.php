<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Localizable;

trait HasSlug
{
    use Localizable;

    public function initializeHasSlug(): void
    {
        $this->fillable[] = 'slug';
    }

    public function getSlugFieldSource(): string
    {
        return $this->slugFieldSource ?? 'title';
    }

    public static function bootHasSlug(): void
    {
        static::creating(fn (Model $model) => $model->fillSlugs());
        static::updating(fn (Model $model) => $model->fillSlugs());
    }

    protected function fillSlugs(): void
    {
        if (
            ! \array_key_exists('slug', $this->attributes) ||
            ! \array_key_exists($this->getSlugFieldSource(), $this->attributes)
        ) {
            return;
        }

        $this->slug = Str::slug($this->slug);

        if (! $this->slug || ! $this->slugAlreadyUsed($this->slug)) {
            $this->slug = $this->generateSlug();
        }
    }

    public function generateSlug(): string
    {
        $base = $slug = Str::slug($this->{$this->getSlugFieldSource()});
        $suffix = 1;

        while ($this->slugAlreadyUsed($slug)) {
            $slug = Str::slug($base . '_' . $suffix++);
        }

        return $slug;
    }

    protected function slugAlreadyUsed(string $slug): bool
    {
        $query = static::query()
            ->where('slug', $slug)
            ->withoutGlobalScopes();

        if ($this->exists) {
            $query->where($this->getKeyName(), '!=', $this->getKey());
        }

        return $query->exists();
    }

    public function getUrlAttribute(): ?string
    {
        $key = $this->getMorphClass();

        if (! $this->slug) {
            return null;
        }

        return route('front.' . Str::plural($key) . '.show', [
            $key => $this->slug,
        ]);
    }
}
