<?php

declare(strict_types=1);

namespace App\Livewire\Charts;

use App\Models\Election;
use Filament\Support\Colors\Color;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class TurnoutAreaChart extends ChartWidget
{
    public Election $election;

    public Collection $areas;

    public array $parameters = [];

    protected static ?string $maxHeight = '300px';

    protected static ?string $pollingInterval = null;

    public function getHeading(): Htmlable
    {
        return new HtmlString(view('components.chart-heading', [
            'title' => 'Distribuție după mediu',
            'url' => route('front.elections.embed.area', [
                'election' => $this->election,
                ...$this->parameters,
            ]),
        ])->render());
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
            $labels[] = \sprintf(
                '%s (%s)',
                $item['area']->getLabel(),
                percent($item['value'], $total, formatted: true)
            );
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
