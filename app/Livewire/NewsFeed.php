<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\User\Role;
use App\Models\Article;
use App\Models\Election;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class NewsFeed extends Component implements HasForms
{
    use InteractsWithForms;
    use WithPagination;

    public ?array $filters = [];

    public ?int $electionId = 82;

    protected $listeners = [
        'reload' => 'reload',
    ];

    public function mount(Election $election): void
    {
        $this->electionId = $election->id;
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Select::make('author')
                    ->label(__('app.newsfeed.filters.author'))
                    ->options(
                        User::query()
                            ->where('role', Role::CONTRIBUTOR)
                            ->whereHas('articles', fn (Builder $query) => $query->where('election_id', $this->electionId))
                            ->pluck('name', 'id')
                    )
                    ->multiple()
                    ->lazy(),

            ])
            ->statePath('filters');
    }

    public function reload(): void
    {
        $this->reset('filters');
        $this->resetPage();
        $this->dispatch('$refresh');
    }

    #[Computed]
    protected function posts(): LengthAwarePaginator
    {
        return Article::query()
            ->with('author', 'media')
            ->when(data_get($this->filters, 'author'), fn (Builder $query, array $authors) => $query->whereIn('author_id', $authors))
            ->onlyPublished()
            ->orderByDesc('published_at')
            ->paginate();
    }
}
