<?php

use App\Http\Controllers\Api\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Webhooks
Route::post('/webhook/github', [WebhookController::class, 'github']);
Route::get('/webhook/github', [WebhookController::class, 'github']);
// Fin webhooks
