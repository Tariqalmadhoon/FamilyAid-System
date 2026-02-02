<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\OtpPasswordResetController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // OTP-based password reset
    Route::get('forgot-password', [OtpPasswordResetController::class, 'showRequestForm'])
                ->name('password.otp.request');
    Route::post('forgot-password/send-otp', [OtpPasswordResetController::class, 'sendOtp'])
                ->name('password.otp.send');
    Route::get('forgot-password/verify', [OtpPasswordResetController::class, 'showVerifyForm'])
                ->name('password.otp.verify');
    Route::post('forgot-password/reset', [OtpPasswordResetController::class, 'resetPassword'])
                ->name('password.otp.update');
});

Route::middleware('auth')->group(function () {
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});
