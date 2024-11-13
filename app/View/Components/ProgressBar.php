<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Number;
use Illuminate\View\Component;

class ProgressBar extends Component
{
    public int|float $value;

    public int|float $max;

    public bool $percent;

    public string $color;

    public ?string $text;

    public function __construct(int|float $value, int|float $max, string $color, bool $percent = false, ?string $text = null)
    {
        $this->value = $value;
        $this->percent = $percent;
        $this->max = $max;
        $this->text = $text;
        $this->color = $color;
    }

    public function percent(): ?float
    {
        return percent($this->value, $this->max);
    }

    public function label(): ?string
    {
        if ($this->percent) {
            return percent($this->value, $this->max, formatted: true);
        }

        return Number::format($this->value);
    }

    public function render(): View
    {
        return view('components.progress-bar');
    }
}
