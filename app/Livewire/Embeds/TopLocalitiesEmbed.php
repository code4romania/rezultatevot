<?php

declare(strict_types=1);

namespace App\Livewire\Embeds;

use App\Livewire\Pages\ElectionTurnouts;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Illuminate\View\View;
use Livewire\Attributes\Layout;

class TopLocalitiesEmbed extends ElectionTurnouts implements HasActions
{
    use InteractsWithActions;

    #[Layout('components.layouts.embed')]
    public function render(): View
    {
        $this->seo('Top 10 mari orașe din România');

        return view('livewire.embeds.top-localities');
    }
}
