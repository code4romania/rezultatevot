<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ElectionTypeResource\Pages;

use App\Filament\Admin\Resources\ElectionTypeResource;
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
