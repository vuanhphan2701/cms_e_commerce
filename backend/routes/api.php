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

        // User Management
        Route::prefix('users')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\AdminUserController::class, 'index']);
            Route::get('/{id}', [\App\Http\Controllers\Api\AdminUserController::class, 'show']);
            Route::post('/{id}/status', [\App\Http\Controllers\Api\AdminUserController::class, 'updateStatus']);
            Route::post('/{id}/verify-kyc', [\App\Http\Controllers\Api\AdminUserController::class, 'verifyKYC']);
            Route::post('/{id}/reset-password', [\App\Http\Controllers\Api\AdminUserController::class, 'resetPassword']);
            Route::get('/{id}/activity', [\App\Http\Controllers\Api\AdminUserController::class, 'getActivityLogs']);
        });
    });
});
