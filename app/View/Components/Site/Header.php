<?php

declare(strict_types=1);

namespace App\View\Components\Site;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Header extends Component
{
    public Collection $menuItems;

    public function __construct()
    {
        $this->menuItems = collect([
            'front.index' => __('app.navigation.home'),
        ]);
    }

    public function render(): View
    {
        return view('components.site.header');
    }
}
