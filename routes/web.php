<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', [App\Http\Controllers\AnalyticsController::class, 'index']);
Route::get('/chat', function() { return redirect('/analytics'); });
Route::post('/chat', [App\Http\Controllers\AnalyticsController::class, 'chat']);

Route::get('/chat/history', [App\Http\Controllers\AnalyticsController::class, 'loadHistory']);
Route::get('/chat/history/{id}', [App\Http\Controllers\AnalyticsController::class, 'loadMessages']);

Route::delete('/chat/delete/{id}', [App\Http\Controllers\AnalyticsController::class, 'deleteChat']);

Route::get('/youtube', [App\Http\Controllers\YouTubeController::class, 'index']);
Route::post('/youtube/summarize', [App\Http\Controllers\YouTubeController::class, 'summarize']);
Route::get('/analytics', [App\Http\Controllers\AnalyticsController::class, 'index']);
Route::post('/analytics/chat', [App\Http\Controllers\AnalyticsController::class, 'chat']);

Route::get('/analytics/history', [App\Http\Controllers\AnalyticsController::class, 'loadHistory']);
Route::get('/analytics/history/{id}', [App\Http\Controllers\AnalyticsController::class, 'loadMessages']);
Route::delete('/analytics/delete/{id}', [App\Http\Controllers\AnalyticsController::class, 'deleteChat']);

Route::put('/chat/rename/{id}', [App\Http\Controllers\AnalyticsController::class, 'renameChat']);
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
