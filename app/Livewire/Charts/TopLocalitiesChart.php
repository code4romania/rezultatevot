<?php

declare(strict_types=1);

namespace App\Livewire\Charts;

use App\Enums\DataLevel;
use App\Models\Election;
use App\Repositories\TurnoutRepository;
use Filament\Support\Colors\Color;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class TopLocalitiesChart extends ChartWidget
{
    public Election $election;

    public ?Collection $topLocalities = null;

    protected static ?string $maxHeight = '1200px';

    protected static ?string $pollingInterval = null;

    public function getHeading(): Htmlable
    {
        return new HtmlString(view('components.chart-heading', [
            'title' => 'Top 10 mari orașe din România',
            'url' => route('front.elections.embed.top-localities', [
                'election' => $this->election,
            ]),
        ])->render());
    }

    protected function getTopLocalities(): Collection
    {
        $localities = collect([
            54975 => 'Cluj-Napoca',
            95060 => 'Iași',
            60419 => 'Constanța',
            155243 => 'Timișoara',
            40198 => 'Brașov',
            69900 => 'Craiova',
            75098 => 'Galați',
            26564 => 'Oradea',
            130534 => 'Ploiești',
        ]);

        if (blank($this->topLocalities)) {
            $this->topLocalities = $localities
                ->map(fn (string $name, int $locality) => TurnoutRepository::getForLevel(
                    election: $this->election,
                    level: DataLevel::NATIONAL,
                    locality: $locality,
                    toBase: true,
                    aggregate: true,
                ))
                ->prepend(TurnoutRepository::getForLevel(
                    election: $this->election,
                    level: DataLevel::NATIONAL,
                    county: 403, // București
                    toBase: true,
                    aggregate: true,
                ))
                ->map(function (object $turnout) use ($localities) {
                    $turnout->name = $localities->get($turnout->place, 'București');
                    $turnout->percent = percent($turnout->total, $turnout->initial_total);

                    return $turnout;
                })
                ->sortByDesc('percent');
        }

        return $this->topLocalities;
    }

    protected function getData(): array
    {
        $result = $this->getTopLocalities();

        $labels = [];
        $data = [];
        $backgroundColor = [];

        foreach ($result as $county) {
            $labels[] = $county->name;

            $backgroundColor[] = 'rgb(' . Color::Blue[400] . ')';
            $data[] = percent($county->total, $county->initial_total);
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Prezența',
                    'borderWidth' => 0,
                    'backgroundColor' => $backgroundColor,
                    'data' => $data,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): RawJs
    {
        if ($this->getTopLocalities()->isEmpty()) {
            return RawJs::make(<<<'JS'
                {
                    indexAxis: 'y',
                    scales: {
                        x: {
                            ticks: {
                                display: false
                            },
                        },
                    },
                    events: []
                }
            JS);
        }

        return RawJs::make(<<<'JS'
            {
                indexAxis: 'y',
                scales: {
                    y: {
                        beginAtZero: true,
                        stacked: true,
                    },
                    x: {
                        beginAtZero: true,
                    },
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                let label =  context.dataset.label;

                                if (label) {
                                    label += ': ';
                                }

                                if (context.parsed.x !== null) {
                                    label += Math.abs(context.parsed.x) + ' %';
                                }
                                return label;
                            },
                        },
                    },
                }
            }
        JS);
    }
}
