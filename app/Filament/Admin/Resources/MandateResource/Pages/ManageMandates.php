<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MandateResource\Pages;

use App\Filament\Admin\Resources\MandateResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMandates extends ManageRecords
{
    protected static string $resource = MandateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
