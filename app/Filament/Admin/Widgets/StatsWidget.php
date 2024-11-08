<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Models\Candidate;
use App\Models\Party;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(__('app.party.label.plural'), Party::count()),
            Stat::make(__('app.candidate.label.plural'), Candidate::count()),
        ];
    }
}
