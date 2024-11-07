<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Resources\ElectionResource;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;

class ElectionSettings extends EditTenantProfile
{
    protected static ?string $slug = 'settings';

    public static function getLabel(): string
    {
        return __('app.election.settings');
    }

    public function form(Form $form): Form
    {
        return ElectionResource::form($form);
    }
}
