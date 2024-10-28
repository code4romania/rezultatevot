<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Models\Election;
use Livewire\Component;

class ElectionTurnout extends Component
{
    public Election $election;

    public function mount(Election $election)
    {
        $this->election = $election;
    }

    public function render()
    {
        return view('livewire.pages.election-turnout');
    }
}
