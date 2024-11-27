<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\ClearsCache;
use Datlechin\FilamentMenuBuilder\Models\MenuLocation as BaseMenuLocation;

class MenuLocation extends BaseMenuLocation
{
    use ClearsCache;

    public function getCacheTags(): array
    {
        return ['menus'];
    }
}
