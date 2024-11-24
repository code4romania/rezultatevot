<?php

declare(strict_types=1);

namespace App\Livewire\Pages;

use Illuminate\Support\Facades\Vite;
use Illuminate\View\View;
use Livewire\Component;

class TestPage extends Component
{
    public function render(): View
    {
        seo()
            ->title('Test page')
            ->image(Vite::asset('resources/images/banner-social.png'));

        return view('livewire.pages.test-page');
    }
}
