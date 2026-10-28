<?php

declare(strict_types=1);

namespace App\Models;

use Datlechin\FilamentMenuBuilder\Models\Menu as BaseMenu;
use Illuminate\Support\Collection;

class Menu extends BaseMenu
{
    public static function getItems(string $location): Collection
    {
        return self::location($location)?->menuItems ?? collect();
    }
}
