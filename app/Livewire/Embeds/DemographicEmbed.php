<?php

declare(strict_types=1);

namespace App\Livewire\Embeds;

use App\Livewire\Pages\ElectionTurnouts;
use Illuminate\View\View;
use Livewire\Attributes\Layout;

class DemographicEmbed extends ElectionTurnouts
{
    #[Layout('components.layouts.embed')]
    public function render(): View
    {
        $this->seo(
            __('app.navigation.turnout') . ' Distribuție după gen și vârstă'
        );

        return view('livewire.embeds.demographic');
    }
}
