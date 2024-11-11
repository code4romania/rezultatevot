<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Models\Vote;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

class ElectionResults extends ElectionPage
{
    #[Layout('components.layouts.election')]
    public function render()
    {
        return view('livewire.pages.election-results');
    }

    #[Computed]
    public function data(): Collection
    {
        return Vote::query()
            ->whereBelongsTo($this->election)
            ->forLevel(
                level: $this->level,
                country: null,
                county: $this->county,
                locality: null,
            )
            ->with('votable')
            ->get()
            ->mapWithKeys(function (Vote $vote) {
                return [
                    $vote->place => [
                        'value' => Number::format(ensureNumeric($vote->votes)),
                        'color' => $vote->votable->color,
                    ],
                ];
            });
    }
}
