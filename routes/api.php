<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventController;

Route::prefix('events')->group(function () {
    Route::get('/', [EventController::class, 'index']);
    Route::post('/', [EventController::class, 'store']);
    Route::put('{event}', [EventController::class, 'update']);
    Route::delete('{event}', [EventController::class, 'destroy']);
    Route::post('getEvent', [EventController::class, 'getEvent']);
});
