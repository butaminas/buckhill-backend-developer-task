<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {

    Route::prefix('admin')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/logout', [AuthController::class, 'logout']);

        Route::post('/create', [AdminController::class, 'store']);
        Route::get('/user-listing', [AdminController::class, 'index']);
        Route::put('/user-edit/{uuid}', [AdminController::class, 'update']);
        Route::delete('/user-delete/{uuid}', [AdminController::class, 'destroy']);
    });


});
