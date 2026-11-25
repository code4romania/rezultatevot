<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Elections\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\Elections\ElectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListElections extends ListRecords
{
    protected static string $resource = ElectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
