<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RaceController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
});

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/races', [RaceController::class, 'getAll']);
    Route::get('/races/{id}', [RaceController::class, 'get']);
    Route::post('/races', [RaceController::class, 'create']);
    Route::patch('/races/{id}', [RaceController::class, 'update']);
    Route::delete('/races/{id}', [RaceController::class, 'delete']);

    Route::get('/applications', [ApplicationController::class, 'getAll']);
    Route::get('/applications/{id}', [ApplicationController::class, 'get']);
    Route::post('/applications', [ApplicationController::class, 'create']);
    Route::delete('/applications/{id}', [ApplicationController::class, 'delete']);
});
