<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Candidates\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\Candidates\CandidateResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCandidates extends ManageRecords
{
    protected static string $resource = CandidateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
