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

        Route::get('counties/{county:code}', [NomenclatureController::class, 'county'])
            ->name('county');
    })->name('nomenclatures.');

    Route::group(['prefix' => '{election:slug}'], function () {
        Route::group(['prefix' => 'turnout'], function () {
            Route::get('/', [TurnoutController::class, 'general'])
                ->name('general');

            Route::get('counties', [TurnoutController::class, 'counties'])
                ->name('by_counties');

            Route::get('counties/{county:code}', [TurnoutController::class, 'county'])
                ->name('by_county');

            Route::get('localities/{locality:code}', [TurnoutController::class, 'locality'])
                ->name('by_locality');
        })->name('turnout.');
    })->name('elections.');
});
