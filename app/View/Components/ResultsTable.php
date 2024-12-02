<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Enums\DataLevel;
use App\Models\Election;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class ResultsTable extends Component
{
    public Election $election;

    public DataLevel $level;

    public Collection $votables;

    public function __construct(Election $election, DataLevel $level, Collection $votables)
    {
        $this->election = $election;
        $this->level = $level;
        $this->votables = $votables;
    }

    public function shouldRender(): bool
    {
        return $this->votables->isNotEmpty();
    }

    public function votablesWithMandates(): Collection
    {
        return $this->votables->where('mandates', '>', 0);
    }

    public function votablesWithoutMandates(): Collection
    {
        return $this->votables->where('mandates', '<=', 0);
    }

    public function render(): View
    {
        if (
            $this->election->has_lists &&
            \array_key_exists('mandates', $this->votables->first())
        ) {
            return view('components.results-tables.lists');
        }

        return view('components.results-tables.simple');
    }
}
