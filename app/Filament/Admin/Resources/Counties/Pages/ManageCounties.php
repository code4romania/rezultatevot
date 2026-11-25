<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Counties\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\Counties\CountyResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCounties extends ManageRecords
{
    protected static string $resource = CountyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
