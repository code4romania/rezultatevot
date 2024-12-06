<?php

declare(strict_types=1);

namespace App\View\Components\Election;

use App\Models\Election;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class Alert extends Component
{
    public ?HtmlString $alert = null;

    public function __construct(Election $election)
    {
        $this->alert = Str::of(data_get($election, 'properties.alert'))
            ->sanitizeHtml()
            ->toHtmlString();
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
