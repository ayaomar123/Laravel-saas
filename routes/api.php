<?php

use App\Http\Middleware\TenantResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

Route::middleware(['api'])->group(function () {
    Route::middleware([TenantResolver::class])->group(function () {
        // Authentication routes
        Route::prefix('auth')->group(function () {
            Route::post('register', [AuthController::class, 'register']);
            Route::post('login', [AuthController::class, 'login']);
            Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
            Route::get('me', [AuthController::class, 'me'])->middleware('auth:sanctum');
        });

        // Tasks routes - require authentication
        Route::middleware('auth:sanctum')->group(function () {
            Route::apiResource('tasks', TaskController::class);
        });
    });
});