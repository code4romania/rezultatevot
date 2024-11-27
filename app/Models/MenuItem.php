<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\ClearsCache;
use Datlechin\FilamentMenuBuilder\Models\MenuItem as BaseMenuItem;

class MenuItem extends BaseMenuItem
{
    use ClearsCache;

    public function getCacheTags(): array
    {
        return ['menus'];
    }
}
