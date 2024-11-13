<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class StackedBar extends Component
{
    public Collection $items;

    public int $maxItems;

    public string $fallbackColor;

    public bool $showThreshold;

    public function __construct(Collection $items, int $maxItems = 4, string $fallbackColor = '#DDD', bool $showThreshold = false)
    {
        $this->maxItems = $maxItems;
        $this->fallbackColor = $fallbackColor;
        $this->showThreshold = $showThreshold;
        $this->items = $this->limit($items);
    }

    protected function limit(Collection $items): Collection
    {
        if ($items->count() <= $this->maxItems) {
            return $items;
        }

        $items = $items->take($this->maxItems);

        $items->push([
            'name' => 'Other',
            'percent' => 100 - $items->sum('percent'),
            'color' => $this->fallbackColor,
        ]);

        return $items;
    }

    public function render(): View
    {
        return view('components.stacked-bar');
    }
}
