<?php

declare(strict_types=1);

use App\Livewire\Pages\ElectionResults;
use App\Livewire\Pages\ElectionTurnouts;
use Illuminate\Support\Facades\Route;

Route::as('front.')->group(function () {
    Route::get('/alegeri/{election:slug}/prezenta', ElectionTurnouts::class)->name('elections.turnout');
    Route::get('/alegeri/{election:slug}/rezultate', ElectionResults::class)->name('elections.results');

    Route::view('/', 'welcome')->name('index');
});
