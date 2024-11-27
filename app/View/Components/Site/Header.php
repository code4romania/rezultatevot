<?php

declare(strict_types=1);

namespace App\View\Components\Site;

use App\Models\Menu;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class Header extends Component
{
    public Collection $menuItems;

    public bool $timeline;

    public function __construct(bool $timeline = false)
    {
        $this->timeline = $timeline;

        $this->menuItems = $this->getMenuItems();
    }

    protected function getMenuItems(): Collection
    {
        return Cache::tags('menus')
            ->rememberForever('header-menu', fn () => Menu::location('header')?->menuItems);
    }

    public function render(): View
    {
        return view('components.site.header');
    }
}
