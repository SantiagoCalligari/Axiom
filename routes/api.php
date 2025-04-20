<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/token', [AuthController::class, 'store_token']);
Route::post('/users', [UserController::class, 'store']);

Route::middleware('auth:api')->group(function () {
    Route::get('/users', [UserController::class, 'show']);
    Route::post('/users/update', [UserController::class, 'update']);
});
