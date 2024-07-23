<?php

use App\Http\Controllers\MessengerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Route;

// Auth Service
Route::prefix('/auth')
    ->group(function () {
        Route::get('/user', [AuthController::class, 'user'])->middleware(['auth:sanctum']);
        Route::post('/sign-in', [AuthController::class, 'auth']);
        Route::post('/refresh-token', [AuthController::class, 'token'])->middleware(['auth:sanctum']);
    });

// User Service
Route::prefix('/user')
    ->middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('{id}', [UserController::class, 'show']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

// Payment Gateway service
Route::prefix('/payment/{gateway}')
    ->middleware(['auth:sanctum'])
    ->group(function () {
        Route::post('/pay', [PaymentController::class, 'pay']);
        Route::post('/callback/{id}', [PaymentController::class, 'checkPayment']);
    });

// Connection Service
Route::prefix('/integration/{integration}')
    ->middleware(['auth:sanctum'])
    ->group(function () {
        Route::post('/create-connection', [MessengerController::class, 'createConnection']);
        Route::delete('/{connection}/status', [MessengerController::class, 'status']);
        Route::get('/{connection}/connect', [MessengerController::class, 'connect']);
        Route::delete('/{connection}/delete', [MessengerController::class, 'delete']);
        Route::delete('/{connection}/disconnect', [MessengerController::class, 'disconnect']);
        Route::post('/send-message', [MessengerController::class, 'sendMessage']);
    });

Route::post('/integration/{integration}/callback', [MessengerController::class, 'callback']);

// More Services
