<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Area: string implements HasLabel, HasColor
{
    use Arrayable;
    use Comparable;

    case URBAN = 'U';
    case RURAL = 'R';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::URBAN => __('app.area.urban'),
            self::RURAL => __('app.area.rural'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::URBAN => Color::Emerald[800],
            self::RURAL => Color::Emerald[400],
        };
    }
}
