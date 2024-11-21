<?php

declare(strict_types=1);

namespace App\View\Components\Election;

use App\Enums\DataLevel;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Title extends Component
{
    public string $title;

    public DataLevel $level;

    public ?string $embedUrl;

    public function __construct(string $title, DataLevel $level, ?string $embedUrl = null)
    {
        $this->title = $title;

        $this->level = $level;

        $this->embedUrl = $embedUrl;
    }

    public function render(): View
    {
        return view('components.election.title');
    }

    public function embedKey(): string
    {
        return hash('xxh128', "embed-{$this->embedUrl}");
    }
}
