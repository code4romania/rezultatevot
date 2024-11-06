<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Contracts\HasLabel;

enum DataLevel: string implements HasLabel
{
    use Arrayable;
    use Comparable;

    case TOTAL = 'T';
    case NATIONAL = 'N';
    case DIASPORA = 'D';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::TOTAL => __('app.data_level.total'),
            self::NATIONAL => __('app.data_level.national'),
            self::DIASPORA => __('app.data_level.diaspora'),
        };
    }
}
