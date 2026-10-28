<?php

declare(strict_types=1);

namespace App\View\Components\Site;

use App\Models\Menu;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Header extends Component
{
    public Collection $menuItems;

    public bool $timeline;

    public function __construct(bool $timeline = false)
    {
        $this->timeline = $timeline;

        $this->menuItems = Menu::getItems('header');
    }

    public function render(): View
    {
        return view('components.site.header');
    }
}
