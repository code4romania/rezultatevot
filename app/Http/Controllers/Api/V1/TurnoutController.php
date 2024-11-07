<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Election;

class TurnoutController extends Controller
{
    public function turnout(Election $election)
    {
        dd($election);
    }
}
