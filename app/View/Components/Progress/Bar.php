<?php

declare(strict_types=1);

namespace App\View\Components\Progress;

use Illuminate\Contracts\View\View;

class Bar extends Base
{
    public function render(): View
    {
        return view('components.progress.bar');
    }

    public function percent(): ?float
    {
        return percent($this->value, $this->max);
    }
}
