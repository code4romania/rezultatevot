<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Menus;

use Filament\Resources\Resource;

// use Datlechin\FilamentMenuBuilder\Resources\MenuResource as BaseMenuResource;

class MenuResource extends Resource
{
    protected static bool $isScopedToTenant = false;

    protected static ?int $navigationSort = 33;

    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation.admin');
    }
}
