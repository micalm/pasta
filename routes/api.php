<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PastaShowController;
use App\Http\Controllers\Api\PastaStoreController;
use App\Http\Controllers\Api\PastaDiffController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('/', MetaController::class, 'home');

Route::group(['prefix' => '/p'], function () {
    Route::get('/{uuid}/{key?}', [PastaShowController::class, 'handle']);
    Route::post('/', [PastaStoreController::class, 'handle']);
    Route::get('/diff/{uuid}/{parent}', [PastaDiffController::class, 'handle']);
});
