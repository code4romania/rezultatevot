<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\NomenclatureController;
use App\Http\Controllers\Api\V1\ResultsController;
use App\Http\Controllers\Api\V1\TurnoutController;
use Illuminate\Support\Facades\Route;

Route::group([
    'as' => 'api.v1.',
    'prefix' => 'v1',
], function () {
    Route::group([
        'as' => 'nomenclatures.',
        'prefix' => 'nomenclatures',
        'controller' => NomenclatureController::class,
    ], function () {
        Route::get('elections', 'elections')->name('elections');
        Route::get('countries', 'countries')->name('countries');
        Route::get('counties', 'counties')->name('counties');
        Route::get('counties/{county}', 'county')->name('county');
    });

    Route::group([
        'as' => 'elections.',
        'prefix' => '{election}',
    ], function () {
        Route::group([
            'as' => 'turnout.',
            'prefix' => 'turnout',
            'controller' => TurnoutController::class,
        ], function () {
            Route::get('/', 'total')->name('total');

            Route::get('/diaspora', 'diaspora')->name('diaspora');
            Route::get('/diaspora/{country}', 'country')->name('diaspora.country');

            Route::get('/national', 'national')->name('national');
            Route::get('/national/{county}', 'county')->name('national.county');
        });

        Route::group([
            'as' => 'result.',
            'prefix' => 'result',
            'controller' => ResultsController::class,
        ], function () {
            Route::get('/', 'total')->name('total');

            Route::get('/diaspora', 'diaspora')->name('diaspora');
            Route::get('/diaspora/{country}', 'country')->name('diaspora.country');

            Route::get('/national', 'national')->name('national');
            Route::get('/national/{county}', 'county')->name('national.county');
            Route::get('/national/{county}/{locality}', 'locality')->name('national.locality');
        });
    });
});
