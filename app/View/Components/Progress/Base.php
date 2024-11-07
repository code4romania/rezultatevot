<?php

declare(strict_types=1);

namespace App\View\Components\Progress;

use Filament\Support\Colors\Color;
use Illuminate\Support\Number;
use Illuminate\View\Component;

abstract class Base extends Component
{
    public int|float $value;

    public int|float $max;

    public bool $percent;

    public string $color;

    public ?string $text;

    public function __construct(int|float $value, int|float $max = 100, string $color = 'indigo', bool $percent = false, ?string $text = null)
    {
        $this->value = $value;
        $this->percent = $percent;
        $this->max = $max;
        $this->text = $text;

        $this->color = match ($color) {
            'slate' => Color::Slate[500],
            'gray' => Color::Gray[500],
            'zinc' => Color::Zinc[500],
            'neutral' => Color::Neutral[500],
            'stone' => Color::Stone[500],
            'red' => Color::Red[500],
            'orange' => Color::Orange[500],
            'amber' => Color::Amber[500],
            'yellow' => Color::Yellow[500],
            'lime' => Color::Lime[500],
            'green' => Color::Green[500],
            'emerald' => Color::Emerald[500],
            'teal' => Color::Teal[500],
            'cyan' => Color::Cyan[500],
            'sky' => Color::Sky[500],
            'blue' => Color::Blue[500],
            'indigo' => Color::Indigo[500],
            'violet' => Color::Violet[500],
            'purple' => Color::Purple[500],
            'fuchsia' => Color::Fuchsia[500],
            'pink' => Color::Pink[500],
            'rose' => Color::Rose[500],
        };
    }

    public function label(): ?string
    {
        if ($this->percent) {
            return percent($this->value, $this->max, formatted: true);
        }

        return Number::format($this->value);
    }
}
