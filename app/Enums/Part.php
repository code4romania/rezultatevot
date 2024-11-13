<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Contracts\HasLabel;

enum Part: int implements HasLabel
{
    use Arrayable;
    use Comparable;

    case PROV = 0;
    case PART = 1;
    case FINAL = 2;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PROV => __('app.part.prov'),
            self::PART => __('app.part.part'),
            self::FINAL => __('app.part.final'),
        };
    }
}
