<?php

declare(strict_types=1);

if (! function_exists('ensureNumeric')) {
    function ensureNumeric(int|float|string $value): int|float
    {
        if (is_int($value) || is_float($value)) {
            return $value;
        }

        if (! is_numeric($value)) {
            throw new InvalidArgumentException('Value must be a numeric type.');
        }

        if (filter_var($value, \FILTER_VALIDATE_FLOAT) !== false) {
            return floatval($value);
        }

        return intval($value);
    }
}

if (! function_exists('percent')) {
    function percent(int|float|string $value, int|float|string $max, int $precision = 2, bool $formatted = false): float|string|null
    {
        $value = ensureNumeric($value);
        $max = ensureNumeric($max);

        if ($max == 0) {
            return null;
        }

        $percent = (float) number_format(min(100, max(0, $value / $max * 100)), $precision);

        if ($formatted) {
            $percent = sprintf('%s%%', $percent);
        }

        return $percent;
    }
}
