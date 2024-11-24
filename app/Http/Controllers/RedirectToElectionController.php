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
            ->where('is_visible', true)
            ->orderBy('type')
            ->latest()
            ->first();

        if (blank($election)) {
            abort(404);
        }

        return redirect()->to($election->getDefaultUrl());
    }
}
