<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Models\Candidate;
use App\Models\Party;
use App\Models\Vote;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use stdClass;
use Tpetry\QueryExpressions\Function\Aggregate\Max;
use Tpetry\QueryExpressions\Function\Aggregate\Min;
use Tpetry\QueryExpressions\Function\Aggregate\Sum;
use Tpetry\QueryExpressions\Language\Alias;

#[On('map:update')]
class ElectionResults extends ElectionPage
{
    protected string $fallbackColor = '#DDD';

    #[Layout('components.layouts.election')]
    public function render()
    {
        return view('livewire.pages.election-results');
    }

    #[Computed()]
    public function parties(): Collection
    {
        return Party::query()
            ->whereBelongsTo($this->election)
            ->with('media')
            ->get();
    }

    #[Computed()]
    public function candidates(): Collection
    {
        return Candidate::query()
            ->whereBelongsTo($this->election)
            ->with('media')
            ->get();
    }

    #[Computed]
    public function aggregate(): Collection
    {
        $result = Vote::query()
            ->whereBelongsTo($this->election)
            ->forLevel(
                level: $this->level,
                country: $this->country,
                county: $this->county,
                locality: $this->locality,
                aggregate: true,
            )
            ->withVotable()
            ->get();

        $total = $result->sum('votes');

        return $result
            ->map(fn (Vote $vote) => [
                'name' => $vote->votable->acronym ?? $vote->votable->name,
                'votes' => Number::format(ensureNumeric($vote->votes)),
                'percent' => percent($vote->votes, $total),
                'color' => hex2rgb($vote->votable->color ?? $this->fallbackColor),
            ]);
    }

    #[Computed]
    public function data(): Collection
    {
        return DB::query()
            ->select([
                new Alias(DB::raw('ANY_VALUE(votable_id)'), 'votable_id'),
                new Alias(DB::raw('ANY_VALUE(votable_type)'), 'votable_type'),
                new Alias(new Max('votes'), 'votes'),
                new Alias(new Sum('votes'), 'total_votes'),
                new Alias(new Min('part'), 'part'),
                'place',
            ])
            ->groupBy('place')
            ->fromSub(
                Vote::query()
                    ->whereBelongsTo($this->election)
                    ->forLevel(
                        level: $this->level,
                        country: null,
                        county: $this->county,
                        locality: null,
                    )
                    ->toBase(),
                'votes'
            )
            ->get()
            ->mapWithKeys(function (stdClass $vote) {
                $votable = match ($vote->votable_type) {
                    (new Party)->getMorphClass() => $this->parties->find($vote->votable_id),
                    (new Candidate)->getMorphClass() => $this->candidates->find($vote->votable_id),
                };

                return [
                    $vote->place => [
                        'value' => Number::format(ensureNumeric($vote->votes)),
                        'percent' => percent($vote->votes, $vote->total_votes, formatted: true),
                        'color' => $votable->color,
                        'label' => $votable->getDisplayName(),
                    ],
                ];
            });
    }
}
