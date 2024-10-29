<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserEventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function () {

    Route::middleware(['jwt.auth'])->group(function () {
        Route::prefix('users/events')->group(function() {
            Route::get('/', [UserEventController::class, 'index']);
            Route::post('/', [UserEventController::class, 'store']);
            Route::get('/{event}', [UserEventController::class, 'show']);
            Route::put('/{event}', [UserEventController::class, 'update']);
            Route::delete('/{event}', [UserEventController::class, 'destroy']);
        });
    });

    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);

});

