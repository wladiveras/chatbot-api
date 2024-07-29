<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FlowController;
use App\Http\Controllers\MessengerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth Service
Route::prefix('/auth')
    ->group(function () {
        Route::get('/redirect/{provider}', [AuthController::class, 'redirectToProvider']);

        // Sanctum login with Magic Link passwordless
        Route::post('/sign-in', [AuthController::class, 'login']);
        Route::post('/magic-link/{token}', [AuthController::class, 'magicLink']);
        Route::get('/user', [AuthController::class, 'user'])->middleware(['auth:sanctum']);
        Route::delete('/sign-out', [AuthController::class, 'logout'])->middleware(['auth:sanctum']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken'])->middleware(['auth:sanctum']);

        // Socialite Login
        Route::get('/redirect/{provider}', [AuthController::class, 'redirectToProvider']);
        Route::get('/callback/{provider}', [AuthController::class, 'callbackWithProvider']);

        // Supabase Login
        Route::post('/supabase-sign-in', [AuthController::class, 'loginSupabase']);
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

Route::prefix('/flow')
    ->middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/', [FlowController::class, 'index']);
        Route::post('/', [FlowController::class, 'create']);
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
        Route::post('/{connection}/status', [MessengerController::class, 'status']);
        Route::get('/{connection}/connect', [MessengerController::class, 'connect']);
        Route::delete('/{connection}/delete', [MessengerController::class, 'delete']);
        Route::delete('/{connection}/disconnect', [MessengerController::class, 'disconnect']);
        Route::post('/send-message', [MessengerController::class, 'sendMessage']);
    });

Route::post('/integration/{integration}/callback', [MessengerController::class, 'callback']);

// More Services
