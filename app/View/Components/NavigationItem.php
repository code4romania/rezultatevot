<?php

declare(strict_types=1);

namespace App\View\Components;

use Datlechin\FilamentMenuBuilder\Models\MenuItem;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NavigationItem extends Component
{
    public MenuItem $item;

    public function __construct(MenuItem $item)
    {
        $this->item = $item;
    }

    public function render(): View
    {
        return view('components.navigation-item');
    }
}
