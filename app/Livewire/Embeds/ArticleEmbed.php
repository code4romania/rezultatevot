<?php

declare(strict_types=1);

namespace App\Livewire\Embeds;

use App\Models\Article;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ArticleEmbed extends Component
{
    public Article $article;

    #[Layout('components.layouts.embed')]
    public function render(): View
    {
        seo()
            ->title($this->article->title);

        return view('livewire.embeds.article');
    }
}
