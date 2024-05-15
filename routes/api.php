<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/users/auth/signup', [AuthController::class, 'signup']);
Route::post('/users/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users/auth/current', [AuthController::class, 'getUser']);
    Route::post('/users/auth/logout', [AuthController::class, 'logout']);
});
