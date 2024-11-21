<?php

declare(strict_types=1);

namespace App\Filament\Contributor\Resources\ArticleResource\Pages;

use App\Filament\Contributor\Resources\ArticleResource;
use App\Models\Election;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ListArticles extends ListRecords
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

//    public function getTabs(): array
//    {
//        return auth()->user()->load(['elections' => fn (BelongsToMany $query) => $query->where('is_live', true)])
//            ->elections
//            ->mapWithKeys(fn (Election $election) => [
//                Tab::make($election->date->year . '-' . $election->title)
//                    ->query(fn (Builder $query) => $query->where('election_id', $election->id)),
//
//            ])->push(
//                Tab::make(__('app.all'))
//            )
//            ->all();
//    }
}
