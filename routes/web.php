<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', [App\Http\Controllers\AnalyticsController::class, 'index']);
Route::get('/chat',[HomeController::class,'index']);Route::post('/chat',[HomeController::class,'chat']);

Route::get('/chat/history', [HomeController::class, 'loadHistory']);
Route::get('/chat/history/{id}', [HomeController::class, 'loadMessages']);

Route::delete('/chat/delete/{id}', [HomeController::class, 'deleteChat']);

Route::get('/youtube', [App\Http\Controllers\YouTubeController::class, 'index']);
Route::post('/youtube/summarize', [App\Http\Controllers\YouTubeController::class, 'summarize']);
Route::get('/analytics', [App\Http\Controllers\AnalyticsController::class, 'index']);
Route::post('/analytics/chat', [App\Http\Controllers\AnalyticsController::class, 'chat']);


Route::get('/analytics/history', [App\Http\Controllers\AnalyticsController::class, 'loadHistory']);
Route::get('/analytics/history/{id}', [App\Http\Controllers\AnalyticsController::class, 'loadMessages']);
Route::delete('/analytics/delete/{id}', [App\Http\Controllers\AnalyticsController::class, 'deleteChat']);


Route::put('/chat/rename/{id}', [App\Http\Controllers\HomeController::class, 'renameChat']);
Route::put('/analytics/rename/{id}', [App\Http\Controllers\AnalyticsController::class, 'renameChat']);
Route::get('/analytics/download/{filename}', [App\Http\Controllers\AnalyticsController::class, 'downloadReport']);

