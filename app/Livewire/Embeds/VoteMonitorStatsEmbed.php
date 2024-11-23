<?php

declare(strict_types=1);

namespace App\Livewire\Embeds;

use App\Models\Election;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class VoteMonitorStatsEmbed extends Component
{
    public Election $election;

    #[Layout('components.layouts.embed')]
    public function render(): View
    {
        return view('livewire.embeds.vote-monitor-stats');
    }
}
