<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\NomenclatureController;
use App\Http\Controllers\Api\V1\TurnoutController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::get('/health', function () {
        return response()->json(['status' => 'ok']);
    });
    Route::group(['prefix' => 'nomenclatures'], function () {
        Route::get('elections', [NomenclatureController::class, 'elections'])
            ->name('elections');

        Route::get('countries', [NomenclatureController::class, 'countries'])
            ->name('countries');

        Route::get('counties', [NomenclatureController::class, 'counties'])
            ->name('counties');

        Route::get('counties/{county}', [NomenclatureController::class, 'county'])
            ->name('county');
    })->name('nomenclatures.');

    Route::group(['prefix' => '{election}'], function () {
        Route::group(['prefix' => 'turnout'], function () {
            Route::get('/', [TurnoutController::class, 'total'])
                ->name('total');

            /*
             * Diaspora turnout
             */
            Route::group(['prefix' => 'diaspora'], function () {
                Route::get('/', [TurnoutController::class, 'diaspora'])
                    ->name('diaspora');
                Route::get('{country}', [TurnoutController::class, 'country'])
                    ->name('country');
            })->name('diaspora');

            Route::group(['prefix' => 'national'], function () {
                Route::get('/', [TurnoutController::class, 'national'])
                    ->name('national');
                Route::get('{county}', [TurnoutController::class, 'county'])
                    ->name('county');
            })->name('national');
        })->name('turnout.');
    })->name('elections.');
});
