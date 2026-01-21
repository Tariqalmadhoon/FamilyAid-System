<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SecurityQuestionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Security Question Password Reset (instead of email-based)
    Route::get('forgot-password', [SecurityQuestionController::class, 'showForm'])
                ->name('password.security.request');

    Route::post('forgot-password/verify-id', [SecurityQuestionController::class, 'verifyNationalId'])
                ->name('password.security.verify-id');

    Route::post('forgot-password/verify-answer', [SecurityQuestionController::class, 'verifyAnswer'])
                ->name('password.security.verify-answer');

    Route::post('forgot-password/reset', [SecurityQuestionController::class, 'resetPassword'])
                ->name('password.security.update');
});

Route::middleware('auth')->group(function () {
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});
