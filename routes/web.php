<?php

declare(strict_types=1);

use App\Http\Controllers\RedirectToElectionController;
use App\Livewire\Pages\ElectionResults;
use App\Livewire\Pages\ElectionTurnouts;
use Illuminate\Support\Facades\Route;

Route::as('front.')->group(function () {
    Route::get('/', RedirectToElectionController::class)->name('index');
    Route::get('/alegeri', RedirectToElectionController::class)->name('elections.index');
    Route::get('/alegeri/{election:slug}', RedirectToElectionController::class)->name('elections.show');

    Route::get('/alegeri/{election:slug}/prezenta', ElectionTurnouts::class)->name('elections.turnout');
    Route::get('/alegeri/{election:slug}/rezultate', ElectionResults::class)->name('elections.results');
});
