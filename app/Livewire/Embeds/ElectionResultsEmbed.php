<?php

declare(strict_types=1);

namespace App\Livewire\Embeds;

use App\Livewire\Pages\ElectionResults;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Illuminate\View\View;
use Livewire\Attributes\Layout;

class ElectionResultsEmbed extends ElectionResults implements HasActions
{
    use InteractsWithActions;

    protected string $fallbackColor = '#DDD';

    #[Layout('components.layouts.embed')]
    public function render(): View
    {
        $this->seo(__('app.navigation.results'));

        return view('livewire.embeds.election-results');
    }
}
