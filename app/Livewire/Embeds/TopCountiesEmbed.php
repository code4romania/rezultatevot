<?php

declare(strict_types=1);

namespace App\Livewire\Embeds;

use App\Livewire\Pages\ElectionTurnouts;
use Illuminate\View\View;
use Livewire\Attributes\Layout;

class TopCountiesEmbed extends ElectionTurnouts
{
    #[Layout('components.layouts.embed')]
    public function render(): View
    {
        $this->seo('Topul județelor după prezență');

        return view('livewire.embeds.top-counties');
    }
}
