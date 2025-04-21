<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/token', [AuthController::class, 'store_token']);
Route::post('/users', [UserController::class, 'store']);
Route::prefix('universities')->group(function () {
    Route::get('', [UniversityController::class, 'index']);
    Route::get('/{university}', [UniversityController::class, 'show']);
});

Route::middleware('auth:api')->group(function () {
    Route::prefix('users')->group(function () {
        Route::get('', [UserController::class, 'show']);
        Route::post('/update', [UserController::class, 'update']);
    });
    Route::prefix('universities')->group(function () {
        Route::post('', [UniversityController::class, 'store']);
        Route::prefix('/{university}')->group(function () {
            Route::post('', [UniversityController::class, 'update']);
            Route::delete('', [UniversityController::class, 'destroy']);
            Route::prefix('/careers')->group(function () {
                Route::post('', [CareerController::class, 'store']);
            });
        });
    });
});
