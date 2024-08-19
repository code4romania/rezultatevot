<?php

declare(strict_types=1);

namespace App\Filament\Resources\ElectionResource\Pages;

use App\Filament\Resources\ElectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditElection extends EditRecord
{
    protected static string $resource = ElectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
