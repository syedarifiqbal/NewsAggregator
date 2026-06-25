<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\UserPreferenceController;
use Illuminate\Support\Facades\Route;

Route::get('/news', [NewsController::class, 'index'])->middleware('throttle:60,1');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user/preferences', [UserPreferenceController::class, 'show']);
    Route::put('/user/preferences', [UserPreferenceController::class, 'update']);
    Route::get('/user/feed', [UserPreferenceController::class, 'feed']);
});
