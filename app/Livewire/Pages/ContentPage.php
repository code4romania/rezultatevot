<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use App\Models\Page;
use Livewire\Component;

class ContentPage extends Component
{
    public Page $page;

    public function render()
    {
        seo()
            ->title($this->page->title);

        return view('livewire.pages.content-page');
    }
}