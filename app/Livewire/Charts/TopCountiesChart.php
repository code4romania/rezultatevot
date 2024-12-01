<?php

declare(strict_types=1);

namespace App\Livewire\Charts;

use App\Enums\DataLevel;
use App\Models\County;
use App\Models\Election;
use App\Repositories\TurnoutRepository;
use Filament\Support\Colors\Color;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class TopCountiesChart extends ChartWidget
{
    public Election $election;

    public ?Collection $topCounties = null;

    protected static ?string $maxHeight = '1200px';

    protected static ?string $pollingInterval = null;

    public function getHeading(): Htmlable
    {
        return new HtmlString(view('components.chart-heading', [
            'title' => 'Topul județelor după prezență',
            'url' => route('front.elections.embed.top-counties', [
                'election' => $this->election,
            ]),
        ])->render());
    }

    protected function getTopCounties(): Collection
    {
        if (blank($this->topCounties)) {
            $counties = County::pluck('name', 'id');

            $this->topCounties = TurnoutRepository::getForLevel(
                election: $this->election,
                level: DataLevel::NATIONAL,
                toBase: true,
            )
                ->map(function (object $turnout) use ($counties) {
                    $turnout->name = $counties->get($turnout->place);
                    $turnout->percent = percent($turnout->total, $turnout->initial_total);

                    return $turnout;
                })
                ->sortByDesc('percent');
        }

        return $this->topCounties;
    }

    protected function getData(): array
    {
        $result = $this->getTopCounties();

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
        if ($this->getTopCounties()->isEmpty()) {
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
                aspectRatio: 0.15,
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
