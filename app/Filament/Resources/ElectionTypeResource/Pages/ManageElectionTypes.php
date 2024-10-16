<?php

declare(strict_types=1);

namespace App\Filament\Resources\ElectionTypeResource\Pages;

use App\Filament\Resources\ElectionTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageElectionTypes extends ManageRecords
{
    protected static string $resource = ElectionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
