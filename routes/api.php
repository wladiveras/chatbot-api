<?php

use App\Http\Controllers\MessengerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('/user')
    //->middleware(['first', 'second'])
    ->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('{id}', [UserController::class, 'show']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

Route::prefix('/auth')
    ->group(function () {
        Route::post('/token', [AuthController::class, 'auth']);
        Route::post('/return/token', [AuthController::class, 'token'])->middleware(['auth:sanctum']);
        Route::post('/user', [AuthController::class, 'user'])->middleware(['auth:sanctum']);
    });


Route::prefix('/payment/{gateway}')
    //->middleware(['first', 'second'])
    ->group(function () {
        Route::post('/pay', [PaymentController::class, 'pay']);
        Route::post('/callback/{id}', [PaymentController::class, 'checkPayment']);
    });

Route::prefix('/integration/{integration}')
    //->middleware(['first', 'second'])
    ->group(function () {
        Route::post('/create-connection', [MessengerController::class, 'createConnection']);
        Route::delete('/{connection}/status', [MessengerController::class, 'status']);
        Route::get('/{connection}/connect', [MessengerController::class, 'connect']);
        Route::delete('/{connection}/delete', [MessengerController::class, 'delete']);
        Route::delete('/{connection}/disconnect', [MessengerController::class, 'disconnect']);
        Route::post('/send-message', [MessengerController::class, 'sendMessage']);
        Route::post('/callback', [MessengerController::class, 'callback']);
    });
