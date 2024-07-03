<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;

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

Route::prefix('/payment/{gateway}')
//->middleware(['first', 'second'])
->group(function () {
    Route::post('/pay', [PaymentController::class, 'pay']);
    Route::post('/callback/{id}', [PaymentController::class, 'checkPayment']);
});
