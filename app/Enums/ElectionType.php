<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use Filament\Support\Contracts\HasLabel;

enum ElectionType: string implements HasLabel
{
    use Arrayable;
    use Comparable;

    case PRESIDENTIAL = 'presidential';
    case PARLIAMENTARY = 'parliamentary';
    case EURO = 'euro';
    case LOCAL = 'local';
    case REFERENDUM = 'referendum';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PRESIDENTIAL => __('app.election_type.presidential'),
            self::PARLIAMENTARY => __('app.election_type.parliamentary'),
            self::EURO => __('app.election_type.euro'),
            self::LOCAL => __('app.election_type.local'),
            self::REFERENDUM => __('app.election_type.referendum'),
        };
    }
}
