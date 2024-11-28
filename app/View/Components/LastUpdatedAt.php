<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Models\Election;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LastUpdatedAt extends Component
{
    public Election $election;

    public ?Carbon $timestamp = null;

    public function __construct(Election $election, string $page)
    {
        $this->election = $election;

        $this->timestamp = match ($page) {
            'turnout' => $election->turnouts_updated_at,
            'results' => $election->records_updated_at,
        };
    }

    public function shouldRender(): bool
    {
        return filled($this->timestamp);
    }

    public function render(): View
    {
        return view('components.last-updated-at');
    }
}
