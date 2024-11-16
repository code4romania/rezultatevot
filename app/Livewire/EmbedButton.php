<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;

class EmbedButton extends Component
{
    public string $url;

    public function render()
    {
        return view('livewire.embed-button');
    }
}
