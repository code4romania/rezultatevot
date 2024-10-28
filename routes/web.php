<?php

declare(strict_types=1);

use App\Livewire\Pages\ElectionTurnout;
use Illuminate\Support\Facades\Route;

Route::as('front.')->group(function () {
    Route::get('/alegeri/{election:slug}/prezenta', ElectionTurnout::class)->name('elections.turnout');

    Route::view('/', 'welcome')->name('index');
});
