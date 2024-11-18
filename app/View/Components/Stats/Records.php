<?php

declare(strict_types=1);

namespace App\View\Components\Stats;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Records extends Component
{
    public Collection $stats;

    public function __construct(Collection $stats)
    {
        $this->stats = $stats;
    }

    public function render(): View
    {
        return view('components.stats.records');
    }
}
