<?php

declare(strict_types=1);

use App\Http\Controllers\RedirectToElectionController;
use App\Livewire\Embeds;
use App\Livewire\Pages;
use Illuminate\Support\Facades\Route;

Route::as('front.')->group(function () {
    Route::get('/', RedirectToElectionController::class)->name('index');
    Route::get('/alegeri', RedirectToElectionController::class)->name('elections.index');
    Route::get('/alegeri/{election:slug}', RedirectToElectionController::class)->name('elections.show');

    Route::get('/alegeri/{election:slug}/prezenta', Pages\ElectionTurnouts::class)->name('elections.turnout');
    Route::get('/alegeri/{election:slug}/rezultate', Pages\ElectionResults::class)->name('elections.results');

    Route::get('/embed/{election:slug}/prezenta', Embeds\ElectionTurnoutsEmbed::class)->name('elections.embed.turnout');
    Route::get('/embed/{election:slug}/rezultate', Embeds\ElectionResultsEmbed::class)->name('elections.embed.results');
    Route::get('/embed/{election:slug}/observatori', Embeds\VoteMonitorStatsEmbed::class)->name('elections.embed.stats');

    Route::get('{page:slug}', Pages\ContentPage::class)->name('pages.show');
});
