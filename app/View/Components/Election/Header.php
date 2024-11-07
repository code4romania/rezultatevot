<?php

declare(strict_types=1);

namespace App\View\Components\Election;

use App\Models\Election;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;

class Header extends Component
{
    public Election $election;

    public Collection $items;

    public function __construct(Election $election)
    {
        $this->election = $election;

        $this->items = collect([
            'front.elections.turnout' => __('app.navigation.turnout'),
            'front.elections.results' => __('app.navigation.results'),
        ]);
    }

    public function render(): View
    {
        return view('components.election.header');
    }

    public function isCurrent(string $route): bool
    {
        return Route::currentRouteName() === $route;
    }
}
