<?php

declare(strict_types=1);

namespace App\Filament\Resources\ElectionResource\Pages;

use App\Filament\Resources\ElectionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateElection extends CreateRecord
{
    protected static string $resource = ElectionResource::class;
}
