<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Article;
use App\Models\Election;
use App\Services\CacheService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class NewsFeed extends Component
{
    use WithPagination;

    public Election $election;

    protected $listeners = [
        'reload' => 'reload',
    ];

    public function reload(): void
    {
        $this->resetPage();
        $this->dispatch('$refresh');
    }

    #[Computed]
    protected function articles(): Collection
    {
        return CacheService::make('articles', $this->election)
            ->remember(
                fn () => Article::query()
                    ->whereBelongsTo($this->election)
                    ->with('author.media', 'media')
                    ->onlyPublished()
                    ->orderByDesc('published_at')
                    ->get()
            );
    }
}
