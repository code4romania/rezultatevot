<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Articles\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\Articles\ArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListArticles extends ListRecords
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
