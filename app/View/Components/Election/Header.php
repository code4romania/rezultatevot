<?php

declare(strict_types=1);

namespace App\View\Components\Election;

use App\Models\Election;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Header extends Component
{
    public Election $election;

    public string $page;

    public function __construct(Election $election, string $page)
    {
        $this->election = $election;

        $this->page = $page;
    }

    public function render(): View
    {
        return view('components.election.header');
    }
}
