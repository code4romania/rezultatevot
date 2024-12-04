<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\DataTransferObjects\ProgressData;
use App\Enums\Area;
use App\Enums\DataLevel;
use App\Models\Candidate;
use App\Repositories\TurnoutRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use stdClass;

class ElectionTurnouts extends ElectionPage
{
    #[Layout('components.layouts.election')]
    public function render(): View
    {
        $this->seo(__('app.navigation.turnout'));

        return view('livewire.pages.election-turnouts');
    }

    #[Computed]
    public function candidates(): Collection
    {
        return $this->election->candidates()
            ->with('media', 'party.media')
            ->get()
            ->map(fn (Candidate $candidate) => [
                'name' => $candidate->name,
                'image' => $candidate->getFirstMediaUrl('default', 'thumb'),
                'party' => $candidate->party?->name,
            ]);
    }

    #[Computed]
    public function aggregate(): ?ProgressData
    {
        $result = TurnoutRepository::getForLevel(
            election: $this->election,
            level: $this->level,
            country: $this->country,
            county: $this->county,
            locality: $this->locality,
            aggregate: true,
            toBase: true,
        );

        if (blank($result) || blank($result->total)) {
            return null;
        }

        return new ProgressData(
            value: $result->total,
            max: $result->initial_total,
        );
    }

    #[Computed]
    public function areas(): Collection
    {
        $result = TurnoutRepository::getForLevelAndArea(
            election: $this->election,
            level: $this->level,
            country: $this->country,
            county: $this->county,
            locality: $this->locality,
            aggregate: true,
            toBase: true,
        )->pluck('total', 'area');

        return collect(Area::cases())
            ->map(fn (Area $area) => [
                'area' => $area,
                'value' => (int) $result->get($area->value, 0),
            ]);
    }

    #[Computed]
    public function demographics(): Collection
    {
        $result = TurnoutRepository::getDemographicsForLevel(
            election: $this->election,
            level: $this->level,
            country: $this->country,
            county: $this->county,
            locality: $this->locality,
            aggregate: true,
            toBase: true,
        );

        $demographics = collect();

        collect($result)
            ->each(function ($value, string $key) use ($demographics) {
                $segments = explode('_', $key);

                if (\count($segments) !== 2) {
                    return;
                }

                if ($segments[1] == 65) {
                    $segments[1] .= '+';
                }

                $demographics->put("{$segments[0]}.{$segments[1]}", (int) $value);
            });

        return $demographics->undot();
    }

    #[Computed]
    public function data(): Collection
    {
        return TurnoutRepository::getForLevel(
            election: $this->election,
            level: $this->level,
            county: $this->county,
            toBase: true,
        )->mapWithKeys(function (stdClass $turnout) {
            if ($this->level->is(DataLevel::DIASPORA)) {
                $value = Number::format(ensureNumeric($turnout->total));
                $class = 'fill-purple-900';
            } else {
                $value = percent($turnout->total, $turnout->initial_total, formatted: true);
                $class = $this->getFill($turnout->total, $turnout->initial_total);
            }

            return [
                $turnout->place => [
                    'value' => $value,
                    'class' => $class,
                ],
            ];
        });
    }

    protected function getFill(int|float|string|null $value, int|float|string|null $max): string
    {
        $percent = percent($value, $max);

        return match (true) {
            $percent >= 90 => 'fill-purple-950',
            $percent >= 80 => 'fill-purple-900',
            $percent >= 70 => 'fill-purple-800',
            $percent >= 60 => 'fill-purple-700',
            $percent >= 50 => 'fill-purple-600',
            $percent >= 40 => 'fill-purple-500',
            $percent >= 30 => 'fill-purple-400',
            $percent >= 20 => 'fill-purple-300',
            $percent >= 10 => 'fill-purple-200',
            $percent > 0 => 'fill-purple-100',
            default => 'fill-gray-400',
        };
    }

    public function getLegend(): ?array
    {
        if ($this->level->is(DataLevel::DIASPORA)) {
            return null;
        }

        return [
            [
                'label' => '0–9%',
                'color' => 'bg-purple-100',
            ],
            [
                'label' => '10–19%',
                'color' => 'bg-purple-200',
            ],
            [
                'label' => '20–29%',
                'color' => 'bg-purple-300',
            ],
            [
                'label' => '30–39%',
                'color' => 'bg-purple-400',
            ],
            [
                'label' => '40–49%',
                'color' => 'bg-purple-500',
            ],
            [
                'label' => '50–59%',
                'color' => 'bg-purple-600',
            ],
            [
                'label' => '60–69%',
                'color' => 'bg-purple-700',
            ],
            [
                'label' => '70–79%',
                'color' => 'bg-purple-800',
            ],
            [
                'label' => '80–89%',
                'color' => 'bg-purple-900',
            ],
            [
                'label' => '90%+',
                'color' => 'bg-purple-950',
            ],
        ];
    }

    public function getEmbedUrl(): string
    {
        return route('front.elections.embed.turnout', [
            'election' => $this->election,
            ...$this->getQueryParameters(),
        ]);
    }
}
