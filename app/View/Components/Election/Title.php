<?php

declare(strict_types=1);

namespace App\View\Components\Election;

use App\Enums\DataLevel;
use App\Models\Country;
use App\Models\County;
use App\Models\Locality;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class Title extends Component
{
    public string $title;

    public DataLevel $level;

    public ?Country $country = null;

    public ?County $county = null;

    public ?Locality $locality = null;

    public ?string $embedUrl;

    public function __construct(string $title, DataLevel $level, ?string $country = null, ?int $county = null, ?int $locality = null, ?string $embedUrl = null)
    {
        $this->title = $title;

        $this->level = $level;

        $this->country = $this->getCountry($country);

        $this->county = $this->getCounty($county);

        $this->locality = $this->getLocality($locality);

        $this->embedUrl = $embedUrl;
    }

    public function render(): View
    {
        return view('components.election.title');
    }

    public function embedKey(): string
    {
        return hash('xxh128', "embed-{$this->embedUrl}");
    }

    protected function getCountry(?string $id): ?Country
    {
        if (blank($id)) {
            return null;
        }

        return Cache::remember(
            "country-{$id}",
            now()->addDay(),
            fn () => Country::find($id)
        );
    }

    protected function getCounty(?int $id): ?County
    {
        if (blank($id)) {
            return null;
        }

        return Cache::remember(
            "county-{$id}",
            now()->addDay(),
            fn () => County::find($id)
        );
    }

    protected function getLocality(?int $id): ?Locality
    {
        if (blank($id)) {
            return null;
        }

        return Cache::remember(
            "locality-{$id}",
            now()->addDay(),
            fn () => Locality::find($id)
        );
    }
}
