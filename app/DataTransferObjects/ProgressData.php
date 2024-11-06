<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

readonly class ProgressData
{
    public int|float $max;

    public int|float $value;

    public ?string $color;

    public function __construct(int|float|string $value, int|float|string $max, ?string $color = null)
    {
        $this->value = ensureNumeric($value);
        $this->max = ensureNumeric($max);
        $this->color = $color;
    }

    public function percent(): ?float
    {
        return percent($this->value, $this->max);
    }
}
