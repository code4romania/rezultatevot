<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\LocalityResource\Pages;

use App\Filament\Admin\Resources\LocalityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageLocalities extends ManageRecords
{
    protected static string $resource = LocalityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
