<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Enums\Time;
use App\Models\Candidate;
use App\Models\Party;
use App\Models\Vote;
use App\Repositories\RecordsRepository;
use App\Repositories\VotesRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use stdClass;

class ElectionResults extends ElectionPage
{
    protected string $fallbackColor = '#DDD';

    #[Layout('components.layouts.election')]
    public function render(): View
    {
        $this->seo(__('app.navigation.results'));

        return view('livewire.pages.election-results');
    }

    #[Computed()]
    public function parties(): Collection
    {
        return Cache::remember("parties-with-votes:{$this->election->id}", Time::DAY_IN_SECONDS, function () {
            return Party::query()
                ->whereBelongsTo($this->election)
                ->whereHas('votes', function (Builder $query) {
                    $query->whereBelongsTo($this->election);
                })
                ->with('media')
                ->get();
        });
    }

    #[Computed()]
    public function candidates(): Collection
    {
        return Cache::remember("candidates-with-votes:{$this->election->id}", Time::DAY_IN_SECONDS, function () {
            return Candidate::query()
                ->whereBelongsTo($this->election)
                ->whereHas('votes', function (Builder $query) {
                    $query->whereBelongsTo($this->election);
                })
                ->with('media')
                ->get();
        });
    }

    #[Computed]
    public function aggregate(): Collection
    {
        $result = VotesRepository::getForLevel(
            election: $this->election,
            level: $this->level,
            country: $this->country,
            county: $this->county,
            locality: $this->locality,
            aggregate: true,
        );

        $total = $result->sum('votes');

        return $result->map(function (Vote $vote) use ($total) {
            $votable = $this->getVotable($vote->votable_type, $vote->votable_id);

            return [
                'name' => $votable->acronym ?? $votable->name,
                'image' => $votable->getFirstMediaUrl(),
                'votes' => ensureNumeric($vote->votes),
                'percent' => percent($vote->votes, $total),
                'color' => hex2rgb($votable->color ?? $this->fallbackColor),
            ];
        });
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
        )->mapWithKeys(function (stdClass $vote) {
            $votable = $this->getVotable($vote->votable_type, $vote->votable_id);

            return [
                $vote->place => [
                    'value' => percent($vote->votes, $vote->total_votes, formatted: true),
                    'color' => $votable->color,
                    'label' => $votable->getDisplayName(),
                ],
            ];
        });
    }

    protected function getVotable(string $type, int $id): Party|Candidate
    {
        return match ($type) {
            (new Party)->getMorphClass() => $this->parties->find($id),
            (new Candidate)->getMorphClass() => $this->candidates->find($id),
        };
    }

    public function getEmbedUrl(): ?string
    {
        return route('front.elections.embed.results', [
            'election' => $this->election,
            ...$this->getQueryParameters(),
        ]);
    }
}
