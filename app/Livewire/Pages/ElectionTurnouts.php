<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\DataTransferObjects\ProgressData;
use App\Models\Turnout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Tpetry\QueryExpressions\Function\Aggregate\Sum;
use Tpetry\QueryExpressions\Language\Alias;

class ElectionTurnouts extends ElectionPage
{
    #[Layout('components.layouts.timeline')]
    public function render(): View
    {
        return view('livewire.pages.election-turnouts');
    }

    #[Computed]
    public function aggregate(): ?ProgressData
    {
        $result = Turnout::query()
            ->where('election_id', $this->election->id)
            ->select([
                new Alias(new Sum('initial_total'), 'initial_total'),
                new Alias(new Sum('total'), 'total'),
            ])
            ->when($this->county, function (Builder $query) {
                $query->where('county_id', $this->county)
                    ->groupBy('county_id')
                    ->addSelect('county_id');
            })
            ->toBase()
            ->first();

        if (blank($result)) {
            return null;
        }

        return new ProgressData(
            value: \intval($result->total),
            max: \intval($result->initial_total),
        );
    }

    #[Computed]
    public function data(): Collection
    {
        return Turnout::query()
            ->where('election_id', $this->election->id)
            ->select([
                new Alias(new Sum('initial_total'), 'initial_total'),
                new Alias(new Sum('total'), 'total'),
            ])
            ->when($this->county, function (Builder $query) {
                $query->where('county_id', $this->county)
                    ->groupBy('locality_id')
                    ->addSelect('locality_id');
            })
            ->toBase()
            ->get();
    }
}
