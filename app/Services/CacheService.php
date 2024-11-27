<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DataLevel;
use App\Models\Election;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * TTLs for stale-while-revalidate cache.
     * Fresh for 45s, revalidate up to 24h.
     * @var array
     */
    protected array $ttl = [
        45, 86400,
    ];

    protected array $tags = [];

    protected string $key;

    public function __construct(
        protected array $name,
        protected int|Election $election,
        protected ?DataLevel $level = null,
        protected ?string $country = null,
        protected ?int $county = null,
        protected ?int $locality = null,
        protected bool $aggregate = false,
        protected bool $toBase = false,
        protected array $addSelect = [],
    ) {
        $this->tags = $this->getTags();

        $this->key = $this->getKey();
    }

    public static function make(
        string|array $name,
        int|Election $election,
        ?DataLevel $level = null,
        ?string $country = null,
        ?int $county = null,
        ?int $locality = null,
        bool $aggregate = false,
        bool $toBase = false,
        array $addSelect = [],
    ): static {
        $static = app(static::class, [
            'name' => Arr::wrap($name),
            'election' => $election,
            'level' => $level,
            'country' => $country,
            'county' => $county,
            'locality' => $locality,
            'aggregate' => $aggregate,
            'toBase' => $toBase,
            'addSelect' => $addSelect,
        ]);

        return $static;
    }

    public function setTTL(int $ttl): static
    {
        $this->ttl = $ttl;

        return $this;
    }

    public function remember(Closure $callback): mixed
    {
        return Cache::tags($this->tags)->flexible($this->key, $this->ttl, $callback);
    }

    protected function getElectionId(): int
    {
        if ($this->election instanceof Election) {
            return $this->election->id;
        }

        return $this->election;
    }

    protected function getName(): string
    {
        return implode('-', $this->name);
    }

    protected function getAddSelect(): string
    {
        return implode('-', $this->addSelect);
    }

    protected function getKey(): string
    {
        return collect([
            'election',
            $this->getElectionId(),
            $this->getName(),
            $this->level?->value,
            $this->country,
            $this->county,
            $this->locality,
            $this->aggregate,
            $this->toBase,
            $this->getAddSelect(),
        ])->join(':');
    }

    protected function getTags(): array
    {
        $prefix = "election:{$this->getElectionId()}";

        $tags = [
            $prefix,
        ];

        $parts = [];
        foreach ($this->name as $name) {
            $parts[] = $name;
            $tags[] = $prefix . ':' . implode('-', $parts);
        }

        if (filled($this->level)) {
            $tags[] = "{$prefix}:{$this->getName()}:{$this->level->value}";
        }

        if (DataLevel::isValue($this->level, DataLevel::NATIONAL) && filled($this->county)) {
            $tags[] = "{$prefix}:{$this->getName()}:{$this->level->value}:{$this->county}";
        }

        return $tags;
    }

    public function clear(): bool
    {
        return Cache::tags([
            "election:{$this->getElectionId()}:{$this->getName()}",
        ])->flush();
    }
}
