<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use InvalidArgumentException;

readonly class ProgressData
{
    public int|float $max;

    public int|float $value;

    public function __construct(int|float|string $value, int|float|string $max)
    {
        $this->value = $this->ensureNumeric($value);
        $this->max = $this->ensureNumeric($max);
    }

    public function percent(): float
    {
        return min(100, max(0, $this->value / $this->max * 100));
    }

    protected function ensureNumeric(mixed $value): int|float
    {
        if (\is_int($value) || \is_float($value)) {
            return $value;
        }

        if (! is_numeric($value)) {
            throw new InvalidArgumentException('Value must be a numeric type.');
        }

        if (filter_var($value, \FILTER_VALIDATE_FLOAT) !== false) {
            return \floatval($value);
        }

        return \intval($value);
    }
}
