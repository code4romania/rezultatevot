<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ArticleResource\Pages;

use App\Filament\Admin\Resources\ArticleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;
}
