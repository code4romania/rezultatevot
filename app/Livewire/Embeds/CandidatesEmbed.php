<?php

declare(strict_types=1);

namespace App\Livewire\Embeds;

use App\Livewire\Pages\ElectionTurnouts;
use Illuminate\View\View;
use Livewire\Attributes\Layout;

class CandidatesEmbed extends ElectionTurnouts
{
    #[Layout('components.layouts.embed')]
    public function render(): View
    {
        $this->seo(
            __('app.navigation.turnout') . ' Candidați'
        );

        return view('livewire.embeds.candidates');
    }
}
