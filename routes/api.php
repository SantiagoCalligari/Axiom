<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/token', [AuthController::class, 'store_token']);

Route::middleware('auth:api')->group(function () {
    Route::post('/users', [UserController::class, 'store'])->can('store users');
});
