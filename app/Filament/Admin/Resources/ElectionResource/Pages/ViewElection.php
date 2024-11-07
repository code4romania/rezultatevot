<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ElectionResource\Pages;

use App\Filament\Admin\Resources\ElectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewElection extends ViewRecord
{
    protected static string $resource = ElectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return $this->getRecord()->title;
    }

    public function getSubheading(): ?string
    {
        return $this->getRecord()->subtitle;
    }
}
