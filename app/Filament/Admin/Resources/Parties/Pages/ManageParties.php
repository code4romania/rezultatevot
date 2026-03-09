<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Parties\Pages;

use App\Filament\Admin\Resources\Parties\PartyResource;
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
