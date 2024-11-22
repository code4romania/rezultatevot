<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Attributes\Url;
use Livewire\Component;

class EmbedButton extends Component
{
    public string $url;

    public array $queryParams=[];


    public function mount(): void
    {
        $this->queryParams = request()->query();
    }

    public function render()
    {

        return view('livewire.embed-button');
    }
}
