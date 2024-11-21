<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\DataTransferObjects\ProgressData;
use App\Enums\DataLevel;
use App\Models\Turnout;
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
        return view('livewire.pages.election-turnouts');
    }

    #[Computed]
    public function aggregate(): ?ProgressData
    {
        $result = Turnout::query()
            ->whereBelongsTo($this->election)
            ->forLevel(
                level: $this->level,
                country: $this->country,
                county: $this->county,
                locality: $this->locality,
                aggregate: true,
            )
            ->toBase()
            ->first();

        if (blank($result) || blank($result->total)) {
            return null;
        }

        return new ProgressData(
            value: $result->total,
            max: $result->initial_total,
        );
    }

    #[Computed]
    public function data(): Collection
    {
        return Turnout::query()
            ->whereBelongsTo($this->election)
            ->forLevel(
                level: $this->level,
                country: null,
                county: $this->county,
                locality: null,
            )
            ->toBase()
            ->get()
            ->mapWithKeys(function (stdClass $turnout) {
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

    protected function getFill(int|float|string $value, int|float|string $max): string
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
