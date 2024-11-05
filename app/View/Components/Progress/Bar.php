<?php

declare(strict_types=1);

namespace App\View\Components\Progress;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Number;

class Bar extends Base
{
    public function render(): View
    {
        return view('components.progress.bar');
    }

    public function label(): string
    {
        if ($this->percent) {
            return Number::percentage($this->percent(), 2);
        }

        return Number::format($this->value);
    }

    public function percent(): float
    {
        return min(100, max(0, $this->value / $this->max * 100));
    }
}
