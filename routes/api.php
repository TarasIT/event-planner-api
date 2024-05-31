<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\GoogleAuthController;
use App\Http\Controllers\Api\Event\EventController;
use Illuminate\Support\Facades\Route;

Route::prefix('users/auth')->group(function () {
    Route::post('/signup', [AuthController::class, 'signup']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::prefix('auth/google')->group(function () {
    Route::get('/redirect', [GoogleAuthController::class, 'redirectToGoogle']);
    Route::get('/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('users')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/current', [AuthController::class, 'getUser']);
        Route::delete('/current', [AuthController::class, 'deleteUser']);
    });

    Route::apiResource('events', EventController::class);
    Route::delete('events', [EventController::class, 'destroyAll']);
});
