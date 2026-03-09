<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Resources\ElectionResource;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Schema;

class ElectionSettings extends EditTenantProfile
{
    protected static ?string $slug = 'settings';

    public static function getLabel(): string
    {
        return __('app.election.settings');
    }

    public function form(Schema $schema): Schema
    {
        return ElectionResource::form($schema);
    }
}
