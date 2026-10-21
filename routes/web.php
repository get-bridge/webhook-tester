<?php

use App\Http\Controllers\IndexController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Route;

// Patterns
Route::pattern('requestId', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
Route::pattern('tokenId', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
Route::pattern('statusCode', '[1-5][0-9][0-9]');
Route::pattern('any', '.*');

// SPA view
Route::get('/', IndexController::class);

Route::group(['middleware' => ['api']], function () {
    // Requests
    Route::any('{tokenId}/{statusCode?}', [RequestController::class, 'create']);
    Route::any('{tokenId}/{any}', [RequestController::class, 'create']);
    Route::get('token/{tokenId}/requests', [RequestController::class, 'all']);
    Route::get('token/{tokenId}/request/{requestId}', [RequestController::class, 'find']);
    Route::get('token/{tokenId}/request/{requestId}/raw', [RequestController::class, 'raw']);
    Route::delete('token/{tokenId}/request/{requestId}', [RequestController::class, 'delete']);
    Route::delete('token/{tokenId}/request', [RequestController::class, 'deleteByToken']);

    // Tokens
    Route::get('token/{tokenId}', [TokenController::class, 'find']);
    Route::post('token', [TokenController::class, 'create']);
    Route::delete('token/{tokenId}', [TokenController::class, 'delete']);
    Route::put('token/{tokenId}', [TokenController::class, 'update']);
    Route::put('token/{tokenId}/cors/toggle', [TokenController::class, 'toggleCors']);
});
