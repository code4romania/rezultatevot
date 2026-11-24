<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\UserResource\Pages;

use Filament\Actions\EditAction;
use App\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
