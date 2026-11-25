<?php

declare(strict_types=1);

namespace App\Filament\Contributor\Resources\Articles\Pages;

use App\Filament\Contributor\Resources\Articles\ArticleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;
}
