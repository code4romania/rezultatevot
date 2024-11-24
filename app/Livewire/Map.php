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

    public array $data = [];

    public ?array $legend = null;

    public bool $embed = false;

    public int|float|null $totalValue = null;

    public ?string $totalValueFormatted = null;

    public ?string $totalLabel = null;

    #[Computed]
    public function file(): string
    {
        return match ($this->level) {
            DataLevel::DIASPORA => 'countries',
            DataLevel::TOTAL => 'romania',
            DataLevel::NATIONAL => $this->county ? "localities/{$this->county}" : 'counties',
        };
    }

    #[Computed]
    public function showOverlay(): bool
    {
        if ($this->level->isNot(DataLevel::TOTAL)) {
            return false;
        }

        return filled($this->totalValue) || filled($this->totalValueFormatted) || filled($this->totalLabel);
    }

    public function render()
    {
        return view('livewire.map');
    }
}
