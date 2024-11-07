<?php

declare(strict_types=1);

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;

class NavigationItem extends Component
{
    public string $route;

    public string $label;

    public function __construct(string $route, string $label)
    {
        $this->route = $route;
        $this->label = $label;
    }

    public function isCurrent(): bool
    {
        return Route::currentRouteName() === $this->route;
    }

    public function render(): View|Closure|string
    {
        return view('components.navigation-item');
    }
}
