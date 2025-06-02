<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Election;
use Illuminate\Http\RedirectResponse;

class RedirectToElectionController extends Controller
{
    public function __invoke(?Election $election = null): RedirectResponse
    {
        $election ??= Election::query()
            ->withoutGlobalScopes()
            ->where('is_visible', true)
            ->latest('date')
            ->first();

        abort_unless($election, 404);

        return redirect()->to($election->getDefaultUrl());
    }
}
