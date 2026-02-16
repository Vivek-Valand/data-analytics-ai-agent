<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::middleware('guest')->group(function () {
    Route::get('/', [App\Http\Controllers\Auth\AuthController::class, 'showLogin']);
    Route::get('/login', [App\Http\Controllers\Auth\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\AuthController::class, 'login']);
    Route::get('/register', [App\Http\Controllers\Auth\AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/verify-email', [App\Http\Controllers\Auth\AuthController::class, 'showVerifyEmail'])->name('verification.notice');
    Route::post('/email/verification-notification', [App\Http\Controllers\Auth\AuthController::class, 'resendVerification'])->middleware('throttle:6,1')->name('verification.send');
    Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\Auth\AuthController::class, 'verify'])->middleware('signed')->name('verification.verify');

    Route::post('/logout', [App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout');

    Route::middleware('verified')->group(function () {
        Route::get('/', [App\Http\Controllers\AnalyticsController::class, 'index']);
        Route::get('/analytics', [App\Http\Controllers\AnalyticsController::class, 'index']);
        Route::post('/analytics/chat', [App\Http\Controllers\AnalyticsController::class, 'chat']);
        Route::get('/analytics/history', [App\Http\Controllers\AnalyticsController::class, 'loadHistory']);
        Route::get('/analytics/history/{id}', [App\Http\Controllers\AnalyticsController::class, 'loadMessages']);
        Route::delete('/analytics/delete/{id}', [App\Http\Controllers\AnalyticsController::class, 'deleteChat']);
        Route::put('/analytics/rename/{id}', [App\Http\Controllers\AnalyticsController::class, 'renameChat']);
        Route::get('/analytics/download/{filename}', [App\Http\Controllers\AnalyticsController::class, 'downloadReport']);

        Route::get('/db-config', [\App\Http\Controllers\DatabaseConfigController::class, 'index']);

        Route::prefix('api/db-config')->group(function () {
            Route::post('/test', [\App\Http\Controllers\DatabaseConfigController::class, 'testConnection']);
            Route::post('/save', [\App\Http\Controllers\DatabaseConfigController::class, 'storeDatabaseConfig']);
            Route::post('/upload-sql', [\App\Http\Controllers\DatabaseConfigController::class, 'storeSqlFile']);
            Route::get('/list', [\App\Http\Controllers\DatabaseConfigController::class, 'getConfigs']);
            Route::get('/{id}', [\App\Http\Controllers\DatabaseConfigController::class, 'show']);
            Route::put('/{id}', [\App\Http\Controllers\DatabaseConfigController::class, 'update']);
            Route::delete('/{type}/{id}', [\App\Http\Controllers\DatabaseConfigController::class, 'destroy']);
        });
    });
});
