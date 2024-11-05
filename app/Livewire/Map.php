<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Support\Facades\Vite;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Map extends Component
{
    public ?string $country = null;

    public ?string $county = null;

    #[Computed]
    public function url(): string
    {
        if ($this->country) {
            return Vite::asset('resources/geojson/countries.geojson');
        }

        if ($this->county) {
            return Vite::asset("resources/geojson/localities/{$this->county}.geojson");
        }

        return Vite::asset('resources/geojson/counties.geojson');
    }

    public function render()
    {
        return view('livewire.map');
    }
}
