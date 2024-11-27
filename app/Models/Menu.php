<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\ClearsCache;
use Datlechin\FilamentMenuBuilder\Models\Menu as BaseMenu;

class Menu extends BaseMenu
{
    use ClearsCache;

    public function getCacheTags(): array
    {
        return ['menus'];
    }
}
