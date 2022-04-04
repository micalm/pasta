<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\PastaCreateController;
use App\Http\Controllers\Web\PastaShowController;
use App\Http\Controllers\Web\PastaStoreController;

Route::get('/', [PastaCreateController::class, 'handle']);
Route::group(['prefix' => '/p'], function () {
    Route::get('/{uuid}/{key?}', [PastaShowController::class, 'handle']);
    Route::post('/', [PastaStoreController::class, 'handle']);
});
