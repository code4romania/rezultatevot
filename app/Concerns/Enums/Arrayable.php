<?php

declare(strict_types=1);

namespace App\Concerns\Enums;

trait Arrayable
{
    /**
     * @return array<int|string, string>
     */
    public static function names(): array
    {
        return collect(self::cases())
            ->pluck('name')
            ->all();
    }

    /**
     * @return array<int|string, string>
     */
    public static function values(): array
    {
        return collect(self::cases())
            ->pluck('value')
            ->all();
    }

    /**
     * @return array<int|string, string|null>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [
                $case->value => $case->getLabel(),
            ])
            ->all();
    }
}
