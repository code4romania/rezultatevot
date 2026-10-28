<?php

declare(strict_types=1);

namespace App\Enums;

use CommitGlobal\Enums\Concerns\Arrayable;
use CommitGlobal\Enums\Concerns\Comparable;
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
