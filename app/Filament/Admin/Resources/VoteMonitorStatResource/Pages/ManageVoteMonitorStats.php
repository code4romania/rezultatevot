<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\VoteMonitorStatResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\VoteMonitorStatResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageVoteMonitorStats extends ManageRecords
{
    protected static string $resource = VoteMonitorStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
