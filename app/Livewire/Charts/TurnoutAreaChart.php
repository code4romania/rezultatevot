<?php

declare(strict_types=1);

namespace App\Livewire\Charts;

use Filament\Support\Colors\Color;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Collection;

class TurnoutAreaChart extends ChartWidget
{
    public Collection $areas;

    public function getHeading(): string
    {
        return 'Distribuție după mediu';
    }

    protected function getData(): array
    {
        $labels = [];
        $data = [];
        $backgroundColor = [];

        $total = $this->areas->sum('value');

        if (! $total) {
            return [
                'labels' => ['Fără date disponibile'],
                'datasets' => [
                    [
                        'data' => [100],
                        'backgroundColor' => ['rgb(' . Color::Gray[400] . ')'],
                    ],
                ],
            ];
        }

        foreach ($this->areas as $item) {
            $labels[] = $item['area']->getLabel() . ' (' . percent($item['value'], $total, formatted: true) . ')';
            $backgroundColor[] = "rgb({$item['area']->getColor()})";
            $data[] = $item['value'];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColor,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        $options = [
            'scales' => [
                'y' => [
                    'ticks' => [
                        'display' => false,
                    ],
                    'grid' => [
                        'display' => false,
                    ],
                ],

                'x' => [
                    'ticks' => [
                        'display' => false,
                    ],
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
        ];

        if (! $this->areas->sum('value')) {
            $options['events'] = [];
        }

        return $options;
    }
}
