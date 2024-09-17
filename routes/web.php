<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => env('APP_NAME') . ' is running'
    ], 200);
});

