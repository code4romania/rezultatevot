<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Models\Election;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Timeline extends Component
{
    public Collection $years;

    /**
     * Currently selected election.
     */
    public ?Election $election = null;

    public function __construct()
    {
        $this->years = Election::query()
            ->get()
            ->groupBy([
                'year',
                fn (Election $election) => $election->type->getLabel(),
            ]);

        $this->election = request()->election;
    }

    public function render(): View
    {
        return view('components.timeline.index');
    }

    public function isActiveYear(int $year): bool
    {
        return $this->election?->year === $year;
    }

    public function isActiveElectionType(string $type): bool
    {
        return $this->election?->type->name === $type;
    }

    public function isActiveElection(Election $election): bool
    {
        return $this->election?->is($election);
    }

    public function isLiveElectionGroup(Collection $elections): bool
    {
        return $elections->contains(
            fn (Election $election) => $election->is_live
        );
    }
}
