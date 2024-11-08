<?php

namespace App\Filament\Admin\Resources\CandidateResource\Pages;

use App\Filament\Admin\Resources\CandidateResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCandidates extends ManageRecords
{
    protected static string $resource = CandidateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
