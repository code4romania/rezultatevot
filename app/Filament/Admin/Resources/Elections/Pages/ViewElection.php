<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Elections\Pages;

use App\Filament\Admin\Resources\Elections\ElectionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewElection extends ViewRecord
{
    protected static string $resource = ElectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
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
