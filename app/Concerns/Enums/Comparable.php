<?php

declare(strict_types=1);

namespace App\Concerns\Enums;

use BackedEnum;

trait Comparable
{
    /**
     * Check if this enum matches the given enum instance or value.
     */
    public function is(BackedEnum | string | int | null $enum): bool
    {
        if ($enum instanceof static) {
            return $this->value === $enum->value;
        }

        return $this->value === $enum;
    }

    /**
     * Check if this enum doesn't match the given enum instance or value.
     */
    public function isNot(BackedEnum | string | int | null $enum): bool
    {
        return ! $this->is($enum);
    }

    public static function isValue(BackedEnum | string | int | null $subject, BackedEnum $enum): bool
    {
        if ($subject === null) {
            return false;
        }

        if (! $subject instanceof self) {
            $subject = self::tryFrom(\is_int($enum->value) ? (int) $subject : (string) $subject);
        }

        return $subject?->is($enum) ?? false;
    }
}
