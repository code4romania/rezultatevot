<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Turnouts\Pages;

use App\Filament\Admin\Resources\Turnouts\TurnoutResource;
use Filament\Resources\Pages\ManageRecords;

class ManageTurnouts extends ManageRecords
{
    protected static string $resource = TurnoutResource::class;
}
