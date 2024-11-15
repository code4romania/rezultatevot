<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Contracts\HasLabel;

enum DefaultElectionPage: string implements HasLabel
{
    use Arrayable;
    use Comparable;

    case TURNOUT = 'turnout';
    case RESULTS = 'results';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::TURNOUT => __('app.navigation.turnout'),
            self::RESULTS => __('app.navigation.results'),
        };
    }
}
