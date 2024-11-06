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

        if (blank($result)) {
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
                    $color = '#FFD700';
                } else {
                    $value = percent($turnout->total, $turnout->initial_total, formatted: true);
                    $color = $this->getColor($turnout->total, $turnout->initial_total);
                }

                return [
                    $turnout->place => [
                        'value' => $value,
                        'color' => $color,
                    ],
                ];
            });
    }

    protected function getColor(int|float|string $value, int|float|string $max): string
    {
        $colors = ['#A50026', '#DA372A', '#F67B4A', '#FDBF6F', '#FEEEA2', '#EAF6A2', '#B7E075', '#74C365', '#229C52', '#006837'];
        $percent = percent($value, $max);

        return match (true) {
            $percent > 90 => $colors[9],
            $percent > 80 => $colors[8],
            $percent > 70 => $colors[7],
            $percent > 60 => $colors[6],
            $percent > 50 => $colors[5],
            $percent > 40 => $colors[4],
            $percent > 30 => $colors[3],
            $percent > 20 => $colors[2],
            $percent > 10 => $colors[1],
            $percent > 0 => $colors[0],
            default => '#DDD',
        };
    }
}
