<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminAuthController;

/**
 * User Authentication Routes (Worker / Employer)
 */
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::post('/email/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/email/resend', [AuthController::class, 'resendVerification']);

    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    Route::middleware('jwt.auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });
});

/**
 * Admin Authentication Routes (Super Admin, Moderator, Finance)
 */
Route::prefix('admin')->group(function () {
    // Public routes
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/refresh-token', [AdminAuthController::class, 'refreshToken']);

    // Protected routes
    Route::middleware('auth:admin')->group(function () {
        Route::get('/me', [AdminAuthController::class, 'me']);
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::post('/logout-all', [AdminAuthController::class, 'logoutAll']);

        Route::get('/sessions', [AdminAuthController::class, 'getSessions']);
        Route::delete('/sessions/{id}', [AdminAuthController::class, 'revokeSession']);
    });
});
