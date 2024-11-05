<?php

declare(strict_types=1);

namespace App\View\Components\Progress;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Group extends Component
{
    public array $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function render(): View
    {
        return view('components.progress.group');
    }
}
