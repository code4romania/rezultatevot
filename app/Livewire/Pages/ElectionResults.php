<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Repositories\RecordsRepository;
use App\Repositories\VotesRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

class ElectionResults extends ElectionPage
{
    protected string $fallbackColor = '#DDD';

    #[Layout('components.layouts.election')]
    public function render(): View
    {
        $this->seo(__('app.navigation.results'));

        return view('livewire.pages.election-results');
    }

    #[Computed]
    public function aggregate(): Collection
    {
        return VotesRepository::getForLevel(
            election: $this->election,
            level: $this->level,
            country: $this->country,
            county: $this->county,
            locality: $this->locality,
            aggregate: true,
        );
    }

    #[Computed]
    public function recordStats(): Collection
    {
        $record = RecordsRepository::getForLevel(
            election: $this->election,
            level: $this->level,
            country: $this->country,
            county: $this->county,
            locality: $this->locality,
            aggregate: true,
            toBase: true,
        );

        return collect($record)
            ->forget('place')
            ->filter(fn (mixed $value) => $value !== null)
            ->mapWithKeys(fn ($value, $key) => [
                __("app.field.$key") => Number::format(ensureNumeric($value)),
            ]);
    }

    #[Computed]
    public function data(): Collection
    {
        return VotesRepository::getMapDataForLevel(
            election: $this->election,
            level: $this->level,
            country: null,
            county: $this->county,
            locality: null,
        );
    }

    public function getEmbedUrl(): ?string
    {
        return route('front.elections.embed.results', [
            'election' => $this->election,
            ...$this->getQueryParameters(),
        ]);
    }
}
