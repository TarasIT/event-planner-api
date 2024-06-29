<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\GoogleAuthController;
use App\Http\Controllers\Api\EmailVerification\EmailVerificationController;
use App\Http\Controllers\Api\Event\EventController;
use App\Http\Controllers\Api\PasswordReset\PasswordResetController;
use Illuminate\Support\Facades\Route;

Route::prefix('users/auth')->group(function () {
    Route::post('/signup', [AuthController::class, 'signup']);
    Route::post('/login', [AuthController::class, 'login'])->middleware('emailVerified');
});

Route::prefix('auth/google')->group(function () {
    Route::get('/redirect', [GoogleAuthController::class, 'redirectToGoogle']);
    Route::get('/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
});

Route::middleware('guest')->group(function () {
    Route::post(
        '/forgot-password',
        [PasswordResetController::class, 'sendResetPasswordLinkToEmail']
    )->name('password.email');
    Route::post(
        '/reset-password',
        [PasswordResetController::class, 'reset']
    )->name('password.update');
});

Route::group([], function () {
    Route::get(
        '/email/verify/{user_id}',
        [EmailVerificationController::class, 'verify']
    )->name('verification.verify');
    Route::post(
        '/email/resend',
        [EmailVerificationController::class, 'resendEmail']
    )->name('verification.resend');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('users')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/current', [AuthController::class, 'getUser']);
        Route::delete('/current', [AuthController::class, 'deleteUser']);
    });

    Route::apiResource('events', EventController::class)->middleware('pictureDecoded');
    Route::delete('events', [EventController::class, 'destroyAll']);
});
