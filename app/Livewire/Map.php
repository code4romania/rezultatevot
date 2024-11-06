<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\DataLevel;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Map extends Component
{
    public DataLevel $level;

    public ?string $country = null;

    public ?string $county = null;

    public ?string $actionUrl = null;

    public array $data = [];

    #[Computed]
    public function file(): string
    {
        return match ($this->level) {
            DataLevel::DIASPORA => 'countries',
            DataLevel::TOTAL => 'counties',
            DataLevel::NATIONAL => $this->county ? "localities/{$this->county}" : 'counties',
        };
    }

    public function render()
    {
        return view('livewire.map');
    }
}
