<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use Livewire\Attributes\Layout;

class ElectionResults extends ElectionPage
{
    #[Layout('components.layouts.election')]
    public function render()
    {
        return view('livewire.pages.election-results');
    }
}
