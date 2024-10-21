<?php

declare(strict_types=1);

namespace App\Filament\Resources\CountyResource\Pages;

use App\Filament\Resources\CountyResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCounties extends ManageRecords
{
    protected static string $resource = CountyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
