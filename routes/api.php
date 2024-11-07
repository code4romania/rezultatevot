<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Nomenclature;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::get('/health', function () {
        return response()->json(['status' => 'ok']);
    });
    Route::group(['prefix' => 'nomenclatures'], function (){
       Route::get('elections', [Nomenclature::class, 'elections']);
    });
});
