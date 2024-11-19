<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\NomenclatureController;
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
            Route::group([
                'as' => 'diaspora.',
                'prefix' => 'diaspora',
            ], function () {
                Route::get('/', 'diaspora')->name('diaspora');
                Route::get('{country}', 'country')->name('country');
            });

            Route::group([
                'as' => 'national.',
                'prefix' => 'national',
            ], function () {
                Route::get('/', 'national')->name('national');
                Route::get('{county}', 'county')->name('county');
            });
        });
    });
});
