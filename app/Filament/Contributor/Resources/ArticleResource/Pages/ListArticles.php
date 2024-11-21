<?php

declare(strict_types=1);

namespace App\Filament\Contributor\Resources\ArticleResource\Pages;

use App\Filament\Contributor\Resources\ArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListArticles extends ListRecords
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
