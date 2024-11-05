<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Election;
use Illuminate\Http\RedirectResponse;

class RedirectToElectionController extends Controller
{
    public function __invoke(?Election $election = null): RedirectResponse
    {
        $election ??= Election::latest()->first();

        $route = $election->properties?->get('default_route') === 'results'
            ? 'front.elections.results'
            : 'front.elections.turnout';

        return redirect()->route($route, $election);
    }
}
