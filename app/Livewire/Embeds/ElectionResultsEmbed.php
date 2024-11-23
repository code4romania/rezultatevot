<?php

declare(strict_types=1);

namespace App\Livewire\Embeds;

use App\Livewire\Pages\ElectionResults;
use Illuminate\View\View;
use Livewire\Attributes\Layout;

class ElectionResultsEmbed extends ElectionResults
{
    protected string $fallbackColor = '#DDD';

    #[Layout('components.layouts.embed')]
    public function render(): View
    {
        $this->seo(__('app.navigation.results'));

        return view('livewire.embeds.election-results');
    }
}
