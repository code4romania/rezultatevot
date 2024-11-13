<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Legend extends Component
{
    public string $label;

    public string $color;

    public ?string $description;

    public ?string $image;

    public function __construct(string $label, string $color, ?string $description = null, ?string $image = null)
    {
        $this->label = $label;
        $this->color = $color;
        $this->description = $description;
        $this->image = $image;
    }

    public function render(): View
    {
        return view('components.legend');
    }
}
