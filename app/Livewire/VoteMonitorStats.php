<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Election;
use App\Models\VoteMonitorStat;
use Livewire\Attributes\Computed;
use Livewire\Component;

class VoteMonitorStats extends Component
{
    public Election $election;

    public bool $showEmbed = false;

    #[Computed]
    public function stats(): array
    {
        return VoteMonitorStat::query()
            ->whereBelongsTo($this->election)
            ->where('enabled', true)
            ->orderBy('order')
            ->get()
            ->toArray();
    }

    #[Computed]
    protected function count(): int
    {
        return \count($this->stats);
    }

    public function gridColumns(): string
    {
        return match ($this->count()) {
            1 => 'sm:grid-cols-1',
            2, 4 => 'sm:grid-cols-2',
            3 => 'sm:grid-cols-3',
            default => 'sm:grid-cols-6',
        };
    }

    public function render()
    {
        return view('livewire.vote-monitor-stats');
    }
}
