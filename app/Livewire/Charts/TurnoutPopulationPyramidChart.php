<?php

declare(strict_types=1);

namespace App\Livewire\Charts;

use Filament\Support\Colors\Color;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Collection;

class TurnoutPopulationPyramidChart extends ChartWidget
{
    public Collection $demographics;

    public int $total = 0;

    protected int | string | array $columnSpan = [
        'default' => 1,
        'xl' => 2,
    ];

    protected static ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        return 'Distribuție după gen și vârstă';
    }

    protected function getData(): array
    {
        $labels = collect([
            '65+',
            '45-64',
            '35-44',
            '25-34',
            '18-24',
        ]);

        $menTotal = collect($this->demographics->get('men'))->sum();
        $womenTotal = collect($this->demographics->get('women'))->sum();
        $this->total = $menTotal + $womenTotal;

        if (! $this->total) {
            return [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => ['Fără date disponibile'],
                        'borderWidth' => 0,
                        'backgroundColor' => ['rgb(' . Color::Gray[400] . ')'],
                    ],
                ],
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => \sprintf(
                        'Masculin (%s)',
                        percent($menTotal, $this->total, formatted: true)
                    ),
                    'stack' => 'Stack 0',
                    'borderWidth' => 0,
                    'backgroundColor' => '#60A5FA',
                    'data' => $labels
                        ->map(fn ($key) => data_get($this->demographics, "men.{$key}", 0))
                        ->map(fn ($v) => -$v),
                ],
                [
                    'label' => \sprintf(
                        'Feminin (%s)',
                        percent($womenTotal, $this->total, formatted: true)
                    ),
                    'stack' => 'Stack 0',
                    'borderWidth' => 0,
                    'backgroundColor' => '#F472B6',
                    'data' => $labels
                        ->map(fn ($key) => data_get($this->demographics, "women.{$key}", 0)),
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
        if (! $this->total) {
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
                interaction: {
                    mode: 'index',
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        stacked: true,
                    },
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => Math.abs(value)
                        },
                    },
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                let label =  context.dataset.label.split(' ')[0];

                                if (label) {
                                    label += ': ';
                                }

                                if (context.parsed.x !== null) {
                                    label += Math.abs(context.parsed.x);
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
