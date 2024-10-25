<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::as('front.')->group(function () {
    Route::view('/', 'welcome')->name('index');
});
