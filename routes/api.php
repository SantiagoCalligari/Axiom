<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/token', [AuthController::class, 'store_token']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/universities/{university}', [UniversityController::class, 'show']);
Route::get('/universities', [UniversityController::class, 'index']);

Route::middleware('auth:api')->group(function () {
    Route::prefix('users')->group(function() {
        Route::get('', [UserController::class, 'show']);
        Route::post('/update', [UserController::class, 'update']);
    });
    Route::prefix('universities')->group(function() {
        Route::post('', [UniversityController::class, 'store']);
        Route::post('/{university}', [UniversityController::class, 'update']);
        Route::delete('/{university}', [UniversityController::class, 'destroy']);
    });
});
