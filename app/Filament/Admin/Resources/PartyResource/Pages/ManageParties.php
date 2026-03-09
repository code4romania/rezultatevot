<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PartyResource\Pages;

use App\Filament\Admin\Resources\PartyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageParties extends ManageRecords
{
    protected static string $resource = PartyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
