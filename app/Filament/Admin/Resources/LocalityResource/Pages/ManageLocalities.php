<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\LocalityResource\Pages;

use App\Filament\Admin\Resources\LocalityResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageLocalities extends ManageRecords
{
    protected static string $resource = LocalityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
