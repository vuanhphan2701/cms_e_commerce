<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Api\AuthController;

/**
 * Authentication Routes
 */
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('jwt.auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });
});

/**
 * Protected Resource Routes
 */
Route::middleware('jwt.auth')->group(function () {
    /**
     * Product API Routes
     */
    Route::get('/product', [ProductController::class, 'index']);
    Route::get('/product/{id}', [ProductController::class, 'show']);
    Route::post('/product', [ProductController::class, 'store']);
    Route::put('/product/{id}', [ProductController::class, 'update']);
    Route::delete('/product/{id}', [ProductController::class, 'destroy']);

    /**
     * Category API Routes
     */
    Route::get('/category', [CategoryController::class, 'index']);
    Route::get('/category/{id}', [CategoryController::class, 'show']);
    Route::post('/category', [CategoryController::class, 'store']);
    Route::put('/category/{id}', [CategoryController::class, 'update']);
    Route::delete('/category/{id}', [CategoryController::class, 'destroy']);

    /**
     * Brand API Routes
     */
    Route::get('/brand', [BrandController::class, 'index']);
    Route::get('/brand/{id}', [BrandController::class, 'show']);
    Route::post('/brand', [BrandController::class, 'store']);
    Route::put('/brand/{id}', [BrandController::class, 'update']);
    Route::delete('/brand/{id}', [BrandController::class, 'destroy']);

    /**
     * Supplier API Routes
     */
    Route::get('/supplier', [SupplierController::class, 'index']);
    Route::get('/supplier/{id}', [SupplierController::class, 'show']);
    Route::post('/supplier', [SupplierController::class, 'store']);
    Route::put('/supplier/{id}', [SupplierController::class, 'update']);
    Route::delete('/supplier/{id}', [SupplierController::class, 'destroy']);

    /**
     * Review API Routes
     */
    Route::get('/review', [ReviewController::class, 'index']);
    Route::get('/review/{id}', [ReviewController::class, 'show']);
    Route::post('/review', [ReviewController::class, 'store']);
    Route::put('/review/{id}', [ReviewController::class, 'update']);
    Route::delete('/review/{id}', [ReviewController::class, 'destroy']);
});
