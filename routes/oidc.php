<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

/*
|--------------------------------------------------------------------------
| OIDC Routes
|--------------------------------------------------------------------------
|
| Here is where you can register OIDC routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "oidc" middleware group. Make something great!
|
*/

Route::get('/oidc', function (Request $request) {
    return response()->json([
        'message' => 'OIDC route is working!',
    ]);
});


