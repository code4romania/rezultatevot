<?php

declare(strict_types=1);

namespace App\View\Components\Election;

use App\Models\Election;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Alert extends Component
{
    public ?string $alert = null;

    public function __construct(Election $election)
    {
        $this->alert = data_get($election, 'properties.alert');
    }

    public function shouldRender(): bool
    {
        return filled($this->alert);
    }

    public function render(): View
    {
        return view('components.election.alert');
    }
}
