<?php

declare(strict_types=1);

namespace App\View\Components\Progress;

use Illuminate\Contracts\View\View;

class Legend extends Base
{
    public function render(): View
    {
        return view('components.progress.legend');
    }
}
