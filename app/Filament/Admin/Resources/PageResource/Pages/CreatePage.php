<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PageResource\Pages;

use App\Filament\Admin\Resources\PageResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;
}
