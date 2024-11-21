<?php

declare(strict_types=1);

namespace App\Livewire\Embeds;

use App\Livewire\Pages\ElectionResults;
use Livewire\Attributes\Layout;

class ElectionResultsEmbed extends ElectionResults
{
    protected string $fallbackColor = '#DDD';

    #[Layout('components.layouts.embed')]
    public function render()
    {
        return view('livewire.embeds.election-results');
    }
}
