<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\RecordResource\Pages;

use App\Filament\Admin\Resources\RecordResource;
use Filament\Resources\Pages\ManageRecords as BaseManageRecords;

class ManageRecords extends BaseManageRecords
{
    protected static string $resource = RecordResource::class;
}
