<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shop\UserController;

/*
|--------------------------------------------------------------------------
| Openapi Common Routes
|--------------------------------------------------------------------------
| 挂载前缀 openapi，最终路径 /openapi/commons/...
*/

Route::middleware('shopInit')->group(function () {
    Route::prefix('commons')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('login', [UserController::class, 'login']);
        });
    });

    Route::prefix('users')->group(function () {
        Route::post('check-email', [UserController::class, 'checkEmail']);
    });
});
